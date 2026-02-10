<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGuardiansAndStudentGuardiansTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('guardians')) {
            Schema::create('guardians', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id')->index();
                $table->unsignedBigInteger('user_id')->nullable()->index(); // optional login user (role_id=6)

                $table->string('name')->nullable();
                $table->string('id_card_no')->nullable();
                $table->string('id_card_no_normalized')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('address')->nullable();

                $table->timestamps();

                $table->unique(['school_id', 'id_card_no_normalized'], 'guardians_school_cnic_unique');
            });
        } else {
            Schema::table('guardians', function (Blueprint $table) {
                if (!Schema::hasColumn('guardians', 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable()->index();
                }
                if (!Schema::hasColumn('guardians', 'user_id')) {
                    $table->unsignedBigInteger('user_id')->nullable()->index();
                }
                if (!Schema::hasColumn('guardians', 'name')) {
                    $table->string('name')->nullable();
                }
                if (!Schema::hasColumn('guardians', 'id_card_no')) {
                    $table->string('id_card_no')->nullable();
                }
                if (!Schema::hasColumn('guardians', 'id_card_no_normalized')) {
                    $table->string('id_card_no_normalized')->nullable()->index();
                }
                if (!Schema::hasColumn('guardians', 'phone')) {
                    $table->string('phone')->nullable();
                }
                if (!Schema::hasColumn('guardians', 'address')) {
                    $table->string('address')->nullable();
                }
            });
        }

        if (!Schema::hasTable('student_guardians')) {
            Schema::create('student_guardians', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('student_id')->index(); // users.id (role_id=7)
                $table->unsignedBigInteger('guardian_id')->index(); // guardians.id
                $table->string('relation')->default('father'); // father/mother/guardian
                $table->boolean('is_primary')->default(true);
                $table->boolean('is_fee_payer')->default(true);
                $table->timestamps();

                $table->unique(['student_id', 'guardian_id', 'relation'], 'student_guardians_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('student_guardians')) {
            Schema::drop('student_guardians');
        }
        if (Schema::hasTable('guardians')) {
            Schema::drop('guardians');
        }
    }
}

