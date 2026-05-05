<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('specific_location');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->string('risk_level', 30)->nullable()->after('longitude');
            $table->foreignId('mapped_by')->nullable()->after('risk_level')->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamp('mapped_at')->nullable()->after('mapped_by');

            $table->index(['latitude', 'longitude']);
            $table->index(['risk_level', 'status']);
        });
    }

    public function down(): void
    {
        Schema::table('potential_hazard_reports', function (Blueprint $table) {
            $table->dropIndex(['latitude', 'longitude']);
            $table->dropIndex(['risk_level', 'status']);
            $table->dropConstrainedForeignId('mapped_by');
            $table->dropColumn(['latitude', 'longitude', 'risk_level', 'mapped_at']);
        });
    }
};
