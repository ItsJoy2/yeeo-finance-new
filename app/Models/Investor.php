<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'amount',
        'expected_return',
        'referral_bonus',
        'return_type',
        'duration',
        'start_date',
        'end_date',
        'next_return_date',
        'received_count',
        'status'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'next_return_date' => 'date',
        'amount' => 'decimal:8',
        'expected_return' => 'decimal:8',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
