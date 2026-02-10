<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuardianIdToStudentFeeManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_fee_managers', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fee_managers', 'guardian_id')) {
                $table->unsignedBigInteger('guardian_id')->nullable()->after('parent_id');
                $table->index(['guardian_id', 'school_id', 'session_id'], 'sfm_guardian_school_session_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_fee_managers', function (Blueprint $table) {
            if (Schema::hasColumn('student_fee_managers', 'guardian_id')) {
                $table->dropIndex('sfm_guardian_school_session_idx');
                $table->dropColumn('guardian_id');
            }
        });
    }
}

