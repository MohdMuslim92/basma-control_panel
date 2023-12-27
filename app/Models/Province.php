<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'state_id'];

    // Relationships
    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
