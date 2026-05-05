# SIAGA POLMAN K3L

Sistem informasi K3L kampus untuk pelaporan insiden, pelaporan potensi bahaya, pemetaan GIS area rawan, edukasi K3L, pusat darurat, dan tindak lanjut laporan oleh Satgas.

Project ini dibangun dengan Laravel 12, PHP 8.4, Blade, Tailwind CSS, Vite, dan Pest.

## Gambaran Umum

SIAGA POLMAN K3L membagi alur kerja menjadi tiga area:

- `Portal Publik`: halaman informasi umum, knowledge center, pusat darurat, form laporan insiden, form laporan hazard, peta GIS area rawan, dan cek status laporan. Bagian ini tidak membutuhkan login kecuali riwayat laporan pribadi.
- `Satgas`: memverifikasi laporan, menentukan klasifikasi insiden, menindaklanjuti hazard/insiden, menambahkan titik area rawan di GIS, mengelola artikel knowledge, dan mengubah status laporan.
- `Admin`: mengelola akun, konten emergency center, dan memantau hazard secara read-only. Admin tidak mengelola teori/knowledge, insiden, kategori insiden, lokasi, atau lokasi hazard.

## Fitur Utama

### Portal Publik

- Landing page informasi K3L.
- Form laporan insiden tanpa login.
- Form laporan potensi bahaya tanpa login.
- Input pelapor: nama, email, dan nomor WhatsApp aktif.
- Cek status laporan insiden berdasarkan nomor laporan atau status.
- Knowledge center untuk materi K3L yang sudah published.
- Emergency center berisi kontak darurat, langkah tanggap darurat, dan panduan P3K.
- Voice to text pada detail kejadian insiden.
- Peta GIS area rawan kampus pada `GET /user/hazard-map`, termasuk titik dari map satelit dan denah kampus.

### Satgas

- Dashboard Satgas.
- Profil Satgas.
- Review laporan insiden dan hazard.
- Verifikasi laporan insiden.
- Menentukan jenis insiden, level dampak, kategori cedera, bagian tubuh, dan dampak saat verifikasi.
- Update status insiden sampai `closed`.
- Tambah tindak lanjut insiden.
- Update status hazard.
- Peta GIS Satgas pada `GET /satgas/hazards/map`.
- Tambah titik area rawan langsung dari peta GIS, tanpa harus menunggu laporan hazard.
- Pilih titik dari map satelit atau foto denah kampus Polman.
- Simpan level risiko, kategori hazard, dan catatan area rawan.
- Pinpoint laporan hazard dari detail laporan bila titik tersebut berasal dari laporan masuk.
- Buat laporan internal Satgas.
- Kelola artikel knowledge.

### Admin

- Dashboard administrasi.
- Kelola akun pengguna.
- Kelola emergency contact.
- Kelola emergency response step.
- Kelola first aid guide.
- Monitoring hazard read-only.

## Alur Insiden

1. Pelapor publik mengisi form insiden dengan nama, email, WhatsApp, lokasi, tanggal kejadian, kronologi, penyebab, dan tindakan awal.
2. Sistem membuat nomor laporan dan mengirim notifikasi WhatsApp ke Satgas.
3. Satgas membuka detail laporan, lalu menentukan klasifikasi saat verifikasi.
4. Status dapat bergerak dari `submitted`, `verified`, `investigating`, `resolved`, sampai `closed`.
5. Pelapor dapat mengecek progres melalui halaman status laporan.

Field yang tidak diisi oleh pelapor publik karena menjadi wewenang Satgas:

- jenis insiden
- level dampak
- kategori cedera
- bagian tubuh
- dampak akhir
- jam kejadian

## Alur Hazard

1. Pelapor publik mengisi form hazard dengan nama, email, WhatsApp, tipe hazard, lokasi, detail lokasi, dan catatan.
2. Sistem mengirim notifikasi WhatsApp ke Satgas.
3. Satgas meninjau hazard dan mengubah status ke `reviewed` atau `resolved`.
4. Satgas dapat memberi pinpoint GIS pada laporan hazard dari halaman detail laporan.
5. Admin hanya memantau hazard dari dashboard admin.

## Alur GIS Area Rawan

Fitur GIS dipakai Satgas untuk menandai area bahaya atau rawan di lingkungan kampus. Titik GIS tidak harus berasal dari laporan hazard publik; Satgas bisa membuat titik mandiri berdasarkan inspeksi lapangan, temuan rutin, atau pemetaan area yang perlu diwaspadai.

### Tambah Titik Mandiri

1. Login sebagai Satgas.
2. Buka `GET /satgas/hazards/map`.
3. Isi panel **Tambah Titik Area Rawan**:
   - nama titik rawan
   - kategori hazard, opsional
   - level risiko: `rendah`, `sedang`, `tinggi`, atau `kritis`
   - catatan singkat
4. Klik **Pilih Titik Satelit** lalu klik lokasi di map satelit, atau klik **Pilih Titik Denah** lalu klik lokasi di denah kampus.
5. Klik **Simpan Titik**.
6. Titik tersimpan di tabel `hazard_map_points` dan tampil untuk Satgas maupun user umum.

### Pinpoint Laporan Hazard

1. Login sebagai Satgas.
2. Buka `GET /satgas/hazards`.
3. Klik **Tinjau** pada laporan hazard.
4. Pada bagian **Pinpoint GIS Hazard**, pilih titik di map satelit atau denah kampus.
5. Klik **Simpan Pinpoint**.
6. Koordinat tersimpan di `potential_hazard_reports`.

### Sumber Peta

- Map satelit memakai Leaflet dan Esri World Imagery, sehingga tidak membutuhkan API key.
- Denah kampus memakai asset lokal `public/img/campus-denah/20260430_144208.jpg`.
- Titik satelit menyimpan `latitude` dan `longitude`.
- Titik denah menyimpan `floorplan_x` dan `floorplan_y`, yaitu koordinat relatif pada gambar denah.

## WhatsApp Notification

Integrasi WhatsApp memakai Fonnte. Variabel yang perlu disiapkan di `.env`:

```env
QUEUE_CONNECTION=sync
FONNTE_ENABLED=true
FONNTE_DEVICE_TOKEN=
FONNTE_ACCOUNT_TOKEN=
FONNTE_VERIFY_SSL=true
```

Untuk development lokal yang terkena masalah sertifikat cURL, `FONNTE_VERIFY_SSL=false` bisa dipakai sementara. Token asli jangan dimasukkan ke repository.

Dengan `QUEUE_CONNECTION=sync`, pengiriman notifikasi dilakukan langsung saat request diproses. Ini mudah untuk demo dan debugging, tetapi response form bisa lebih lambat kalau API WhatsApp sedang lambat.

## Instalasi Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
```

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
php artisan storage:link
```

Jalankan aplikasi:

```bash
php artisan serve
npm run dev
```

Atau gunakan script bawaan:

```bash
composer run dev
```

## Akun Default Seeder

Setelah menjalankan `php artisan migrate:fresh --seed`, akun default berikut tersedia:

| Role | Email | Username | Password |
| --- | --- | --- | --- |
| Admin | `admin@k3l.local` | `admin.k3l` | `password` |
| Satgas | `satgas@k3l.local` | `satgas.k3l` | `password` |
| Mahasiswa | `mahasiswa@k3l.local` | `mhs.k3l` | `password` |

Portal publik tetap bisa digunakan tanpa login.

## Dokumentasi Testing Postman

Base URL default:

```text
{{base_url}} = http://127.0.0.1:8000
```

CSV testcase ada di:

- [docs/postman-testing-routes.csv](</c:/Users/Hafizh/Documents/Tugas Akhir/TA_K3L/docs/postman-testing-routes.csv:1>)

Environment variable yang disarankan di Postman:

- `base_url`: `http://127.0.0.1:8000`
- `csrf_token`: dikosongkan dulu
- `incident_id`: contoh `1`
- `hazard_id`: contoh `1`
- `user_id`: contoh `1`
- `location_id`: contoh `1`
- `incident_category_id`: contoh `1`
- `injury_category_id`: contoh `1`
- `body_part_id`: contoh `1`
- `knowledge_article_id`: contoh `1`
- `knowledge_slug`: contoh `panduan-dasar-apd-area-praktikum`
- `emergency_contact_id`: contoh `1`
- `emergency_response_step_id`: contoh `1`
- `first_aid_guide_id`: contoh `1`

Route project ini memakai web route Laravel. Request `POST`, `PATCH`, `PUT`, dan `DELETE` membutuhkan session cookie dan CSRF token. Ambil token dari halaman form terkait atau halaman login, lalu kirim sebagai field `_token`.

Script test umum Postman:

```javascript
pm.test('Response tidak error', function () {
    pm.expect(pm.response.code).to.be.below(500);
});

pm.test('Status web valid', function () {
    pm.expect(pm.response.code).to.be.oneOf([200, 302, 403, 404, 422]);
});
```

## Route Aktif

### Public

- `GET /`
- `GET|POST /login`
- `GET|POST /register`
- `POST /logout`
- `GET /up`
- `GET /user/dashboard`
- `GET /user/emergency-center`
- `GET /user/knowledge-center`
- `GET /user/knowledge-center/module/{slug}`
- `GET /user/hazard-map`
- `GET /user/hazard-reports`
- `GET|POST /user/hazard-reports/create`
- `GET /user/hazard-reports/{potentialHazardReport}`
- `GET|POST /user/incidents/create`
- `GET /user/incidents/status`
- `GET /user/incidents/{incidentReport}`

### Admin

- `GET /admin/dashboard`
- `GET|POST /admin/users`
- `GET /admin/users/create`
- `GET|PUT|PATCH /admin/users/{user}/edit`
- `GET /admin/hazards`
- `GET /admin/hazards/{potentialHazardReport}`
- CRUD emergency contacts
- CRUD emergency response steps
- CRUD first aid guides

### Satgas

- `GET /satgas/dashboard`
- `GET|PATCH /satgas/profile`
- `GET|POST /satgas/incidents`
- `GET /satgas/incidents/create`
- `GET /satgas/incidents/{incidentReport}`
- `PATCH /satgas/incidents/{incidentReport}/verify`
- `PATCH /satgas/incidents/{incidentReport}/status`
- `POST /satgas/incidents/{incidentReport}/follow-ups`
- `GET|POST /satgas/hazards`
- `GET /satgas/hazards/create`
- `GET /satgas/hazards/map`
- `POST /satgas/hazards/map-points`
- `GET /satgas/hazards/{potentialHazardReport}`
- `PATCH /satgas/hazards/{potentialHazardReport}/pinpoint`
- `PATCH /satgas/hazards/{potentialHazardReport}/status`
- CRUD satgas knowledge articles

## GitHub Actions

Workflow CI ada di:

- [.github/workflows/ci.yml](</c:/Users/Hafizh/Documents/Tugas Akhir/TA_K3L/.github/workflows/ci.yml:1>)

CI menjalankan:

- Composer install dan validasi aplikasi Laravel.
- Migrasi dan seeder dengan SQLite in-memory.
- Compile route dan Blade template.
- Test fitur Satgas Hazard/GIS.
- Build frontend Vite.
- Validasi file CSV testcase Postman dan keberadaan asset denah GIS.

## Command Verifikasi Lokal

```bash
php artisan migrate
php artisan route:list --except-vendor
php artisan view:cache
php artisan test tests/Feature/SatgasPotentialHazardReviewFeatureTest.php
npm run build
```

Automated test lama mungkin perlu disesuaikan ulang dengan perubahan portal publik dan perubahan field form laporan.

## Struktur Folder Penting

- `app/Http/Controllers`: controller per role.
- `app/Actions`: proses bisnis seperti create incident dan update status.
- `app/Services`: integrasi eksternal seperti Fonnte WhatsApp.
- `app/Support`: helper dashboard dan notifikasi.
- `resources/views`: Blade view user, satgas, admin, dan partial.
- `public/img/campus-denah`: asset denah kampus untuk layer GIS berbasis gambar.
- `routes/web.php`: definisi route utama.
- `database/migrations`: struktur database.
- `database/seeders`: role, reference data, emergency center, knowledge, dan akun default.
- `docs`: dokumen testcase dan pendukung pengujian.
