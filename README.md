# SIAGA POLMAN K3L

Sistem informasi K3L kampus untuk pelaporan insiden, pelaporan potensi bahaya, pusat materi edukasi, pusat darurat, dan analisis tindak lanjut berbasis role.

Project ini dibangun dengan:
- Laravel 12
- PHP 8.4
- Blade
- Tailwind CSS
- Vite
- Pest

## Gambaran Umum

SIAGA POLMAN K3L dirancang untuk membantu kampus mengelola proses keselamatan kerja dan lingkungan secara lebih rapi. Sistem ini memisahkan alur kerja menjadi tiga peran utama:

- `Mahasiswa/User`
  Mengirim laporan insiden, melaporkan potensi bahaya, membaca materi K3L, memantau status laporan, dan melihat aktivitas akun.
- `Satgas`
  Memverifikasi laporan, menindaklanjuti hazard dan insiden, membuat materi edukasi, serta menganalisis pola temuan melalui dashboard analitik.
- `Admin`
  Mengelola akun, master data, knowledge center, emergency center, monitoring hazard, dan administrasi sistem secara keseluruhan.

## Fitur Utama

### 1. Autentikasi dan Role
- Login dengan `email` atau `username`
- Registrasi pengguna baru sebagai `mahasiswa`
- Redirect dashboard otomatis sesuai role
- Proteksi akun nonaktif

### 2. Modul User
- Dashboard ringkasan laporan dan knowledge
- Buat laporan insiden
- Buat laporan potensi bahaya
- Riwayat laporan insiden
- Status pelaporan insiden
- Riwayat hazard report
- Pusat darurat
- Knowledge center
- Aktivitas saya
- Profil dan edit profil

### 3. Modul Satgas
- Dashboard analitik temuan dan tindak lanjut
- Review laporan insiden
- Verifikasi status insiden
- Tindak lanjut insiden
- Review hazard report
- Update status hazard
- Tambah laporan insiden internal
- Tambah hazard report internal
- Kelola materi knowledge

### 4. Modul Admin
- Dashboard administrasi sistem
- Kelola akun semua role
- Kelola lokasi
- Kelola kategori insiden
- Kelola kategori knowledge
- Kelola artikel knowledge
- Tambah laporan insiden internal
- Tambah hazard report internal
- Monitoring hazard
- Kelola emergency contact
- Kelola emergency response step
- Kelola first aid guide

### 5. Knowledge Center
- Artikel knowledge dengan cover image
- Konten materi berbasis section
- Section dapat berisi teks, list, gambar, dan video
- Preview materi saat create/edit
- Tampilan show materi lebih lebar dan editorial

### 6. UX Tambahan
- Navbar floating
- Filter dan pencarian realtime
- Popup konfirmasi delete
- Layout admin dan satgas diseragamkan dengan sistem user

## Alur Sistem

### Alur Insiden
1. User, admin, atau satgas membuat laporan insiden.
2. Laporan masuk dengan status `submitted`.
3. Satgas membuka detail laporan dan melakukan verifikasi.
4. Status dapat bergerak ke `verified`, `investigating`, `resolved`, lalu `closed`.
5. Aktivitas pelaporan tercatat di activity log.
6. User dapat memantau progres melalui halaman status pelaporan.

### Alur Hazard
1. User, admin, atau satgas membuat hazard report.
2. Laporan masuk dengan status `submitted`.
3. Satgas meninjau dan mengubah status ke `reviewed` atau `resolved`.
4. Hazard tampil pada dashboard monitoring dan analitik.

### Alur Knowledge
1. Admin atau satgas membuat materi.
2. Materi dapat disimpan sebagai `draft`, `review`, `published`, atau `archived`.
3. Materi published tampil di knowledge center user.

### Alur Emergency Center
1. Admin mengelola kontak darurat, panduan pertolongan pertama, dan langkah tanggap.
2. User mengakses pusat darurat saat membutuhkan informasi cepat.

## Struktur Role dan Akses

### User
- `user.dashboard`
- `user.incidents.*`
- `user.hazards.*`
- `user.knowledge.*`
- `user.activities.*`
- `user.profile.*`
- `user.emergency.index`

### Satgas
- `satgas.dashboard`
- `satgas.incidents.*`
- `satgas.hazards.*`
- `satgas.knowledge-articles.*`

### Admin
- `admin.dashboard`
- `admin.users.*`
- `admin.locations.*`
- `admin.incident-categories.*`
- `admin.knowledge-categories.*`
- `admin.knowledge-articles.*`
- `admin.hazards.*`
- `admin.emergency-contacts.*`
- `admin.emergency-response-steps.*`
- `admin.first-aid-guides.*`

## Instalasi Lokal

### 1. Clone repository
```bash
git clone <repository-url>
cd TA_K3L
```

### 2. Install dependency backend
```bash
composer install
```

### 3. Install dependency frontend
```bash
npm install
```

### 4. Siapkan environment
```bash
cp .env.example .env
php artisan key:generate
```

Jika kamu memakai Windows PowerShell dan `cp` tidak tersedia, gunakan:
```powershell
Copy-Item .env.example .env
php artisan key:generate
```

### 5. Atur database
Sesuaikan `.env` dengan database lokal kamu.

Contoh default di `.env.example` saat ini mengarah ke MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=sistem_k3l
DB_USERNAME=root
DB_PASSWORD=
```

### 6. Migrasi dan seed
```bash
php artisan migrate:fresh --seed
```

### 7. Buat symbolic link storage
```bash
php artisan storage:link
```

### 8. Jalankan aplikasi

Backend:
```bash
php artisan serve
```

Frontend dev server:
```bash
npm run dev
```

Atau gunakan script bawaan:
```bash
composer run dev
```

## Akun Default Seeder

Setelah menjalankan `php artisan migrate:fresh --seed`, akun default berikut tersedia:

### Admin
- Email: `admin@k3l.local`
- Username: `admin.k3l`
- Password: `password`

### Satgas
- Email: `satgas@k3l.local`
- Username: `satgas.k3l`
- Password: `password`

### Mahasiswa
- Email: `mahasiswa@k3l.local`
- Username: `mhs.k3l`
- Password: `password`

## Cara Penggunaan

### Sebagai Mahasiswa
1. Login atau registrasi akun.
2. Buka dashboard user.
3. Pilih `Buat Laporan Insiden` atau `Buat Hazard Report`.
4. Isi form pelaporan dan kirim.
5. Pantau update di `Status Pelaporan`, `Riwayat Hazard`, dan `Aktivitas Saya`.
6. Buka `Knowledge Center` untuk membaca materi edukasi.
7. Buka `Emergency Center` jika membutuhkan bantuan darurat atau panduan cepat.

### Sebagai Satgas
1. Login dengan akun satgas.
2. Buka dashboard satgas untuk melihat analitik dan rekomendasi otomatis.
3. Review laporan insiden yang masuk.
4. Verifikasi dan ubah status sesuai tindak lanjut lapangan.
5. Review hazard report dan ubah status penanganan.
6. Tambahkan materi knowledge bila diperlukan.
7. Buat laporan internal jika Satgas menemukan insiden atau bahaya sendiri.

### Sebagai Admin
1. Login dengan akun admin.
2. Kelola akun, lokasi, kategori, dan konten knowledge.
3. Kelola konten emergency center.
4. Pantau hazard report dan administrasi data sistem.
5. Buat laporan internal bila diperlukan.

## Testing

Project ini sudah memiliki automated test untuk unit dan feature.

### Menjalankan semua test
```bash
php artisan test
```

### Menjalankan build frontend
```bash
npm run build
```

### Menjalankan cache Blade check
```bash
php artisan view:cache
```

### Validasi Composer
```bash
composer validate --strict
```

## GitHub Actions

Workflow CI ada di:
- [.github/workflows/ci.yml](</c:/Users/Hafizh/Documents/Tugas Akhir/TA_K3L/.github/workflows/ci.yml:1>)

CI saat ini menjalankan:
- health check Laravel
- build frontend Vite
- backend test suite
- compile check Blade template

## Catatan Teknis

- Frontend asset dibangun dengan `Vite`
- Testing memakai `Pest`
- Test environment memakai `SQLite in-memory`
- Upload cover knowledge dan lampiran memerlukan `php artisan storage:link`
- Workflow CI memakai `PHP 8.4`

## Struktur Folder Penting

- `app/Http/Controllers`
  Logika controller per role
- `app/Actions`
  Action class untuk proses bisnis seperti create incident dan update status
- `app/Support`
  Penyusun data dashboard dan helper internal
- `resources/views`
  Blade view untuk user, satgas, admin, dan partial bersama
- `routes/web.php`
  Definisi route utama
- `database/seeders`
  Seeder role, reference data, knowledge, emergency center, dan akun default
- `tests`
  Unit test dan feature test

## Pengembangan Lanjutan yang Disarankan

- Tambahkan file `.env.testing` jika ingin memisahkan konfigurasi testing lokal
- Pisahkan workflow CI untuk `code-style` jika ingin `Pint` dijalankan terpisah dari functional test
- Tambahkan dokumentasi ERD dan screenshot antarmuka bila README ingin dijadikan dokumen TA yang lebih formal

## Lisensi

Project ini mengikuti lisensi yang digunakan oleh framework dan dependency terkait. Jika project ini dipakai untuk kebutuhan institusi atau tugas akhir, sesuaikan bagian lisensi dengan kebijakan kampus atau tim pengembang.
