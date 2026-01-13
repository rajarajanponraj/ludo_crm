<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->after('user_id');
                // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            }

            if (!Schema::hasColumn('attendances', 'distance_travelled')) {
                $table->decimal('distance_travelled', 12, 4)->default(0)->after('check_out_lng');
            }

            if (!Schema::hasColumn('attendances', 'late_mark')) {
                $table->boolean('late_mark')->default(0)->after('distance_travelled');
            }

            if (!Schema::hasColumn('attendances', 'early_leave')) {
                $table->boolean('early_leave')->default(0)->after('late_mark');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn(['company_id', 'distance_travelled', 'late_mark', 'early_leave']);
        });
    }
};
