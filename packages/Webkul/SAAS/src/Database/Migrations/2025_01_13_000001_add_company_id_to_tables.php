<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'leads',
            'persons',
            'organizations',
            'products',
            'quotes',
            'activities',
            // 'attributes', // Keeping attributes global for MVP 
            'lead_pipelines',
            'lead_sources',
            'lead_types',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'company_id')) {
                        $table->unsignedBigInteger('company_id')->nullable()->after('id');
                        // Foreign key constraint - optional for MVP but good for integrity
                        // $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'leads',
            'persons',
            'organizations',
            'products',
            'quotes',
            'activities',
            'lead_pipelines',
            'lead_sources',
            'lead_types',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('company_id');
                });
            }
        }
    }
};
