<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Family extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'mother',
        'mother_birthdate',
        'state_id',
        'city_id',
        'address',
        'phone',
        'otherPhone',
        'sponsor',
        'educationLevel',
        'skills',
        'isSheWorking',
        'workDetails',
        'accommodationType',
        'rentingAmount',
        'gpsLocation',
        'project',
        'deserveSupport',
        'supportType',
        'support',
        'hasIllness',
        'illnesses',
        'notes',
        'latitude',
        'longitude',
        'user_created',
        'user_updated',
    ];

    // Relationships
    public function orphans()
    {
        return $this->hasMany(Orphan::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_created');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'user_updated');
    }
}
