<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSlcNumberPatternToSchoolsTable extends Migration
{
    public function up()
    {
        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'slc_number_pattern')) {
                $table->string('slc_number_pattern')->nullable()->after('enrollment_number_pattern');
            }
        });
    }

    public function down()
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'slc_number_pattern')) {
                $table->dropColumn('slc_number_pattern');
            }
        });
    }
}

