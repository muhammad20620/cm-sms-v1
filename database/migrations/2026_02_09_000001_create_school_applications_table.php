<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('school_applications')) {
            return;
        }

        Schema::create('school_applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->index();

            // Who submitted
            $table->unsignedBigInteger('parent_id')->index();
            $table->unsignedBigInteger('guardian_id')->nullable()->index();

            // Optional: application for a specific student
            $table->unsignedBigInteger('student_id')->nullable()->index();
            $table->unsignedBigInteger('class_id')->nullable()->index();
            $table->unsignedBigInteger('section_id')->nullable()->index();

            // leave|other
            $table->string('type', 20)->index();
            $table->string('title', 120);
            $table->text('message')->nullable();

            // For leave applications
            $table->date('leave_from')->nullable()->index();
            $table->date('leave_to')->nullable()->index();

            // pending|approved|rejected
            $table->string('status', 20)->default('pending')->index();
            $table->unsignedBigInteger('decided_by')->nullable()->index();
            $table->timestamp('decided_at')->nullable()->index();
            $table->text('decision_note')->nullable();

            $table->string('attachment_path')->nullable();

            $table->timestamps();

            $table->index(['school_id', 'status', 'type'], 'school_applications_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_applications');
    }
};

