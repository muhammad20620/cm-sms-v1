<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassFeeStructuresTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('class_fee_structures')) {
            Schema::create('class_fee_structures', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('session_id')->index();
                $table->unsignedBigInteger('class_id')->index();
                $table->unsignedBigInteger('section_id')->nullable()->index();

                $table->string('title')->default('Monthly Fee');
                $table->integer('amount')->default(0);

                $table->timestamps();

                $table->unique(['school_id', 'session_id', 'class_id', 'section_id'], 'class_fee_structures_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('class_fee_structures');
    }
}

