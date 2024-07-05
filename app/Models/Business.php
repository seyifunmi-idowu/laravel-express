<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;
use Illuminate\Database\Eloquent\Builder;

class Business extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'business';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'business_type',
        'business_name',
        'business_address',
        'business_category',
        'delivery_volume',
        'webhook_url',
        'e_secret_key',
    ];

    protected $casts = [
        'id' => 'string',
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

    protected static function booted()
    {
        static::addGlobalScope('withNonDeletedUsers', function (Builder $builder) {
            $builder->whereHas('user', function (Builder $query) {
                $query->whereNull('deleted_at');
            });
        });
    }

    public function getDisplayNameAttribute()
    {
        return $this->business_name ? $this->business_name : $this->user->display_name;
    }




}
