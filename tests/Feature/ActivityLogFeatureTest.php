<?php

namespace Tests\Feature;

use App\Actions\Hazards\CreatePotentialHazardReport;
use App\Models\ActivityLog;
use App\Models\BodyPart;
use App\Models\IncidentCategory;
use App\Models\IncidentReport;
use App\Models\InjuryCategory;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_incident_creation_and_verification_generate_activity_logs(): void
    {
        $user = $this->createMahasiswaUser();
        $satgas = $this->createSatgasUser();
        $category = IncidentCategory::query()->create(['name' => 'Unsafe Action']);
        $injuryCategory = InjuryCategory::query()->create(['name' => 'Luka Memar']);
        $bodyPart = BodyPart::query()->create(['name' => 'Kaki']);
        $location = $this->createLocation();

        $this->actingAs($user)
            ->post(route('user.incidents.store'), [
                'victim_type' => 'self',
                'incident_category_id' => $category->id,
                'injury_category_id' => $injuryCategory->id,
                'body_part_id' => $bodyPart->id,
                'location_id' => $location->id,
                'incident_date' => '2026-04-25',
                'incident_time' => '09:00',
                'severity_level' => 'medium',
                'victim_name' => 'Rachmat Hidayat',
                'victim_position' => 'mahasiswa',
                'victim_gender' => 'male',
                'victim_age' => 22,
                'witness_name' => 'Abdul Muhyi',
                'ppe_used' => 'Tidak ada',
                'title' => 'Area kerja licin',
                'chronology' => 'Lantai area kerja licin dan perlu penanganan.',
                'cause' => 'Tumpahan cairan belum dibersihkan.',
                'initial_action' => 'Area diberi rambu sementara.',
                'impact' => 'Berpotensi menyebabkan terpeleset.',
                'unsafe_conditions' => ['area_kerja_berbahaya'],
                'unsafe_actions' => ['penggunaan_alat_tidak_aman'],
                'unsafe_condition_cause' => 'Area belum diberi pembatas.',
                'unsafe_action_cause' => 'Instruksi kerja belum jelas.',
                'warning_given_before_incident' => '0',
                'incident_previously_occurred' => '0',
                'proposed_preventions' => ['pengamanan_sumber_bahaya', 'inspeksi_rutin'],
                'prevention_action_plan' => 'Pasang pembatas dan jadwalkan inspeksi rutin.',
            ])
            ->assertRedirect(route('user.incidents.index'));

        $report = IncidentReport::query()->firstOrFail();

        $this->assertSame('Rachmat Hidayat', $report->victim_name);
        $this->assertSame('Abdul Muhyi', $report->witness_name);
        $this->assertSame(['area_kerja_berbahaya'], $report->unsafe_conditions);
        $this->assertSame(['pengamanan_sumber_bahaya', 'inspeksi_rutin'], $report->proposed_preventions);

        $this->actingAs($satgas)
            ->patch(route('satgas.incidents.verify', $report), [
                'verification_note' => 'Laporan valid dan diteruskan ke tahap tindak lanjut.',
            ])
            ->assertRedirect(route('satgas.incidents.show', $report));

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'type' => 'incident_created',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $user->id,
            'type' => 'incident_verified',
            'actor_id' => $satgas->id,
        ]);
    }

    public function test_hazard_creation_and_resolution_generate_activity_logs_and_are_visible_on_activity_page(): void
    {
        $user = $this->createMahasiswaUser();
        $satgas = $this->createSatgasUser();
        $location = $this->createLocation();

        $report = app(CreatePotentialHazardReport::class)->handle([
            'location_id' => $location->id,
            'hazard_type' => 'listrik',
            'title' => 'Kabel terbuka',
            'specific_location' => 'Dekat panel utama',
            'notes' => 'Perlu isolasi area.',
            'attachments' => [],
        ], $user->id);

        $this->actingAs($satgas)
            ->patch(route('satgas.hazards.update-status', $report), [
                'status' => 'resolved',
                'response_note' => 'Kabel sudah diamankan dan diganti.',
            ])
            ->assertRedirect(route('satgas.hazards.show', $report));

        $this->actingAs($user)
            ->get(route('user.activities.index'))
            ->assertOk()
            ->assertSeeText('Hazard report berhasil dikirim')
            ->assertSeeText('Status hazard report diperbarui')
            ->assertSeeText('Kabel sudah diamankan dan diganti.');
    }

    public function test_activity_page_only_displays_current_user_logs(): void
    {
        $user = $this->createMahasiswaUser();
        $otherUser = $this->createMahasiswaUser('mahasiswa-b');

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'actor_id' => $user->id,
            'type' => 'incident_created',
            'title' => 'Aktivitas saya',
            'description' => 'Aktivitas user saat ini.',
            'occurred_at' => now(),
        ]);

        ActivityLog::query()->create([
            'user_id' => $otherUser->id,
            'actor_id' => $otherUser->id,
            'type' => 'hazard_created',
            'title' => 'Aktivitas user lain',
            'description' => 'Tidak boleh tampil di akun ini.',
            'occurred_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.activities.index'))
            ->assertOk()
            ->assertSeeText('Aktivitas saya')
            ->assertDontSeeText('Aktivitas user lain');
    }

    public function test_user_can_mark_single_activity_and_all_activities_as_read(): void
    {
        $user = $this->createMahasiswaUser();

        $firstActivity = ActivityLog::query()->create([
            'user_id' => $user->id,
            'actor_id' => $user->id,
            'type' => 'incident_created',
            'title' => 'Aktivitas pertama',
            'description' => 'Belum dibaca.',
            'occurred_at' => now()->subMinute(),
        ]);

        $secondActivity = ActivityLog::query()->create([
            'user_id' => $user->id,
            'actor_id' => $user->id,
            'type' => 'hazard_created',
            'title' => 'Aktivitas kedua',
            'description' => 'Juga belum dibaca.',
            'occurred_at' => now(),
        ]);

        $this->actingAs($user)
            ->patch(route('user.activities.read', $firstActivity))
            ->assertRedirect(route('user.activities.index'))
            ->assertSessionHas('status', 'Aktivitas ditandai sudah dibaca.');

        $this->assertDatabaseMissing('activity_logs', [
            'id' => $firstActivity->id,
            'read_at' => null,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'id' => $secondActivity->id,
            'read_at' => null,
        ]);

        $this->actingAs($user)
            ->patch(route('user.activities.read-all'))
            ->assertRedirect(route('user.activities.index'))
            ->assertSessionHas('status', 'Semua aktivitas ditandai sudah dibaca.');

        $this->assertDatabaseMissing('activity_logs', [
            'id' => $secondActivity->id,
            'read_at' => null,
        ]);
    }

    public function test_user_dashboard_navbar_displays_unread_activity_badge(): void
    {
        $user = $this->createMahasiswaUser();

        ActivityLog::query()->create([
            'user_id' => $user->id,
            'actor_id' => $user->id,
            'type' => 'incident_created',
            'title' => 'Aktivitas belum dibaca',
            'description' => 'Akan muncul sebagai badge.',
            'occurred_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.activities.index'))
            ->assertOk()
            ->assertSeeText('Belum Dibaca')
            ->assertSeeText('1');
    }

    protected function createMahasiswaUser(string $code = 'mahasiswa'): User
    {
        $role = Role::query()->firstOrCreate([
            'code' => $code,
        ], [
            'name' => ucfirst(str_replace('-', ' ', $code)),
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createSatgasUser(): User
    {
        $role = Role::query()->firstOrCreate([
            'code' => 'satgas',
        ], [
            'name' => 'Satgas',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    protected function createLocation(): Location
    {
        return Location::query()->create([
            'name' => 'Workshop Pemesinan',
            'code' => 'WP-01',
            'is_active' => true,
        ]);
    }
}
