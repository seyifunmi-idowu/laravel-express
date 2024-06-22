<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\StaticFunction;
use App\Services\NotificationService;


class Wallet extends Model
{
    use HasFactory;
    protected $table = 'wallet_wallet';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'balance',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function deposit($amount)
    {
        $this->balance += $amount;
        $this->save();

        $title = "Wallet credited";
        $message = "₦ " . number_format($amount, 2) . " has been credited into your wallet.";
        $notificationService = new NotificationService();
        $notificationService->sendPushNotification($this->user, $title, $message);
    }

    public function withdraw($amount, $deductNegative = false)
    {
        if ($deductNegative || $this->balance >= $amount) {
            $this->balance -= $amount;
            $this->save();

            $title = "Wallet withdrawal";
            $message = "₦ " . number_format($amount, 2) . " has been debited from your wallet.";
            $notificationService = new NotificationService();
            $notificationService->sendPushNotification($this->user, $title, $message);
    
            if ($this->balance < 0) {
                $title = "Low balance";
                $message = "Your wallet has hit rock bottom with ₦ -" . number_format(abs($this->balance), 2) . ". Kindly fund wallet.";
                $notificationService = new NotificationService();
                $notificationService->sendPushNotification($this->user, $title, $message);
            }
        } else {
            throw new \Exception("Insufficient balance for withdrawal.");
        }
    }


}
