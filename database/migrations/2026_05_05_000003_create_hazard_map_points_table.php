<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hazard_map_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('title', 200);
            $table->string('hazard_type', 50)->nullable();
            $table->string('risk_level', 30)->default('sedang');
            $table->text('description')->nullable();
            $table->string('map_source', 30);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->decimal('floorplan_x', 10, 3)->nullable();
            $table->decimal('floorplan_y', 10, 3)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['map_source', 'is_active']);
            $table->index(['risk_level', 'is_active']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hazard_map_points');
    }
};
