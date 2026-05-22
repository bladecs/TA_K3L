<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floorplans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('building_key', 100);
            $table->string('building_name', 150);
            $table->unsignedSmallInteger('floor');
            $table->string('name', 150);
            $table->unsignedSmallInteger('version')->default(1);
            $table->string('source_type', 30)->default('image');
            $table->string('file_disk', 50)->nullable();
            $table->string('file_path')->nullable();
            $table->string('original_filename')->nullable();
            $table->longText('svg_markup')->nullable();
            $table->unsignedInteger('canvas_width')->nullable();
            $table->unsignedInteger('canvas_height')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['building_key', 'floor', 'version']);
            $table->index(['building_key', 'floor', 'is_active']);
            $table->index(['location_id', 'is_active']);
        });

        Schema::create('floorplan_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('floorplan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campus_room_id')->constrained()->cascadeOnDelete();
            $table->string('shape_key', 120);
            $table->string('shape_type', 30)->default('polygon');
            $table->json('geometry')->nullable();
            $table->string('label')->nullable();
            $table->string('default_fill_color', 20)->default('#e5e7eb');
            $table->string('incident_fill_color', 20)->default('#ef4444');
            $table->string('hazard_fill_color', 20)->default('#f59e0b');
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['floorplan_id', 'campus_room_id']);
            $table->unique(['floorplan_id', 'shape_key']);
            $table->index(['campus_room_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('floorplan_rooms');
        Schema::dropIfExists('floorplans');
    }
};
