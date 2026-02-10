<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStudentNumberPatternsToSchoolsTable extends Migration
{
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'admission_number_pattern')) {
                $table->string('admission_number_pattern')->nullable()->after('running_session');
            }
            if (!Schema::hasColumn('schools', 'enrollment_number_pattern')) {
                $table->string('enrollment_number_pattern')->nullable()->after('admission_number_pattern');
            }
        });
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'enrollment_number_pattern')) {
                $table->dropColumn('enrollment_number_pattern');
            }
            if (Schema::hasColumn('schools', 'admission_number_pattern')) {
                $table->dropColumn('admission_number_pattern');
            }
        });
    }
}

