<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSortOrderToClassesTable extends Migration
{
    public function up()
    {
        Schema::table('classes', function (Blueprint $table) {
            if (!Schema::hasColumn('classes', 'sort_order')) {
                // Smaller = younger class (e.g., Playgroup=0, Nursery=1, KG=2, Class 1=3 ...)
                $table->integer('sort_order')->nullable()->after('name')->index();
            }
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            if (Schema::hasColumn('classes', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
}

