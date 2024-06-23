<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class Card extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'card';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'card_type',
        'card_auth',
        'last_4',
        'exp_month',
        'exp_year',
        'country_code',
        'brand',
        'first_name',
        'last_name',
        'reusable',
        'customer_code',
    ];

    protected $hidden = [
        'user_id',
        'deleted_at',
        'state',
        'last_name',
        'first_name',
        'card_auth',
        'reusable',
        'customer_code',
        'updated_at',
        'created_by_id',
        'deleted_by_id',
        'updated_by_id',
        'created_at',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
