<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class FavoriteRider extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'favourite_rider';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'rider_id',
        'customer_id',
    ];

    public function rider()
    {
        return $this->belongsTo(Rider::class, 'rider_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
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
