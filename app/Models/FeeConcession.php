<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeConcession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
        'scope_type',
        'student_id',
        'guardian_id',
        'mode',
        'value',
        'is_active',
        'note',
    ];
}

