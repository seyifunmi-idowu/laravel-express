<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class RiderDocument extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rider_document';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'type',
        'number',
        'file_url',
        'rider_id',
        'verified',
    ];

    public function rider()
    {
        return $this->belongsTo(Rider::class, 'rider_id');
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
