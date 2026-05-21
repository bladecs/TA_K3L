<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->string('building_key', 100)->nullable()->after('specific_location');
            $table->unsignedSmallInteger('building_floor')->nullable()->after('building_key');
            $table->foreignId('campus_room_id')->nullable()->after('building_floor')->constrained('campus_rooms')->nullOnDelete();
        });

        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->string('building_key', 100)->nullable()->after('specific_location');
            $table->unsignedSmallInteger('building_floor')->nullable()->after('building_key');
            $table->foreignId('campus_room_id')->nullable()->after('building_floor')->constrained('campus_rooms')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_room_id');
            $table->dropColumn(['building_key', 'building_floor']);
        });

        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropConstrainedForeignId('campus_room_id');
            $table->dropColumn(['building_key', 'building_floor']);
        });
    }
};
