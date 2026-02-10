<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentWithdrawalsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('student_withdrawals')) {
            Schema::create('student_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('student_id')->index();

                // Snapshot at time of withdrawal (for certificate stability)
                $table->unsignedBigInteger('enrollment_id')->nullable()->index();
                $table->unsignedBigInteger('class_id')->nullable()->index();
                $table->unsignedBigInteger('section_id')->nullable()->index();
                $table->unsignedBigInteger('session_id')->nullable()->index();
                $table->string('admission_no')->nullable();
                $table->string('enrollment_no')->nullable();
                $table->string('father_name')->nullable();
                $table->string('father_cnic')->nullable();

                // Certificate/withdrawal info
                $table->string('slc_no')->nullable()->index();
                $table->date('withdrawal_date')->nullable()->index();
                $table->date('slc_issue_date')->nullable();
                $table->text('reason')->nullable();
                $table->text('remarks')->nullable();
                $table->boolean('dues_cleared')->default(false);

                // Audit
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->timestamps();

                $table->unique(['school_id', 'student_id'], 'student_withdrawals_school_student_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('student_withdrawals');
    }
}

