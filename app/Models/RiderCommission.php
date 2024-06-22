<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class RiderCommission extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rider_commission';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'rider_id',
        'commission_id',
    ];

    public function rider()
    {
        return $this->belongsTo(Rider::class, 'rider_id');
    }

    public function commission()
    {
        return $this->belongsTo(Commission::class, 'commission_id');
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
