<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountUsage extends Model
{
    
    protected $fillable = [
        'discount_id', 'times_used', 'user_id'
    ];
    
}
