<?php

namespace App\Support\Reports;

use App\Models\BodyPart;
use App\Models\IncidentCategory;
use App\Models\InjuryCategory;
use App\Models\Location;
use Illuminate\Support\Facades\Schema;

class ReportFormOptions
{
    public function incident(): array
    {
        return [
            'categories' => IncidentCategory::query()->orderBy('name')->get(),
            'locations' => $this->locations(),
            'injuryCategories' => InjuryCategory::query()->orderBy('name')->get(),
            'bodyParts' => BodyPart::query()->orderBy('name')->get(),
            'severityOptions' => [
                'low' => 'Rendah',
                'medium' => 'Sedang',
                'high' => 'Tinggi',
                'critical' => 'Kritis',
            ],
            'victimPositionOptions' => [
                'mahasiswa' => 'Mahasiswa',
                'karyawan' => 'Karyawan',
                'publik' => 'Publik',
                'kontraktor' => 'Kontraktor',
                'pengunjung' => 'Pengunjung',
            ],
            'unsafeConditionOptions' => [
                'pengamanan_tidak_memadai' => 'Pengamanan yang tidak memadai',
                'tidak_ada_pengamanan_lokasi_berbahaya' => 'Tidak ada pengamanan pada lokasi berbahaya',
                'apd_cacat' => 'Alat pelindung diri yang cacat',
                'alat_kerja_cacat' => 'Alat kerja yang cacat',
                'area_kerja_berbahaya' => 'Area kerja yang berbahaya',
                'pencahayaan_tidak_memadai' => 'Pencahayaan tidak memadai',
                'ventilasi_tidak_memadai' => 'Ventilasi tidak memadai',
                'kurang_apd' => 'Kurangnya alat pelindung diri (APD)',
                'kurang_alat_kerja' => 'Kurangnya alat kerja yang memadai',
                'pakaian_tidak_aman' => 'Pakaian yang tidak aman',
                'kurang_pelatihan' => 'Tidak ada atau kurangnya pelatihan kerja',
                'lain_lain' => 'Lain-lain',
            ],
            'unsafeActionOptions' => [
                'pengoperasian_tanpa_ijin' => 'Pengoperasian tanpa ijin',
                'kecepatan_tidak_terkendali' => 'Pengoperasian dengan kecepatan tidak terkendali',
                'alat_pengaman_tidak_berfungsi' => 'Menyebabkan alat pengaman tidak berfungsi',
                'menggunakan_alat_cacat' => 'Menggunakan alat kerja yang cacat',
                'penggunaan_alat_tidak_aman' => 'Penggunaan alat kerja dengan cara tidak aman',
                'pengangkatan_tidak_aman' => 'Pengangkatan tidak aman',
                'posisi_kerja_tidak_aman' => 'Menyebabkan posisi kerja tidak aman',
                'pengalih_perhatian' => 'Pengalih perhatian atau bercanda saat bekerja',
                'tidak_menggunakan_apd' => 'Tidak menggunakan alat pelindung diri (APD)',
                'tidak_menggunakan_alat_tersedia' => 'Tidak menggunakan alat kerja yang tersedia',
                'lain_lain' => 'Lain-lain',
            ],
            'preventionOptions' => [
                'hentikan_aktivitas' => 'Hentikan aktivitas',
                'pengamanan_sumber_bahaya' => 'Beri pengamanan pada sumber bahaya',
                'rancang_ulang_langkah_kerja' => 'Rancang ulang langkah kerja',
                'kebijakan_baru' => 'Buat kebijakan / peraturan baru',
                'inspeksi_rutin' => 'Inspeksi rutin pada sumber bahaya',
                'pelatihan_tenaga_kerja' => 'Beri pelatihan pada tenaga kerja',
                'pelatihan_pengawas' => 'Beri pelatihan pada pengawas kerja',
                'rancang_ulang_tempat_kerja' => 'Rancang ulang tempat kerja',
                'perkuat_kebijakan' => 'Perkuat penerapan kebijakan yang sudah ada',
                'penggunaan_apd' => 'Penggunaan alat pelindung diri (APD)',
                'lain_lain' => 'Lain-lain',
            ],
        ];
    }

    public function hazard(): array
    {
        $hazardTypes = [
            ['key' => 'lingkungan', 'label' => 'Lingkungan', 'icon' => 'eco'],
            ['key' => 'peralatan', 'label' => 'Peralatan', 'icon' => 'construction'],
            ['key' => 'listrik', 'label' => 'Listrik', 'icon' => 'bolt'],
            ['key' => 'zat-kimia', 'label' => 'Zat Kimia', 'icon' => 'science'],
        ];

        return [
            'locations' => $this->locations(),
            'hazardTypes' => $hazardTypes,
            'selectedHazardType' => old('hazard_type', $hazardTypes[0]['key']),
        ];
    }

    public function combined(): array
    {
        return array_merge($this->incident(), $this->hazard());
    }

    protected function locations()
    {
        $locations = collect();

        if (Schema::hasTable('locations')) {
            $locations = Location::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        if ($locations->isEmpty()) {
            $locations = collect([
                (object) ['id' => 1, 'name' => 'Bengkel Manufaktur'],
                (object) ['id' => 2, 'name' => 'Laboratorium Elektronika'],
                (object) ['id' => 3, 'name' => 'Workshop Material'],
            ]);
        }

        return $locations;
    }
}
