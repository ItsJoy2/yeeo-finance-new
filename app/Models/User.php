<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Investor;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

// class User extends Authenticatable implements MustVerifyEmail
class User extends Authenticatable
{
    use HasFactory,HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'mobile',
        'funding_wallet',
        'spot_wallet',
        'token_wallet',
        'refer_by',
        'refer_code',
        'is_active',
        'last_activated_at',
        'is_block',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];
    protected $casts = [
        'last_activated_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function referredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refer_by');
    }

    public function hasReceivedActivationBonus(): bool
    {
        return $this->last_activated_at !== null;
    }
    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'refer_by');
    }

    public function totalTeamMembersCount(int $level = 1): int
    {
        $count = $this->referrals()->count();

        foreach ($this->referrals as $referral) {
            $count += $referral->totalTeamMembersCount($level + 1);
        }

        return $count;
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->refer_code = self::generateReferCode();
        });
    }

    public static function generateReferCode(): string
    {
        do {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
        } while (self::where('refer_code', $code)->exists());

        return $code;
    }
    public function investors()
    {
        return $this->hasMany(Investor::class);
    }


}


