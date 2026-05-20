# Hasil Pengujian Fungsi Newman

Collection: Sistem Informasi K3L - Web Route Functional Test
Total request: 79
Request gagal: 0
Total assertion: 237
Assertion gagal: 0
Failure: 0
Durasi: 37.5 detik

| No | Request | Method | URL | Code | Status |
|---|---|---|---|---|---|
| 1 | Root redirect portal | GET | http://127.0.0.1:8000/ | 200 | passed |
| 2 | Halaman login | GET | http://127.0.0.1:8000/login | 200 | passed |
| 3 | Halaman register | GET | http://127.0.0.1:8000/register | 200 | passed |
| 4 | Dashboard publik | GET | http://127.0.0.1:8000/user/dashboard | 200 | passed |
| 5 | Emergency center publik | GET | http://127.0.0.1:8000/user/emergency-center | 200 | passed |
| 6 | Knowledge center publik | GET | http://127.0.0.1:8000/user/knowledge-center | 200 | passed |
| 7 | Detail knowledge publik | GET | http://127.0.0.1:8000/user/knowledge-center/module/panduan-dasar-apd-area-praktikum | 200 | passed |
| 8 | Peta GIS publik | GET | http://127.0.0.1:8000/user/hazard-map | 200 | passed |
| 9 | Form hazard publik | GET | http://127.0.0.1:8000/user/hazard-reports/create | 200 | passed |
| 10 | Submit hazard publik | POST | http://127.0.0.1:8000/user/hazard-reports | 302 | passed |
| 11 | Form insiden publik | GET | http://127.0.0.1:8000/user/incidents/create | 200 | passed |
| 12 | Submit insiden publik | POST | http://127.0.0.1:8000/user/incidents | 302 | passed |
| 13 | Cek status insiden | GET | http://127.0.0.1:8000/user/incidents/status | 200 | passed |
| 14 | Cek status insiden dengan query | GET | http://127.0.0.1:8000/user/incidents/status?q=INC-0001 | 200 | passed |
| 15 | Detail insiden publik aman | GET | http://127.0.0.1:8000/user/incidents/1 | 200 | passed |
| 16 | Akses daftar insiden publik dialihkan login | GET | http://127.0.0.1:8000/user/incidents | 200 | passed |
| 17 | Akses daftar hazard publik dialihkan login | GET | http://127.0.0.1:8000/user/hazard-reports | 200 | passed |
| 18 | Ambil CSRF login admin | GET | http://127.0.0.1:8000/login | 200 | passed |
| 19 | Login admin | POST | http://127.0.0.1:8000/login | 302 | passed |
| 20 | Dashboard admin | GET | http://127.0.0.1:8000/admin/dashboard | 200 | passed |
| 21 | Daftar user | GET | http://127.0.0.1:8000/admin/users | 200 | passed |
| 22 | Form tambah user | GET | http://127.0.0.1:8000/admin/users/create | 200 | passed |
| 23 | Tambah user | POST | http://127.0.0.1:8000/admin/users | 302 | passed |
| 24 | Form edit user aman | GET | http://127.0.0.1:8000/admin/users/3/edit | 200 | passed |
| 25 | Daftar lokasi | GET | http://127.0.0.1:8000/admin/locations | 200 | passed |
| 26 | Form tambah lokasi | GET | http://127.0.0.1:8000/admin/locations/create | 200 | passed |
| 27 | Tambah lokasi | POST | http://127.0.0.1:8000/admin/locations | 302 | passed |
| 28 | Daftar kategori insiden | GET | http://127.0.0.1:8000/admin/incident-categories | 200 | passed |
| 29 | Form tambah kategori insiden | GET | http://127.0.0.1:8000/admin/incident-categories/create | 200 | passed |
| 30 | Tambah kategori insiden | POST | http://127.0.0.1:8000/admin/incident-categories | 302 | passed |
| 31 | Monitoring hazard admin | GET | http://127.0.0.1:8000/admin/hazards | 200 | passed |
| 32 | Detail hazard monitoring aman | GET | http://127.0.0.1:8000/admin/hazards/1 | 200 | passed |
| 33 | Daftar kategori knowledge | GET | http://127.0.0.1:8000/admin/knowledge-categories | 200 | passed |
| 34 | Form tambah kategori knowledge | GET | http://127.0.0.1:8000/admin/knowledge-categories/create | 200 | passed |
| 35 | Tambah kategori knowledge | POST | http://127.0.0.1:8000/admin/knowledge-categories | 302 | passed |
| 36 | Daftar artikel knowledge admin | GET | http://127.0.0.1:8000/admin/knowledge-articles | 200 | passed |
| 37 | Form artikel knowledge admin | GET | http://127.0.0.1:8000/admin/knowledge-articles/create | 200 | passed |
| 38 | Tambah artikel knowledge admin | POST | http://127.0.0.1:8000/admin/knowledge-articles | 302 | passed |
| 39 | Daftar kontak darurat | GET | http://127.0.0.1:8000/admin/emergency-contacts | 200 | passed |
| 40 | Form kontak darurat | GET | http://127.0.0.1:8000/admin/emergency-contacts/create | 200 | passed |
| 41 | Tambah kontak darurat | POST | http://127.0.0.1:8000/admin/emergency-contacts | 302 | passed |
| 42 | Daftar langkah tanggap darurat | GET | http://127.0.0.1:8000/admin/emergency-response-steps | 200 | passed |
| 43 | Form langkah tanggap darurat | GET | http://127.0.0.1:8000/admin/emergency-response-steps/create | 200 | passed |
| 44 | Tambah langkah tanggap darurat | POST | http://127.0.0.1:8000/admin/emergency-response-steps | 302 | passed |
| 45 | Daftar first aid | GET | http://127.0.0.1:8000/admin/first-aid-guides | 200 | passed |
| 46 | Form first aid | GET | http://127.0.0.1:8000/admin/first-aid-guides/create | 200 | passed |
| 47 | Tambah first aid | POST | http://127.0.0.1:8000/admin/first-aid-guides | 302 | passed |
| 48 | Logout admin | POST | http://127.0.0.1:8000/logout | 302 | passed |
| 49 | Ambil CSRF login satgas | GET | http://127.0.0.1:8000/login | 200 | passed |
| 50 | Login satgas | POST | http://127.0.0.1:8000/login | 302 | passed |
| 51 | Dashboard satgas | GET | http://127.0.0.1:8000/satgas/dashboard | 200 | passed |
| 52 | Profil satgas | GET | http://127.0.0.1:8000/satgas/profile | 200 | passed |
| 53 | Update profil satgas | PATCH | http://127.0.0.1:8000/satgas/profile | 302 | passed |
| 54 | Daftar insiden satgas | GET | http://127.0.0.1:8000/satgas/incidents | 200 | passed |
| 55 | Form insiden satgas | GET | http://127.0.0.1:8000/satgas/incidents/create | 200 | passed |
| 56 | Submit insiden satgas | POST | http://127.0.0.1:8000/satgas/incidents | 302 | passed |
| 57 | Detail insiden satgas aman | GET | http://127.0.0.1:8000/satgas/incidents/15 | 200 | passed |
| 58 | Verifikasi insiden aman | PATCH | http://127.0.0.1:8000/satgas/incidents/15/verify | 302 | passed |
| 59 | Update status insiden aman | PATCH | http://127.0.0.1:8000/satgas/incidents/15/status | 302 | passed |
| 60 | Tambah follow up insiden aman | POST | http://127.0.0.1:8000/satgas/incidents/15/follow-ups | 302 | passed |
| 61 | Daftar hazard satgas | GET | http://127.0.0.1:8000/satgas/hazards | 200 | passed |
| 62 | Form hazard satgas | GET | http://127.0.0.1:8000/satgas/hazards/create | 200 | passed |
| 63 | Submit hazard satgas | POST | http://127.0.0.1:8000/satgas/hazards | 302 | passed |
| 64 | Peta hazard satgas | GET | http://127.0.0.1:8000/satgas/hazards/map | 200 | passed |
| 65 | Tambah titik peta hazard | POST | http://127.0.0.1:8000/satgas/hazards/map-points | 302 | passed |
| 66 | Detail hazard satgas aman | GET | http://127.0.0.1:8000/satgas/hazards/14 | 200 | passed |
| 67 | Update status hazard aman | PATCH | http://127.0.0.1:8000/satgas/hazards/14/status | 302 | passed |
| 68 | Daftar artikel knowledge satgas | GET | http://127.0.0.1:8000/satgas/knowledge-articles | 200 | passed |
| 69 | Form artikel knowledge satgas | GET | http://127.0.0.1:8000/satgas/knowledge-articles/create | 200 | passed |
| 70 | Tambah artikel knowledge satgas | POST | http://127.0.0.1:8000/satgas/knowledge-articles | 302 | passed |
| 71 | Logout satgas | POST | http://127.0.0.1:8000/logout | 302 | passed |
| 72 | Admin route tanpa login dialihkan | GET | http://127.0.0.1:8000/admin/dashboard | 200 | passed |
| 73 | Satgas route tanpa login dialihkan | GET | http://127.0.0.1:8000/satgas/dashboard | 200 | passed |
| 74 | Ambil CSRF login mahasiswa | GET | http://127.0.0.1:8000/login | 200 | passed |
| 75 | Login mahasiswa | POST | http://127.0.0.1:8000/login | 302 | passed |
| 76 | Mahasiswa tidak boleh akses admin | GET | http://127.0.0.1:8000/admin/dashboard | 403 | passed |
| 77 | Mahasiswa tidak boleh akses satgas | GET | http://127.0.0.1:8000/satgas/dashboard | 403 | passed |
| 78 | Ambil CSRF dashboard mahasiswa | GET | http://127.0.0.1:8000/user/dashboard | 200 | passed |
| 79 | Logout mahasiswa | POST | http://127.0.0.1:8000/logout | 302 | passed |
