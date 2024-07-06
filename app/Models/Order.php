<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'customer_id',
        'rider_id',
        'business_id',
        'vehicle_id',
        'order_id',
        'chat_id',
        'status',
        'payment_method',
        'payment_by',
        'order_by',
        'paid',
        'pickup_number',
        'pickup_contact_name',
        'pickup_location',
        'pickup_name',
        'pickup_location_longitude',
        'pickup_location_latitude',
        'delivery_number',
        'delivery_contact_name',
        'delivery_location',
        'delivery_name',
        'delivery_location_longitude',
        'delivery_location_latitude',
        'delivery_time',
        'order_stop_overs_meta_data',
        'total_amount',
        'fele_amount',
        'paid_fele',
        'tip_amount',
        'order_meta_data',
        'distance',
        'duration',
    ];

    protected $casts = [
        'order_stop_overs_meta_data' => 'array',
        'order_meta_data' => 'array',
        'paid' => 'boolean',
        'paid_fele' => 'boolean',
        'delivery_time' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function rider()
    {
        return $this->belongsTo(Rider::class, 'rider_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function orderTimeline()
    {
        return $this->hasMany(OrderTimeline::class);
    }

    public function order_timeline()
    {
        return $this->hasMany(OrderTimeline::class);
    }


    public function isCustomerOrder()
    {
        return $this->order_by === 'CUSTOMER';
    }

    public function getPickUpTime()
    {
        $orderTimeline = $this->orderTimeline()
            ->where('status', 'RIDER_PICKED_UP_ORDER')
            ->first();
        
        return $orderTimeline ? $orderTimeline->created_at->format('Y-m-d H:i:s') : null;
    }

    // Method to get delivery time
    public function getDeliveryTime()
    {
        $orderTimeline = $this->orderTimeline()
            ->where('status', 'ORDER_DELIVERED')
            ->first();
        
        return $orderTimeline ? $orderTimeline->created_at->format('Y-m-d H:i:s') : null;
    }

    public function getOrderProgress()
    {
        $status_mapper = [
            "PENDING"=> 2,
            "PROCESSING_ORDER"=> 4,
            "PENDING_RIDER_CONFIRMATION"=> 6,
            "RIDER_ACCEPTED_ORDER"=> 10,
            "RIDER_AT_PICK_UP"=> 15,
            "RIDER_PICKED_UP_ORDER"=> 20,
            "ORDER_ARRIVED"=> 80,
            "ORDER_DELIVERED"=> 95,
            "ORDER_COMPLETED"=> 100,
            "ORDER_CANCELLED"=> 0
        ];
        
        return $status_mapper[$this->status];
    }

    public function getStatusDisplay()
    {
        return ucwords(str_replace('_', ' ', $this->status));
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = StaticFunction::generate_id();
            }
            if (empty($model->state)) {
                $model->state = 'ACTIVE';
            }
        });
    }

}
