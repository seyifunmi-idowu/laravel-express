<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Helpers\StaticFunction;

class Rider extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rider';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 
        'user_id',
        'vehicle_id',
        'vehicle_type',
        'vehicle_make',
        'vehicle_model',
        'vehicle_plate_number',
        'vehicle_color',
        'rider_info',
        'city',
        'avatar_url',
        'status',
        'status_updates',
        'operation_locations',
        'on_duty',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function riderDocuments()
    {
        return $this->hasMany(RiderDocument::class, 'rider_id');
    }

    public function riderOrders()
    {
        return $this->hasMany(Order::class, 'rider_id');
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

    public function getActiveOrder()
    {
        return $this->riderOrders()->where('status', 'active');
    }

    public function getDisplayNameAttribute()
    {
        return $this->user->first_name . ' ' . $this->user->last_name;
    }

    public function getRiderStatus()
    {
        if (in_array($this->status, ["APPROVED", "DISAPPROVED", "SUSPENDED"])) {
            return $this->status;
        }

        $documents = $this->riderDocument;
        return $documents->count() > 0 ? "PENDING_APPROVAL" : $this->status;
    }


    public function vehiclePhotos()
    {
        return $this->riderDocuments()
            ->where('type', 'vehicle_photo')
            ->pluck('file_url');
    }

    public function kycVerified()
    {
        $documentTypesToCheck = [
            "vehicle_photo",
            "passport_photo",
            "government_id",
            "guarantor_letter",
            "address_verification",
        ];

        foreach ($documentTypesToCheck as $documentType) {
            $riderDocuments = $this->riderDocuments()
                ->where('type', $documentType)
                ->get();

            if ($riderDocuments->isEmpty()) {
                return false;
            }

            $allVerified = $riderDocuments->every(function ($document) {
                return $document->verified;
            });

            if (!$allVerified) {
                return false;
            }
        }

        return true;
    }


}
