<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class BankAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bank_account';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'account_number',
        'account_name',
        'bank_code',
        'bank_name',
        'recipient_code',
        'meta',
        'save_account',
    ];

    protected $casts = [
        'id' => 'string',
        'meta' => 'array',
        'save_account' => 'boolean',
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
