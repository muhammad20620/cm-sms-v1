//database migration
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

if (!Schema::hasColumn('sessions', 'school_id')) {
    Schema::table('sessions', function (Blueprint $table) {
        $table->integer('school_id')->nullable();
    });
}

if (!Schema::hasColumn('schools', 'running_session')) {
    Schema::table('schools', function (Blueprint $table2) {
        $table2->integer('running_session')->nullable();
    });
}

if (!Schema::hasColumn('addons', 'purchase_code')) {
    Schema::table('addons', function (Blueprint $table3) {
        $table3->string('purchase_code')->nullable();
    });
}