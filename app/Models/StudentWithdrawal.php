<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentWithdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'enrollment_id',
        'class_id',
        'section_id',
        'session_id',
        'admission_no',
        'enrollment_no',
        'father_name',
        'father_cnic',
        'slc_no',
        'withdrawal_date',
        'slc_issue_date',
        'reason',
        'remarks',
        'dues_cleared',
        'created_by',
    ];
}

