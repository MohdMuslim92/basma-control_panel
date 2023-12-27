<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orphan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'family_id',
        'birthdate',
        'educationLevel',
        'class',
        'notes',
        'insurance',
        'hasIllness',
        'illnesses',
        'user_created',
        'user_updated',
    ];

    // Relationships
    public function family()
    {
        return $this->belongsTo(Family::class);
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
