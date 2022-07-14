<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BilledShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'description', 'duration', 'hourly_rate', 'date'
    ];

    protected $casts = [
        'date' => 'date'
      ];
}