<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class Rider extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rider';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'vehicle_id',
        'vehicle_type',
        'vehicle_make',
        'vehicle_model',
        'vehicle_plate_number',
        'vehicle_color',
        'rider_info',
        'city',
        'avatar_url',
        'status',
        'status_updates',
        'operation_locations',
        'on_duty',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
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

    public function getActiveOrder()
    {
        return $this->hasMany(Order::class)->where('status', 'active');
    }


}
