<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeSiblingDiscountsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_sibling_discounts')) {
            Schema::create('fee_sibling_discounts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('session_id')->nullable()->index(); // null = all sessions

                // If guardian_id is null => applies to all families in the school
                $table->unsignedBigInteger('guardian_id')->nullable()->index();

                // dob|class|hybrid
                $table->string('basis', 20)->default('hybrid')->index();

                // Apply only if family has at least N children
                $table->unsignedTinyInteger('min_children')->default(2);

                // percent|fixed
                $table->string('mode', 20)->default('percent');
                $table->integer('value')->default(50); // percent (0-100) or fixed amount

                $table->boolean('is_active')->default(true)->index();
                $table->string('note')->nullable();

                $table->timestamps();

                $table->index(['school_id', 'session_id', 'guardian_id', 'is_active'], 'fee_sibling_discounts_lookup_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_sibling_discounts');
    }
}

