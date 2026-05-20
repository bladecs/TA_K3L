<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->string('victim_name', 150)->nullable()->after('victim_user_id');
            $table->text('victim_address')->nullable()->after('victim_name');
            $table->string('victim_position', 50)->nullable()->after('victim_address');
            $table->string('victim_position_description', 200)->nullable()->after('victim_position');
            $table->enum('victim_gender', ['male', 'female'])->nullable()->after('victim_position_description');
            $table->unsignedTinyInteger('victim_age')->nullable()->after('victim_gender');
            $table->string('witness_name', 150)->nullable()->after('incident_time');
            $table->text('ppe_used')->nullable()->after('witness_name');
            $table->json('unsafe_conditions')->nullable()->after('impact');
            $table->json('unsafe_actions')->nullable()->after('unsafe_conditions');
            $table->longText('unsafe_condition_cause')->nullable()->after('unsafe_actions');
            $table->longText('unsafe_action_cause')->nullable()->after('unsafe_condition_cause');
            $table->boolean('warning_given_before_incident')->nullable()->after('unsafe_action_cause');
            $table->boolean('incident_previously_occurred')->nullable()->after('warning_given_before_incident');
            $table->json('proposed_preventions')->nullable()->after('incident_previously_occurred');
            $table->longText('prevention_action_plan')->nullable()->after('proposed_preventions');
        });
    }

    public function down(): void
    {
        Schema::table('incident_reports', function (Blueprint $table) {
            $table->dropColumn([
                'victim_name',
                'victim_address',
                'victim_position',
                'victim_position_description',
                'victim_gender',
                'victim_age',
                'witness_name',
                'ppe_used',
                'unsafe_conditions',
                'unsafe_actions',
                'unsafe_condition_cause',
                'unsafe_action_cause',
                'warning_given_before_incident',
                'incident_previously_occurred',
                'proposed_preventions',
                'prevention_action_plan',
            ]);
        });
    }
};
