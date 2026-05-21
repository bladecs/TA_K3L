<?php

namespace Tests\Feature;

use App\Models\CampusRoom;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCampusRoomFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_update_campus_room(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin)
            ->post(route('admin.campus-rooms.store'), [
                'building_key' => 'gedung-teori',
                'floor' => 2,
                'name' => 'B201',
                'code' => 'B201',
                'sort_order' => 10,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.campus-rooms.index'));

        $room = CampusRoom::query()->firstOrFail();

        $this->actingAs($admin)
            ->put(route('admin.campus-rooms.update', $room), [
                'building_key' => 'gedung-teori',
                'floor' => 3,
                'name' => 'B301',
                'code' => 'B301',
                'sort_order' => 20,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.campus-rooms.index'));

        $this->assertDatabaseHas('campus_rooms', [
            'building_key' => 'gedung-teori',
            'building_name' => 'Gedung Teori & Kantor',
            'floor' => 3,
            'name' => 'B301',
            'is_active' => true,
        ]);
    }

    protected function createAdminUser(): User
    {
        $role = Role::query()->create([
            'name' => 'Admin',
            'code' => 'admin',
        ]);

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
