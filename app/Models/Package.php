<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'category_id',
        'plan_name',
        'image',
        'min_investment',
        'max_investment',
        'return_type',
        'duration',
        'pnl_return',
        'pnl_bonus',
        'status'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function investors()
    {
        return $this->hasMany(Investor::class);
    }
}
