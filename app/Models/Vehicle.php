<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicle';

    protected $fillable = [
        'id', 
        'id',
        'name',
        'status',
        'note',
        'start_date',
        'end_date',
        'file_url',
        'base_fare',
        'km_5_below_fare',
        'km_5_above_fare',
        'price_per_minute',
    ];

    protected $casts = [
        'id' => 'string',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = StaticFunction::generate_id();
            }
        });
    }

}
