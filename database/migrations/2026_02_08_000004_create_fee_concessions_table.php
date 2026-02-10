<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFeeConcessionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('fee_concessions')) {
            Schema::create('fee_concessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('session_id')->nullable()->index(); // null = all sessions

                // scope_type: student|guardian
                $table->string('scope_type', 20)->index();
                $table->unsignedBigInteger('student_id')->nullable()->index();
                $table->unsignedBigInteger('guardian_id')->nullable()->index();

                // mode: percent|fixed
                $table->string('mode', 20)->default('fixed');
                $table->integer('value')->default(0); // percent (0-100) or fixed amount

                $table->boolean('is_active')->default(true)->index();
                $table->string('note')->nullable();

                $table->timestamps();

                $table->index(['school_id', 'scope_type', 'student_id', 'guardian_id'], 'fee_concessions_scope_idx');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('fee_concessions');
    }
}

