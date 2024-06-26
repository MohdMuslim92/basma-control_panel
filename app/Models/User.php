<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use \App\Models\Role;
use App\Models\State;
use App\Models\Province;
use App\Models\UserStatus;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'state_id',
        'province_id',
        'address',
        'phone',
        'dob',
        'educationLevel',
        'specialization',
        'skills',
        'alreadyVolunteering',
        'organizationName',
        'volunteeringStartDate',
        'volunteeringEndDate',
        'monthlyShare',
        'meetingDay',
        'role_id',
        'user_status_id',
        'admin_mail',
        'last_pay',
        'last_seen_at',
        'terms',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }

    public function adminData()
    {
        return $this->belongsTo(User::class, 'admin_mail', 'email');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    // Define relationship to UserStatus
    public function userStatus()
    {
        return $this->belongsTo(UserStatus::class, 'user_status_id');
    }

    // Define relationship to Certificate
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function notifications()
    {
        return $this->belongsToMany(Notification::class)
        ->withPivot('read')
        ->withTimestamps();
    }

}
