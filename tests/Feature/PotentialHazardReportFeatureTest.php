<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\PotentialHazardReport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PotentialHazardReportFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_hazard_create_page_displays_combined_report_switcher(): void
    {
        $user = $this->createMahasiswaUser();

        $this->actingAs($user)
            ->get(route('user.hazards.create'))
            ->assertOk()
            ->assertSeeText('Form Pelaporan K3L')
            ->assertSeeText('Form Insiden')
            ->assertSeeText('Form Hazard')
            ->assertSee('data-report-panel="incident"', false)
            ->assertSee('data-report-panel="hazard"', false);
    }

    public function test_user_can_submit_potential_hazard_report(): void
    {
        Storage::fake('public');

        $user = $this->createMahasiswaUser();
        $location = Location::query()->create([
            'name' => 'Workshop Teknik',
            'code' => 'WORKSHOP',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post(route('user.hazards.store'), [
            'title' => 'Kabel panel terkelupas',
            'hazard_type' => 'listrik',
            'location_id' => $location->id,
            'specific_location' => 'Sisi utara panel utama',
            'notes' => 'Perlu isolasi area sebelum praktikum berikutnya.',
            'attachments' => [
                UploadedFile::fake()->image('hazard-photo.jpg'),
            ],
        ]);

        $response
            ->assertRedirect(route('user.hazards.create'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('potential_hazard_reports', [
            'reported_by' => $user->id,
            'location_id' => $location->id,
            'hazard_type' => 'listrik',
            'title' => 'Kabel panel terkelupas',
            'status' => 'submitted',
        ]);

        $report = PotentialHazardReport::query()->first();

        $this->assertNotNull($report);
        $this->assertCount(1, $report->attachments);
        Storage::disk('public')->assertExists($report->attachments->first()->file_path);
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
