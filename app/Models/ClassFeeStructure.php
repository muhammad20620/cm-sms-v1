<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassFeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
        'class_id',
        'section_id',
        'title',
        'amount',
    ];
}

