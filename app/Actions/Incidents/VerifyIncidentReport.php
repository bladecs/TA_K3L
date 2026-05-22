<?php

namespace App\Actions\Incidents;

use App\Models\IncidentReport;
use App\Support\ActivityLogger;
use App\Support\WhatsAppReportNotifier;
use Illuminate\Support\Facades\DB;

class VerifyIncidentReport
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected WhatsAppReportNotifier $whatsAppReportNotifier,
    ) {
    }

    public function handle(IncidentReport $incidentReport, int $verifierId, ?string $note = null, array $classification = []): IncidentReport
    {
        return DB::transaction(function () use ($incidentReport, $verifierId, $note, $classification) {
            $previousStatus = $incidentReport->status;

            $incidentReport->update([
                'status' => 'verified',
                'title' => $classification['title'] ?? $incidentReport->title,
                'incident_category_id' => $classification['incident_category_id'] ?? $incidentReport->incident_category_id,
                'severity_level' => $classification['severity_level'] ?? $incidentReport->severity_level,
                'injury_category_id' => $classification['injury_category_id'] ?? $incidentReport->injury_category_id,
                'body_part_id' => $classification['body_part_id'] ?? $incidentReport->body_part_id,
                'impact' => $classification['impact'] ?? $incidentReport->impact,
                'unsafe_conditions' => $classification['unsafe_conditions'] ?? $incidentReport->unsafe_conditions,
                'unsafe_actions' => $classification['unsafe_actions'] ?? $incidentReport->unsafe_actions,
                'unsafe_condition_cause' => $classification['unsafe_condition_cause'] ?? $incidentReport->unsafe_condition_cause,
                'unsafe_action_cause' => $classification['unsafe_action_cause'] ?? $incidentReport->unsafe_action_cause,
                'warning_given_before_incident' => $classification['warning_given_before_incident'] ?? $incidentReport->warning_given_before_incident,
                'incident_previously_occurred' => $classification['incident_previously_occurred'] ?? $incidentReport->incident_previously_occurred,
                'proposed_preventions' => $classification['proposed_preventions'] ?? $incidentReport->proposed_preventions,
                'prevention_action_plan' => $classification['prevention_action_plan'] ?? $incidentReport->prevention_action_plan,
                'verified_location_id' => $classification['verified_location_id'] ?? $incidentReport->verified_location_id ?? $incidentReport->location_id,
                'verified_specific_location' => $classification['verified_specific_location'] ?? $incidentReport->verified_specific_location ?? $incidentReport->specific_location,
                'verified_latitude' => $classification['verified_latitude'] ?? $incidentReport->verified_latitude ?? $incidentReport->latitude,
                'verified_longitude' => $classification['verified_longitude'] ?? $incidentReport->verified_longitude ?? $incidentReport->longitude,
                'verified_location_accuracy' => $classification['verified_location_accuracy'] ?? $incidentReport->verified_location_accuracy ?? $incidentReport->location_accuracy,
                'location_verified_by' => $verifierId,
                'location_verified_at' => now(),
                'verified_by' => $verifierId,
                'verified_at' => now(),
            ]);

            $incidentReport->statusHistories()->create([
                'from_status' => $previousStatus,
                'to_status' => 'verified',
                'changed_by' => $verifierId,
                'change_note' => $note ?: 'Laporan telah diverifikasi oleh Satgas/Admin.',
                'created_at' => now(),
            ]);

            if ($incidentReport->reported_by !== null) {
                $this->activityLogger->log(
                    $incidentReport->reported_by,
                    $verifierId,
                    'incident_verified',
                    'Laporan insiden telah diverifikasi',
                    $note ?: "Laporan {$incidentReport->report_number} telah diverifikasi dan siap ditindaklanjuti.",
                    $incidentReport,
                    [
                        'from_status' => $previousStatus,
                        'to_status' => 'verified',
                        'report_number' => $incidentReport->report_number,
                    ],
                );
            }

            $this->whatsAppReportNotifier->incidentStatusUpdated(
                $incidentReport,
                'verified',
                $note ?: "Laporan {$incidentReport->report_number} telah diverifikasi dan siap ditindaklanjuti.",
            );

            return $incidentReport->fresh([
                'category',
                'location',
                'verifiedLocation',
                'reporter',
                'statusHistories.changer',
            ]);
        });
    }
}
