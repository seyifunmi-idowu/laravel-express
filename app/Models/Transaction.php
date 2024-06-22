<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transactions';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'transaction_type',
        'transaction_status',
        'amount',
        'currency',
        'reference',
        'pssp',
        'payment_channel',
        'description',
        'wallet_id',
        'object_id',
        'object_class',
        'payment_category',
        'pssp_meta_data'
    ];

    protected $hidden = [
        'user_id',
        'deleted_at',
        'state',
        'pssp',
        'payment_channel',
        'wallet_id',
        'object_id',
        'object_class',
        'payment_category',
        'pssp_meta_data',
        'updated_at',
        'created_by_id',
        'deleted_by_id',
        'updated_by_id',
    ];

    protected $casts = [
        'pssp_meta_data' => 'array',
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
