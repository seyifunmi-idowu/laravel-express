<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class OrderTimeline extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_timeline';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'order_id',
        'status',
        'proof_url',
        'reason',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    protected $hidden = [
        'deleted_at',
        'state',
        'updated_at',
        'created_by_id',
        'deleted_by_id',
        'updated_by_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusIcon()
    {
        $status_mapper = [
            "PENDING"=> "money-coins",
            "PROCESSING_ORDER"=> "cart",
            "PENDING_RIDER_CONFIRMATION"=>  "bell-55",
            "RIDER_ACCEPTED_ORDER"=> "check-bold",
            "RIDER_AT_PICK_UP"=> "pin-3",
            "RIDER_PICKED_UP_ORDER"=>"delivery-fast",
            "ORDER_ARRIVED"=>"pin-3",
            "ORDER_DELIVERED"=> "bag-17",
            "ORDER_COMPLETED"=> "bag-17",
            "ORDER_CANCELLED"=>"fat-remove",
        ];
        
        return $status_mapper[$this->status];
    }

    public function getStatusColour()
    {
        $status_mapper = [
            "PENDING"=> "info",
            "PROCESSING_ORDER"=> "warning",
            "PENDING_RIDER_CONFIRMATION"=>  "dark",
            "RIDER_ACCEPTED_ORDER"=> "success",
            "RIDER_AT_PICK_UP"=> "warning",
            "RIDER_PICKED_UP_ORDER"=>"info",
            "ORDER_ARRIVED"=>"success",
            "ORDER_DELIVERED"=> "dark",
            "ORDER_COMPLETED"=> "primary",
            "ORDER_CANCELLED"=>"danger",
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
