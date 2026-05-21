<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('building_key', 100);
            $table->string('building_name', 150);
            $table->unsignedSmallInteger('floor');
            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['building_key', 'floor', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_rooms');
    }
};
