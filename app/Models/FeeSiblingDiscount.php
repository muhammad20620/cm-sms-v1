<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSiblingDiscount extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
        'guardian_id',
        'basis',
        'min_children',
        'mode',
        'value',
        'is_active',
        'note',
    ];
}

