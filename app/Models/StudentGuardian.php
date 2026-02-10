<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentGuardian extends Model
{
    use HasFactory;

    protected $table = 'student_guardians';

    protected $fillable = [
        'student_id',
        'guardian_id',
        'relation',
        'is_primary',
        'is_fee_payer',
    ];
}

