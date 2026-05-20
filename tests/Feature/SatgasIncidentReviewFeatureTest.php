<?php

namespace Tests\Feature;

use App\Models\IncidentCategory;
use App\Models\IncidentFollowUp;
use App\Models\IncidentReport;
use App\Models\InjuryCategory;
use App\Models\BodyPart;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SatgasIncidentReviewFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_satgas_incident_index_displays_status_summary_cards(): void
    {
        $satgas = $this->createSatgasUser();
        $reporter = $this->createMahasiswaUser();
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Condition']);
        $location = Location::query()->create([
            'name' => 'Workshop Teknik',
            'code' => 'WORKSHOP',
            'is_active' => true,
        ]);

        foreach (['submitted', 'verified', 'investigating', 'resolved', 'closed'] as $index => $status) {
            IncidentReport::query()->create([
                'report_number' => sprintf('INC-SAT-%03d', $index + 1),
                'reported_by' => $reporter->id,
                'incident_category_id' => $category->id,
                'location_id' => $location->id,
                'incident_date' => '2026-04-25',
                'incident_time' => '08:00:00',
                'severity_level' => 'medium',
                'title' => "Laporan {$status}",
                'chronology' => "Kronologi laporan {$status} yang cukup panjang untuk disimpan.",
                'status' => $status,
                'submitted_at' => now(),
            ]);
        }

        $this->actingAs($satgas)
            ->get(route('satgas.incidents.index'))
            ->assertOk()
            ->assertSeeText('Submitted')
            ->assertSeeText('Verified')
            ->assertSeeText('Investigating')
            ->assertSeeText('Resolved')
            ->assertSeeText('Closed');
    }

    public function test_satgas_incident_detail_displays_follow_ups_and_attachments_panel(): void
    {
        $satgas = $this->createSatgasUser();
        $reporter = $this->createMahasiswaUser();
        $category = IncidentCategory::query()->create(['name' => 'Near Miss']);
        $injuryCategory = InjuryCategory::query()->create(['name' => 'Luka Memar']);
        $bodyPart = BodyPart::query()->create(['name' => 'Kaki']);
        $location = Location::query()->create([
            'name' => 'Laboratorium Kimia',
            'code' => 'LAB-KIM',
            'is_active' => true,
        ]);

        $report = IncidentReport::query()->create([
            'report_number' => 'INC-DET-001',
            'reported_by' => $reporter->id,
            'incident_category_id' => $category->id,
            'injury_category_id' => $injuryCategory->id,
            'body_part_id' => $bodyPart->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '09:15:00',
            'severity_level' => 'high',
            'victim_name' => 'Rachmat Hidayat',
            'victim_position' => 'mahasiswa',
            'victim_gender' => 'male',
            'victim_age' => 22,
            'witness_name' => 'Abdul Muhyi',
            'ppe_used' => 'Tidak ada',
            'title' => 'Perlu inspeksi lanjutan',
            'chronology' => 'Temuan membutuhkan inspeksi lanjutan dan sudah dicatat oleh satgas.',
            'impact' => 'Korban mengalami memar ringan.',
            'unsafe_conditions' => ['area_kerja_berbahaya'],
            'unsafe_actions' => ['penggunaan_alat_tidak_aman'],
            'warning_given_before_incident' => false,
            'incident_previously_occurred' => false,
            'proposed_preventions' => ['pengamanan_sumber_bahaya', 'inspeksi_rutin'],
            'prevention_action_plan' => 'Pasang pembatas dan jadwalkan inspeksi rutin.',
            'status' => 'investigating',
            'submitted_at' => now(),
        ]);

        IncidentFollowUp::query()->create([
            'incident_report_id' => $report->id,
            'action_taken' => 'Jadwalkan inspeksi panel dan batasi akses sementara.',
            'action_owner_id' => $satgas->id,
            'due_date' => '2026-04-27',
            'status' => 'in_progress',
            'created_by' => $satgas->id,
        ]);

        $this->actingAs($satgas)
            ->get(route('satgas.incidents.show', $report))
            ->assertOk()
            ->assertSeeText('Tindak Lanjut')
            ->assertSeeText('Jadwalkan inspeksi panel dan batasi akses sementara.')
            ->assertSeeText('Lampiran')
            ->assertSeeText('Rachmat Hidayat')
            ->assertSeeText('Abdul Muhyi')
            ->assertSeeText('Luka Memar')
            ->assertSeeText('Area kerja yang berbahaya')
            ->assertSeeText('Beri pengamanan pada sumber bahaya')
            ->assertSeeText('Pasang pembatas dan jadwalkan inspeksi rutin.');
    }

    public function test_satgas_cannot_reverify_closed_incident_from_detail_page(): void
    {
        $satgas = $this->createSatgasUser();
        $reporter = $this->createMahasiswaUser();
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Action']);
        $location = Location::query()->create([
            'name' => 'Gedung Perkuliahan A',
            'code' => 'GPA',
            'is_active' => true,
        ]);

        $report = IncidentReport::query()->create([
            'report_number' => 'INC-CLS-001',
            'reported_by' => $reporter->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '10:00:00',
            'severity_level' => 'low',
            'title' => 'Kasus sudah ditutup',
            'chronology' => 'Kasus ini sudah selesai dan tidak boleh diverifikasi ulang.',
            'status' => 'closed',
            'submitted_at' => now(),
            'closed_at' => now(),
        ]);

        $this->actingAs($satgas)
            ->get(route('satgas.incidents.show', $report))
            ->assertOk()
            ->assertSeeText('tidak memerlukan verifikasi ulang', false)
            ->assertDontSeeText('Catatan verifikasi');
    }

    protected function createSatgasUser(): User
    {
        $role = Role::query()->create(['name' => 'Satgas', 'code' => 'satgas']);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createMahasiswaUser(): User
    {
        $role = Role::query()->create(['name' => 'Mahasiswa', 'code' => 'mahasiswa']);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
