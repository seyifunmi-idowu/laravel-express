<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;
use Illuminate\Database\Eloquent\Builder;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'customer';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'customer_type',
        'business_name',
        'business_address',
        'business_category',
        'delivery_volume',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->whereNull('deleted_at');
    }

    public function getDisplayNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    protected static function booted()
    {
        static::addGlobalScope('withNonDeletedUsers', function (Builder $builder) {
            $builder->whereHas('user', function (Builder $query) {
                $query->whereNull('deleted_at');
            });
        });
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
