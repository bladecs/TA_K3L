<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Support\Dashboard\AdminDashboardData;
use App\Support\Dashboard\SatgasDashboardData;
use Tests\TestCase;

class AuthorizationFeatureTest extends TestCase
{
    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $mahasiswaRole = new Role([
            'name' => 'Mahasiswa',
            'code' => 'mahasiswa',
        ]);

        $user = User::factory()->make(['id' => 101]);
        $user->setRelation('role', $mahasiswaRole);

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_regular_user_cannot_access_satgas_dashboard(): void
    {
        $mahasiswaRole = new Role([
            'name' => 'Mahasiswa',
            'code' => 'mahasiswa',
        ]);

        $user = User::factory()->make(['id' => 102]);
        $user->setRelation('role', $mahasiswaRole);

        $this->actingAs($user)
            ->get(route('satgas.dashboard'))
            ->assertForbidden();
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $this->instance(AdminDashboardData::class, new class extends AdminDashboardData
        {
            public function build(): array
            {
                return [
                    'stats' => [
                        'total_users' => 0,
                        'active_users' => 0,
                        'satgas_count' => 0,
                        'incident_count' => 0,
                        'submitted_incidents' => 0,
                        'verified_incidents' => 0,
                        'location_count' => 0,
                        'role_count' => 0,
                        'published_knowledge' => 0,
                        'hazard_reports' => 0,
                        'reviewed_hazards' => 0,
                        'resolved_hazards' => 0,
                        'emergency_contacts' => 0,
                    ],
                    'recentReports' => collect(),
                    'recentHazardReports' => collect(),
                    'operationalSummary' => [
                        'incident_backlog' => 0,
                        'hazard_backlog' => 0,
                        'published_knowledge' => 0,
                        'active_emergency_contacts' => 0,
                    ],
                ];
            }
        });

        $adminRole = new Role([
            'name' => 'Admin',
            'code' => 'admin',
        ]);

        $admin = User::factory()->make(['id' => 201]);
        $admin->setRelation('role', $adminRole);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSeeText('Dashboard Admin');
    }

    public function test_satgas_can_access_satgas_dashboard(): void
    {
        $this->instance(SatgasDashboardData::class, new class extends SatgasDashboardData
        {
            public function build(string $period = '180'): array
            {
                return [
                    'period' => $period,
                    'periodLabel' => '6 bulan terakhir',
                    'stats' => [
                        'submitted_incidents' => 0,
                        'verified_incidents' => 0,
                        'investigating_incidents' => 0,
                        'resolved_incidents' => 0,
                        'closed_incidents' => 0,
                        'critical_incidents' => 0,
                        'total_hazards' => 0,
                    ],
                    'priorityReports' => collect(),
                    'incidentStatusSummary' => [
                        'completed' => 0,
                        'in_progress' => 0,
                        'pending' => 0,
                    ],
                    'hazardStatusSummary' => [
                        'completed' => 0,
                        'in_progress' => 0,
                        'pending' => 0,
                    ],
                    'severityBreakdown' => collect([
                        ['key' => 'low', 'label' => 'LOW', 'count' => 0],
                        ['key' => 'medium', 'label' => 'MEDIUM', 'count' => 0],
                        ['key' => 'high', 'label' => 'HIGH', 'count' => 0],
                        ['key' => 'critical', 'label' => 'CRITICAL', 'count' => 0],
                    ]),
                    'hazardTypeBreakdown' => collect([
                        ['key' => 'lingkungan', 'label' => 'Lingkungan', 'count' => 0],
                        ['key' => 'peralatan', 'label' => 'Peralatan', 'count' => 0],
                        ['key' => 'listrik', 'label' => 'Listrik', 'count' => 0],
                        ['key' => 'zat-kimia', 'label' => 'Zat Kimia', 'count' => 0],
                    ]),
                    'monthlyTrend' => collect([
                        ['label' => 'Apr 2026', 'incidents' => 0, 'hazards' => 0],
                    ]),
                    'locationInsights' => collect(),
                    'sourceBreakdown' => [
                        'incidents' => ['user' => 0, 'internal' => 0],
                        'hazards' => ['user' => 0, 'internal' => 0],
                    ],
                    'recommendations' => collect([
                        [
                            'title' => 'Belum ada pola dominan',
                            'description' => 'Data pada periode ini masih minim.',
                            'icon' => 'insights',
                            'tone' => 'text-slate-700 bg-slate-100',
                        ],
                    ]),
                    'workloadSummary' => [
                        'needs_review' => 0,
                        'needs_field_follow_up' => 0,
                        'ready_to_close' => 0,
                    ],
                ];
            }
        });

        $satgasRole = new Role([
            'name' => 'Satgas',
            'code' => 'satgas',
        ]);

        $satgas = User::factory()->make(['id' => 202]);
        $satgas->setRelation('role', $satgasRole);

        $this->actingAs($satgas)
            ->get(route('satgas.dashboard'))
            ->assertOk()
            ->assertSeeText('Dashboard Satgas');
    }
}
