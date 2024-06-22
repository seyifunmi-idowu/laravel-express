<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'address';

    protected $fillable = [
        'id', 
        'customer_id',
        'formatted_address',
        'longitude',
        'latitude',
        'state',
        'country',
        'landmark',
        'direction',
        'label',
        'meta_data',
        'save_address',
    ];

    protected $casts = [
        'id' => 'string',
        'meta_data' => 'array',
        'save_address' => 'boolean',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public $incrementing = false;
    protected $keyType = 'string';

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
