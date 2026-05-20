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

class IncidentUserPagesFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_incident_create_page_displays_combined_report_switcher(): void
    {
        $user = $this->createMahasiswaUser();

        $this->actingAs($user)
            ->get(route('user.incidents.create'))
            ->assertOk()
            ->assertSeeText('Form Pelaporan K3L')
            ->assertSeeText('Form Insiden')
            ->assertSeeText('Form Hazard')
            ->assertSee('data-report-panel="incident"', false)
            ->assertSee('data-report-panel="hazard"', false);
    }

    public function test_incident_index_uses_overall_summary_counts(): void
    {
        $user = $this->createMahasiswaUser();
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Condition']);
        $location = Location::query()->create([
            'name' => 'Workshop Teknik',
            'code' => 'WORKSHOP',
            'is_active' => true,
        ]);

        foreach (range(1, 12) as $number) {
            IncidentReport::query()->create([
                'report_number' => sprintf('INC-IDX-%03d', $number),
                'reported_by' => $user->id,
                'incident_category_id' => $category->id,
                'location_id' => $location->id,
                'incident_date' => '2026-04-25',
                'incident_time' => '08:00:00',
                'severity_level' => 'low',
                'title' => "Laporan {$number}",
                'chronology' => "Kronologi laporan {$number} yang cukup panjang untuk disimpan.",
                'status' => $number <= 2 ? 'submitted' : ($number === 3 ? 'closed' : 'verified'),
                'submitted_at' => now()->subMinutes($number),
            ]);
        }

        $this->actingAs($user)
            ->get(route('user.incidents.index'))
            ->assertOk()
            ->assertSeeText('Total Laporan')
            ->assertSeeText('12')
            ->assertSeeText('Perlu Review')
            ->assertSeeText('2')
            ->assertSeeText('Selesai')
            ->assertSeeText('1');
    }

    public function test_incident_status_page_displays_resolved_status_board(): void
    {
        $user = $this->createMahasiswaUser();
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Action']);
        $location = Location::query()->create([
            'name' => 'Laboratorium Kimia',
            'code' => 'LAB-KIM',
            'is_active' => true,
        ]);

        IncidentReport::query()->create([
            'report_number' => 'INC-STS-001',
            'reported_by' => $user->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '09:00:00',
            'severity_level' => 'medium',
            'title' => 'Mesin selesai diperbaiki',
            'chronology' => 'Perbaikan utama telah dilakukan dan tinggal penutupan laporan.',
            'status' => 'resolved',
            'submitted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.incidents.status'))
            ->assertOk()
            ->assertSeeText('Tindakan Selesai')
            ->assertSeeText('resolved');
    }

    public function test_incident_detail_page_displays_follow_ups(): void
    {
        $user = $this->createMahasiswaUser();
        $satgasRole = Role::query()->create(['name' => 'Satgas', 'code' => 'satgas']);
        $satgas = User::factory()->create([
            'role_id' => $satgasRole->id,
            'is_active' => true,
        ]);
        $category = IncidentCategory::query()->create(['name' => 'Near Miss']);
        $injuryCategory = InjuryCategory::query()->create(['name' => 'Luka Memar']);
        $bodyPart = BodyPart::query()->create(['name' => 'Kaki']);
        $location = Location::query()->create([
            'name' => 'Gedung Perkuliahan A',
            'code' => 'GPA',
            'is_active' => true,
        ]);

        $report = IncidentReport::query()->create([
            'report_number' => 'INC-SHW-001',
            'reported_by' => $user->id,
            'incident_category_id' => $category->id,
            'injury_category_id' => $injuryCategory->id,
            'body_part_id' => $bodyPart->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '11:00:00',
            'severity_level' => 'high',
            'victim_name' => 'Rachmat Hidayat',
            'victim_position' => 'mahasiswa',
            'victim_gender' => 'male',
            'victim_age' => 22,
            'witness_name' => 'Abdul Muhyi',
            'ppe_used' => 'Tidak ada',
            'title' => 'Perlu penggantian panel',
            'chronology' => 'Panel menunjukkan gejala panas berlebih dan area langsung diamankan.',
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
            'action_taken' => 'Penggantian panel dijadwalkan dan area diberi pembatas.',
            'action_owner_id' => $satgas->id,
            'due_date' => '2026-04-27',
            'status' => 'in_progress',
            'created_by' => $satgas->id,
        ]);

        $this->actingAs($user)
            ->get(route('user.incidents.show', $report))
            ->assertOk()
            ->assertSeeText('Tindak Lanjut')
            ->assertSeeText('Penggantian panel dijadwalkan dan area diberi pembatas.')
            ->assertSeeText('Rachmat Hidayat')
            ->assertSeeText('Abdul Muhyi')
            ->assertSeeText('Luka Memar')
            ->assertSeeText('Area kerja yang berbahaya')
            ->assertSeeText('Beri pengamanan pada sumber bahaya')
            ->assertSeeText('Pasang pembatas dan jadwalkan inspeksi rutin.');
    }

    protected function createMahasiswaUser(): User
    {
        $role = Role::query()->create([
            'name' => 'Mahasiswa',
            'code' => 'mahasiswa',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
