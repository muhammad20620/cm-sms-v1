<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentNumberSequencesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('student_number_sequences')) {
            Schema::create('student_number_sequences', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->string('type', 20)->index(); // admission|enrollment
                $table->integer('year')->index();
                $table->unsignedBigInteger('last_seq')->default(0);
                $table->timestamps();

                $table->unique(['school_id', 'type', 'year'], 'student_number_sequences_unique');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('student_number_sequences');
    }
}

