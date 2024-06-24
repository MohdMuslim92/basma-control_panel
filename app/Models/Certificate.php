<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Notification;

class Certificate extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'admin_id',
        'name',
        'language',
        'code',
        'status',
        'approve1',
        'approve2',
        'approve3',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function approvedByFirst()
    {
        return $this->belongsTo(User::class, 'approve1');
    }

    public function approvedBySecond()
    {
        return $this->belongsTo(User::class, 'approve2');
    }

    public function approvedByThird()
    {
        return $this->belongsTo(User::class, 'approve3');
    }
    
    public function notifications()
    {
        return $this->belongsToMany(Notification::class)
        ->withPivot('read')
        ->withTimestamps();
    }
}
