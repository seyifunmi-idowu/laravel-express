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


    public function isCustomerOrder()
    {
        return $this->order_by === 'CUSTOMER';
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
