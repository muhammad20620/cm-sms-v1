<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFeeGeneratorColumnsToStudentFeeManagersTable extends Migration
{
    public function up()
    {
        Schema::table('student_fee_managers', function (Blueprint $table) {
            // Some installs may already have these columns (DB created via install.sql).
            if (!Schema::hasColumn('student_fee_managers', 'parent_id')) {
                $table->unsignedBigInteger('parent_id')->nullable()->after('class_id');
                $table->index(['parent_id', 'school_id', 'session_id'], 'sfm_parent_school_session_idx');
            }

            if (!Schema::hasColumn('student_fee_managers', 'amount')) {
                // Base amount before discount
                $table->integer('amount')->nullable()->after('total_amount');
            }

            if (!Schema::hasColumn('student_fee_managers', 'discounted_price')) {
                // Discount amount
                $table->integer('discounted_price')->nullable()->default(0)->after('amount');
            }

            if (!Schema::hasColumn('student_fee_managers', 'fee_group_id')) {
                // One generator run id (UUID-ish string). Used for pooling/printing.
                $table->string('fee_group_id', 60)->nullable()->index();
            }

            if (!Schema::hasColumn('student_fee_managers', 'billing_month')) {
                $table->unsignedTinyInteger('billing_month')->nullable()->index();
            }

            if (!Schema::hasColumn('student_fee_managers', 'billing_year')) {
                $table->unsignedSmallInteger('billing_year')->nullable()->index();
            }

            if (!Schema::hasColumn('student_fee_managers', 'due_date')) {
                $table->date('due_date')->nullable()->index();
            }

            if (!Schema::hasColumn('student_fee_managers', 'fee_breakdown')) {
                // JSON/text to store breakdown later (fee heads etc.)
                $table->longText('fee_breakdown')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('student_fee_managers', function (Blueprint $table) {
            if (Schema::hasColumn('student_fee_managers', 'fee_breakdown')) {
                $table->dropColumn('fee_breakdown');
            }
            if (Schema::hasColumn('student_fee_managers', 'due_date')) {
                $table->dropColumn('due_date');
            }
            if (Schema::hasColumn('student_fee_managers', 'billing_year')) {
                $table->dropColumn('billing_year');
            }
            if (Schema::hasColumn('student_fee_managers', 'billing_month')) {
                $table->dropColumn('billing_month');
            }
            if (Schema::hasColumn('student_fee_managers', 'fee_group_id')) {
                $table->dropColumn('fee_group_id');
            }
            if (Schema::hasColumn('student_fee_managers', 'discounted_price')) {
                $table->dropColumn('discounted_price');
            }
            if (Schema::hasColumn('student_fee_managers', 'amount')) {
                $table->dropColumn('amount');
            }
            if (Schema::hasColumn('student_fee_managers', 'parent_id')) {
                $table->dropIndex('sfm_parent_school_session_idx');
                $table->dropColumn('parent_id');
            }
        });
    }
}

