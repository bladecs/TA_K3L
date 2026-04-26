<?php

namespace Tests\Feature;

use App\Models\EmergencyContact;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Location;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSatgasDashboardFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_displays_cross_module_database_counts(): void
    {
        $adminRole = Role::query()->create(['name' => 'Admin', 'code' => 'admin']);
        $satgasRole = Role::query()->create(['name' => 'Satgas', 'code' => 'satgas']);
        $mahasiswaRole = Role::query()->create(['name' => 'Mahasiswa', 'code' => 'mahasiswa']);

        $admin = User::factory()->create(['role_id' => $adminRole->id, 'is_active' => true]);
        $satgas = User::factory()->create(['role_id' => $satgasRole->id, 'is_active' => true]);
        $mahasiswa = User::factory()->create(['role_id' => $mahasiswaRole->id, 'is_active' => true]);

        $location = Location::query()->create([
            'name' => 'Workshop Teknik',
            'code' => 'WORKSHOP',
            'is_active' => true,
        ]);
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Condition']);

        IncidentReport::query()->create([
            'report_number' => 'INC-ADM-001',
            'reported_by' => $mahasiswa->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '08:30:00',
            'severity_level' => 'medium',
            'title' => 'Kondisi area licin',
            'chronology' => 'Area ditemukan licin dan sudah diberi penanda sementara.',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $knowledgeCategory = KnowledgeCategory::query()->create([
            'name' => 'Pelaporan Insiden',
            'slug' => 'pelaporan-insiden',
        ]);

        KnowledgeArticle::query()->create([
            'knowledge_category_id' => $knowledgeCategory->id,
            'title' => 'Panduan Pelaporan Cepat',
            'slug' => 'panduan-pelaporan-cepat',
            'summary' => 'Panduan singkat.',
            'content' => 'Isi materi panduan.',
            'status' => 'published',
            'created_by' => $admin->id,
            'approved_by' => $admin->id,
            'published_at' => now(),
        ]);

        PotentialHazardReport::query()->create([
            'report_number' => 'HZD-ADM-001',
            'reported_by' => $mahasiswa->id,
            'location_id' => $location->id,
            'hazard_type' => 'lingkungan',
            'title' => 'Lantai licin',
            'status' => 'reviewed',
            'submitted_at' => now(),
            'reviewed_by' => $satgas->id,
            'reviewed_at' => now(),
        ]);

        EmergencyContact::query()->create([
            'name' => 'Kontak Darurat Kampus',
            'phone_number' => '112',
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Materi K3')
            ->assertSeeText('Hazard Report')
            ->assertSeeText('Emergency Center')
            ->assertSeeText('Konten pembelajaran yang sudah dipublikasikan.')
            ->assertSeeText('Hazard Terkini')
            ->assertSeeText('Lantai licin')
            ->assertSeeText('Status Operasional');
    }

    public function test_satgas_dashboard_displays_resolved_and_priority_workload(): void
    {
        $satgasRole = Role::query()->create(['name' => 'Satgas', 'code' => 'satgas']);
        $mahasiswaRole = Role::query()->create(['name' => 'Mahasiswa', 'code' => 'mahasiswa']);

        $satgas = User::factory()->create(['role_id' => $satgasRole->id, 'is_active' => true]);
        $mahasiswa = User::factory()->create(['role_id' => $mahasiswaRole->id, 'is_active' => true]);

        $location = Location::query()->create([
            'name' => 'Laboratorium Kimia',
            'code' => 'LAB-KIM',
            'is_active' => true,
        ]);
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Action']);

        IncidentReport::query()->create([
            'report_number' => 'INC-STG-001',
            'reported_by' => $mahasiswa->id,
            'incident_category_id' => $category->id,
            'location_id' => $location->id,
            'incident_date' => '2026-04-25',
            'incident_time' => '09:00:00',
            'severity_level' => 'critical',
            'title' => 'Perlu evakuasi area',
            'chronology' => 'Ditemukan kondisi berisiko tinggi dan area diamankan.',
            'status' => 'resolved',
            'submitted_at' => now(),
        ]);

        $this->actingAs($satgas)
            ->get(route('satgas.dashboard'))
            ->assertOk()
            ->assertSeeText('Penyelesaian laporan insiden')
            ->assertSeeText('Perlu evakuasi area')
            ->assertSeeText('Rekomendasi tindak lanjut Satgas')
            ->assertSeeText('Penanganan yang perlu perhatian');
    }
}
