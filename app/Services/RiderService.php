<?php

namespace App\Services;

use App\Models\Rider;
use App\Models\RiderDocument;
use Illuminate\Database\Eloquent\Collection;
use App\Helpers\StaticFunction;
use App\Exceptions\CustomAPIException;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\RetrieveRiderResource;
use App\Models\User;
use App\Models\Order;
use App\Models\Vehicle;
use App\Helpers\S3Uploader;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiderService
{
    protected UserService $userService;
    protected AuthService $authService;

    public function __construct(
        UserService $userService,
        AuthService $authService
    )
    {
        $this->userService = $userService;
        $this->authService = $authService;
    }

    public function createRider($user, $data){
        $data['user_id'] = $user->id;
        $rider = Rider::create($data);
        return $rider;
    }

    public function getRider($user)
    {
        $rider = Rider::where('user_id', $user->id);
        if (!$rider) {
            throw new CustomAPIException('Rider not found', 404);
        }
        return $rider->first();
    }
   
    public function registerRider($request)
    {
        $email = $request->email;
        $phone_number = $request->phone_number;
        $fullname = $request->fullname;
        $password = $request->password;
        $receive_email_promotions = $request->receive_email_promotions;
        $city = $request->city;
        $one_signal_id = $request->one_signal_id;
        $referral_code = $request->referral_code;
        $first_name = explode(" ", $fullname)[0];
        $last_name = explode(" ", $fullname)[1];
        
        $user = $this->userService->createUser([
            'email' => $email,
            'phone_number' => $phone_number,
            'user_type' => "RIDER",
            'first_name' => $first_name,
            'last_name' => $last_name,
            'password' => $password,
            'receive_email_promotions' => $receive_email_promotions,
            'one_signal_id' => $one_signal_id,
            'referral_code' => $referral_code,
        ]);
        // $user->assignRole('rider');
        $this->createRider(
            $user, 
            [
                'city' => $city,
            ]
        );
        
        $this->authService->initiatePhoneVerification($phone_number);
        // send to rider
        NotificationService::sendEmailMessage(
            [$user->email], 
            "Welcome", 
            ["display_name"=> $user->getDisplayNameAttribute()],
            "emails.otp_template"
        );
        // send to admin
        $redirect_url = config('constants.BASE_URL');
        NotificationService::sendEmailMessage(
            config('constants.ADMIN_EMAILS'), 
            "New rider signup", 
            [
                "display_name"=> $user->getDisplayNameAttribute(),
                "redirect_url"=> $redirect_url,
            ],
            "emails.otp_template"
        );
        return array();
    }

    public function login($request)
    {
        $user = $this->userService->authenticateUser($request->get('email'), $request->get('password'));            
        $token = Auth::login($user);
        $rider = $this->getRider($user);
        return [
            "rider" => new RetrieveRiderResource($rider),
            "token" => ["access" => $token],
        ];
    }
    
    public static function getRiderPerformance($request, $user)
    {
        $period = $request->query('period', 'today');
        $endDate = Carbon::now();
        
        switch (strtolower($period)) {
            case 'yesterday':
                $endDate = Carbon::create($endDate->year, $endDate->month, $endDate->day, 0, 0, 0);
                $startDate = $endDate->copy()->subDay();
                break;
            case 'week':
                $startDate = $endDate->copy()->subWeek();
                break;
            case 'month':
                $startDate = $endDate->copy()->subDays(30);
                break;
            default: // today
                $startDate = Carbon::create($endDate->year, $endDate->month, $endDate->day);
                break;
        }

        $orders = Order::where('rider_id', $user->id)
            ->where('status', 'ORDER_COMPLETED')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalAmountSum = $orders->sum('total_amount') ?? 0.0;
        $feleAmountSum = $orders->sum('fele_amount') ?? 0.0;
        $totalEarning = $totalAmountSum - $feleAmountSum;
        $totalDelivery = $orders->count();

        $totalDuration = $orders->sum(function ($order) {
            return (int) $order->duration;
        });

        $avgDurationSeconds = $totalDelivery > 0 ? $totalDuration / $totalDelivery : 0;

        $response = [
            'total_delivery' => $totalDelivery,
            'earning' => round($totalEarning, 2),
            'hours_worked' => self::getTimeInWords($totalDuration),
            'avg_delivery_time' => self::getTimeInWords($avgDurationSeconds),
        ];

        return $response;
    }

    private static function getTimeInWords($seconds)
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        } else {
            return "{$minutes} mins";
        }
    }

    public function getRiderDocument($rider, $type = null)
    {
        $query = RiderDocument::where('rider_id', $rider->id);
        if ($type) {
            $query->where('type', $type);
        }
        return $query->get();
    }

    public function getRiderDocumentStatus($rider, $documentType)
    {
        $documents = $this->getRiderDocument($rider, $documentType);
        if ($documents->isEmpty()) {
            return ['status' => 'unverified', 'files' => []];
        }

        $allVerified = $documents->every(fn($doc) => $doc->verified);
        $fileUrls = $documents->pluck('file_url')->all();

        return [
            'status' => $allVerified ? 'verified' : 'unverified',
            'files' => $fileUrls,
        ];
    }

    public function setRiderDuty($user, $on_duty)
    {
        $rider = $this->getRider($user);
        $rider->on_duty = $on_duty;
        $rider->save();
        return true;
    }

    public function submitKyc($user, $data)
    {
        $vehicleId = $data['vehicle_id'];
        $vehicle = Vehicle::where('id', $vehicleId)->first();

        if (!$vehicle || $vehicle->status !== 'ACTIVE') {
            throw new CustomAPIException(
                'Vehicle not found',
                400
            );
        }

        $rider = $this->getRider($user);
        $rider->vehicle_make = $data['vehicle_make'] ?? $rider->vehicle_make;
        $rider->vehicle_model = $data['vehicle_model'] ?? $rider->vehicle_model;
        $rider->vehicle_color = $data['vehicle_color'] ?? $rider->vehicle_color;
        $rider->vehicle_plate_number = $data['vehicle_plate_number'] ?? $rider->vehicle_plate_number;
        $rider->vehicle_id = $vehicle->id;
        $rider->save();

        DB::transaction(function () use ($rider, $data) {
            $documentFields = [
                'vehicle_photo',
                'passport_photo',
                'government_id',
                'guarantor_letter',
                'address_verification',
                'driver_license',
                'insurance_certificate',
                'certificate_of_vehicle_registration',
                'authorization_letter'
            ];
    
            foreach ($documentFields as $field) {
                if ($data->hasFile($field)) {
                    $this->addRiderDocument($rider, $field, $data->file($field));
                }
            }
        });
    

        return $rider;
    }

    public function addRiderDocument($rider, $documentType, $file)
    {
        $s3Uploader = new S3Uploader("/rider_document/$documentType");
        $fileUrl = $s3Uploader->uploadFileObject($file, $file->getClientOriginalName());

        return RiderDocument::create([
            'rider_id' => $rider->id,
            'type' => $documentType,
            'file_url' => $fileUrl,
            'verified' => false,
        ]);
    }

    public function updateRiderVehicle($request, User $user)
    {
        $rider = $this->getRider($user);
        $data = $request->only(['vehicle_type', 'vehicle_make', 'vehicle_model', 'vehicle_plate_number']);

        if (!empty($data['vehicle_type'])) {
            $rider->vehicle_type = $data['vehicle_type'];
        }
        if (!empty($data['vehicle_make'])) {
            $rider->vehicle_make = $data['vehicle_make'];
        }
        if (!empty($data['vehicle_model'])) {
            $rider->vehicle_model = $data['vehicle_model'];
        }
        if (!empty($data['vehicle_plate_number'])) {
            $rider->vehicle_plate_number = $data['vehicle_plate_number'];
        }
        $rider->save();
        return $rider;
    }

}
