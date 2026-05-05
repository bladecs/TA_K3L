<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->string('map_source', 30)->nullable()->after('mapped_at');
            $table->decimal('floorplan_x', 10, 3)->nullable()->after('map_source');
            $table->decimal('floorplan_y', 10, 3)->nullable()->after('floorplan_x');

            $table->index(['map_source', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->dropIndex(['map_source', 'status']);
            $table->dropColumn(['map_source', 'floorplan_x', 'floorplan_y']);
        });
    }
};
