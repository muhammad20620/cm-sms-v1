<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolApplication extends Model
{
    use HasFactory;

    protected $table = 'school_applications';

    protected $fillable = [
        'school_id',
        'parent_id',
        'guardian_id',
        'student_id',
        'class_id',
        'section_id',
        'type',
        'title',
        'message',
        'leave_from',
        'leave_to',
        'status',
        'decided_by',
        'decided_at',
        'decision_note',
        'attachment_path',
    ];
}

