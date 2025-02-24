<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'min_cart_total', 'applicable_products',
        'applicable_categories', 'expires_at', 'max_uses', 'per_user_limit',
        'first_time_only', 'active_from', 'active_to', "stackable"
    ];
    
    protected $casts = [
        'applicable_products' => 'array',
        'applicable_categories' => 'array',
        'expires_at' => 'datetime',
        'active_from' => 'datetime',
        'active_to' => 'datetime',
        'stackable' => 'boolean',
    ];
}
