<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'stripe_plan_id',
        'price',
        'trial_days',
        'features',
        'active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'features' => 'json',
        'active' => 'boolean', // Retaining this good practice from existing file
        'price' => 'decimal:2', // Retaining this good practice
        'trial_days' => 'integer', // Retaining this good practice
    ];
}
