<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class OrderTimeline extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'order_timeline';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'order_id',
        'status',
        'proof_url',
        'reason',
        'meta_data',
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
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
