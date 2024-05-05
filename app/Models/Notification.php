<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Notification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'title',
        'message',
        'link',
        'data',
        'user_id',
        'read',
    ];

    protected $casts = [
        'data' => 'array',
    ];

     /**
     * The users that belong to the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
