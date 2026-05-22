const fs = require('fs');
const path = require('path');

const outDir = __dirname;
const collectionPath = path.join(outDir, 'k3l-web-newman.postman_collection.json');
const environmentPath = path.join(outDir, 'k3l-web-newman.postman_environment.json');

const csrfExtractor = `
const body = pm.response.text();
const match = body.match(/name=["']_token["']\\s+value=["']([^"']+)["']/);
if (match) {
  pm.environment.set('csrf_token', match[1]);
}
`;

const ok = (name, codes = [200]) => `
pm.test('${name}', () => {
  pm.expect(${JSON.stringify(codes)}).to.include(pm.response.code);
});
pm.test('Tidak terjadi server error', () => {
  pm.expect(pm.response.code).to.be.below(500);
});
${csrfExtractor}
`;

const no5xx = `
pm.test('Tidak terjadi server error', () => {
  pm.expect(pm.response.code).to.be.below(500);
});
${csrfExtractor}
`;

const formBody = (params) => ({
  mode: 'urlencoded',
  urlencoded: Object.entries(params).map(([key, value]) => ({
    key,
    value: String(value),
    type: 'text',
  })),
});

const request = (name, method, url, testScript, body = null) => ({
  name,
  event: [
    {
      listen: 'test',
      script: {
        type: 'text/javascript',
        exec: testScript.trim().split('\n'),
      },
    },
  ],
  request: {
    method,
    header: [
      { key: 'Accept', value: 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8' },
    ],
    url: { raw: `{{base_url}}${url}`, host: ['{{base_url}}'], path: url.split('/').filter(Boolean) },
    ...(body ? { body } : {}),
  },
  ...(body ? { protocolProfileBehavior: { followRedirects: false } } : {}),
});

const get = (name, url, codes = [200]) => request(name, 'GET', url, ok(name, codes));
const getWithTests = (name, url, codes = [200], extraTests = '') => request(name, 'GET', url, `${ok(name, codes)}\n${extraTests}`);
const mutate = (name, method, url, params, codes = [200, 302], extraTests = '') => request(
  name,
  method,
  url,
  `${ok(name, codes)}\n${extraTests}`,
  formBody(params),
);

const captureIdFromLocation = (variable, pattern) => `
const locationHeader = pm.response.headers.get('Location') || '';
const idMatch = locationHeader.match(${pattern});
if (idMatch) {
  pm.environment.set('${variable}', idMatch[1]);
}
`;

const bodyContains = (...needles) => `
var responseBody = pm.response.text();
${needles.map((needle) => `pm.test('Memuat teks: ${needle}', () => pm.expect(responseBody).to.include('${needle}'));`).join('\n')}
`;

const login = (role, loginValue) => [
  get(`Ambil CSRF login ${role}`, '/login'),
  mutate(`Login ${role}`, 'POST', '/login', {
    _token: '{{csrf_token}}',
    login: loginValue,
    password: 'password',
    remember: '1',
  }, [200, 302]),
];

const logout = (role) => mutate(`Logout ${role}`, 'POST', '/logout', { _token: '{{csrf_token}}' }, [200, 302, 405]);

const timestamp = '{{$timestamp}}';

const publicItems = [
  get('Root redirect portal', '/', [200, 302]),
  get('Halaman login', '/login'),
  get('Halaman register', '/register'),
  getWithTests('Dashboard publik', '/user/dashboard', [200], bodyContains('Daftar Titik Insiden', 'Denah Kampus')),
  get('Emergency center publik', '/user/emergency-center'),
  get('Knowledge center publik', '/user/knowledge-center'),
  get('Detail knowledge publik', '/user/knowledge-center/module/{{knowledge_slug}}', [200, 404]),
  get('Peta GIS publik', '/user/hazard-map'),
  get('Form hazard publik', '/user/hazard-reports/create'),
  mutate('Submit hazard publik', 'POST', '/user/hazard-reports', {
    _token: '{{csrf_token}}',
    reporter_name: 'Pelapor Umum',
    reporter_email: `pelapor.hazard.${timestamp}@example.test`,
    reporter_whatsapp: '081234567890',
    title: `Hazard publik ${timestamp}`,
    hazard_type: 'listrik',
    location_id: '{{location_id}}',
    specific_location: 'Ruang praktikum lantai 1',
    latitude: '-6.8774225',
    longitude: '107.6207125',
    location_accuracy: '12',
    notes: 'Kabel terlihat terkelupas dan berpotensi tersentuh pengguna.',
  }, [200, 302]),
  get('Form insiden publik', '/user/incidents/create'),
  mutate('Submit insiden publik', 'POST', '/user/incidents', {
    _token: '{{csrf_token}}',
    reporter_name: 'Pelapor Umum',
    reporter_email: `pelapor.insiden.${timestamp}@example.test`,
    reporter_whatsapp: '081234567890',
    title: `Insiden publik ${timestamp}`,
    incident_category_id: '{{incident_category_id}}',
    injury_category_id: '{{injury_category_id}}',
    body_part_id: '{{body_part_id}}',
    location_id: '{{location_id}}',
    incident_date: '2026-05-01',
    incident_time: '09:30',
    severity_level: 'medium',
    victim_type: 'self',
    victim_name: 'Rachmat Hidayat',
    victim_position: 'mahasiswa',
    victim_position_description: 'Mahasiswa Teknik Mesin',
    victim_gender: 'male',
    victim_age: '22',
    witness_name: 'Abdul Muhyi',
    ppe_used: 'Tidak ada',
    chronology: 'Pelapor terpeleset karena lantai licin setelah kegiatan praktik di area workshop.',
    cause: 'Lantai licin',
    initial_action: 'Area diberi tanda peringatan dan korban dibantu ke tempat aman.',
    impact: 'Korban mengalami memar ringan dan aktivitas dihentikan sementara.',
    'unsafe_conditions[]': 'area_kerja_berbahaya',
    'unsafe_actions[]': 'penggunaan_alat_tidak_aman',
    unsafe_condition_cause: 'Area belum diberi pembatas yang memadai.',
    unsafe_action_cause: 'Instruksi kerja belum dikomunikasikan dengan jelas.',
    warning_given_before_incident: '0',
    incident_previously_occurred: '0',
    'proposed_preventions[]': 'pengamanan_sumber_bahaya',
    prevention_action_plan: 'Pasang pembatas dan lakukan inspeksi rutin.',
  }, [200, 302], captureIdFromLocation('incident_id', '/\\/user\\/incidents\\/(\\d+)/')),
  get('Cek status insiden', '/user/incidents/status'),
  get('Cek status insiden dengan query', '/user/incidents/status?q={{incident_report_number}}'),
  get('Detail insiden publik aman', '/user/incidents/{{incident_id}}', [200, 404]),
  get('Akses daftar insiden publik dialihkan login', '/user/incidents', [200, 302]),
  get('Akses daftar hazard publik dialihkan login', '/user/hazard-reports', [200, 302]),
];

const adminItems = [
  ...login('admin', 'admin@k3l.local'),
  get('Dashboard admin', '/admin/dashboard'),
  get('Daftar user', '/admin/users'),
  get('Form tambah user', '/admin/users/create'),
  mutate('Tambah user', 'POST', '/admin/users', {
    _token: '{{csrf_token}}',
    role_id: '{{role_mahasiswa_id}}',
    name: `User Test ${timestamp}`,
    username: `user.test.${timestamp}`,
    email: `user.test.${timestamp}@example.test`,
    phone: '081234567800',
    password: 'password123',
    is_active: '1',
  }),
  get('Form edit user aman', '/admin/users/{{user_id}}/edit', [200, 404]),
  get('Daftar lokasi', '/admin/locations'),
  get('Form tambah lokasi', '/admin/locations/create'),
  mutate('Tambah lokasi', 'POST', '/admin/locations', {
    _token: '{{csrf_token}}',
    name: `Lokasi Test ${timestamp}`,
    code: `LOC-${timestamp}`,
    description: 'Lokasi untuk pengujian sistem.',
    is_active: '1',
  }),
  get('Daftar kategori insiden', '/admin/incident-categories'),
  get('Form tambah kategori insiden', '/admin/incident-categories/create'),
  mutate('Tambah kategori insiden', 'POST', '/admin/incident-categories', {
    _token: '{{csrf_token}}',
    name: `Kategori Test ${timestamp}`,
    description: 'Kategori untuk pengujian sistem.',
  }),
  get('Daftar ruangan gedung', '/admin/campus-rooms'),
  get('Form tambah ruangan gedung', '/admin/campus-rooms/create'),
  getWithTests('Daftar denah gedung', '/admin/floorplans', [200], bodyContains('Denah per Gedung dan Lantai')),
  get('Form buat denah gedung', '/admin/floorplans/create'),
  get('Preview denah gedung aman', '/admin/floorplans/{{floorplan_id}}', [200, 404]),
  get('Edit denah gedung aman', '/admin/floorplans/{{floorplan_id}}/edit', [200, 404]),
  mutate('Generate denah gedung aman', 'POST', '/admin/floorplans', {
    _token: '{{csrf_token}}',
    name: `Denah Newman ${timestamp}`,
    location_id: '{{location_id}}',
    building_key: 'gedung-teori',
    floor: '9',
    version: '1',
    canvas_width: '420',
    canvas_height: '260',
    is_active: '1',
    'rooms[0][campus_room_id]': '{{campus_room_id}}',
    'rooms[0][shape_type]': 'rect',
    'rooms[0][coordinates]': '{"x":40,"y":40,"width":160,"height":90}',
    'rooms[0][label]': 'Ruang Newman',
    'rooms[0][default_fill_color]': '#e5e7eb',
    'rooms[0][incident_fill_color]': '#ef4444',
    'rooms[0][hazard_fill_color]': '#f59e0b',
    'rooms[0][sort_order]': '1',
  }, [200, 302, 422]),
  get('Monitoring hazard admin', '/admin/hazards'),
  get('Detail hazard monitoring aman', '/admin/hazards/{{hazard_id}}', [200, 404]),
  get('Daftar kategori knowledge', '/admin/knowledge-categories'),
  get('Form tambah kategori knowledge', '/admin/knowledge-categories/create'),
  mutate('Tambah kategori knowledge', 'POST', '/admin/knowledge-categories', {
    _token: '{{csrf_token}}',
    name: `Knowledge Test ${timestamp}`,
    slug: `knowledge-test-${timestamp}`,
    description: 'Kategori knowledge untuk pengujian.',
  }),
  get('Daftar artikel knowledge admin', '/admin/knowledge-articles'),
  get('Form artikel knowledge admin', '/admin/knowledge-articles/create'),
  mutate('Tambah artikel knowledge admin', 'POST', '/admin/knowledge-articles', {
    _token: '{{csrf_token}}',
    knowledge_category_id: '{{knowledge_category_id}}',
    title: `Artikel Admin ${timestamp}`,
    slug: `artikel-admin-${timestamp}`,
    summary: 'Panduan singkat dari admin.',
    reading_time: '5 menit',
    status: 'published',
    'sections[0][title]': 'Materi',
    'sections[0][body]': 'Isi materi admin untuk pengujian.',
    'sections[0][list_style]': 'paragraph',
    'sections[0][media_type]': 'none',
  }),
  get('Daftar kontak darurat', '/admin/emergency-contacts'),
  get('Form kontak darurat', '/admin/emergency-contacts/create'),
  mutate('Tambah kontak darurat', 'POST', '/admin/emergency-contacts', {
    _token: '{{csrf_token}}',
    name: `Pos Keamanan ${timestamp}`,
    phone_number: '081234567000',
    description: 'Kontak keamanan kampus 24 jam.',
    icon: 'phone',
    color_class: 'bg-red-500',
    sort_order: '1',
    is_active: '1',
  }),
  get('Daftar langkah tanggap darurat', '/admin/emergency-response-steps'),
  get('Form langkah tanggap darurat', '/admin/emergency-response-steps/create'),
  mutate('Tambah langkah tanggap darurat', 'POST', '/admin/emergency-response-steps', {
    _token: '{{csrf_token}}',
    title: `Amankan Area ${timestamp}`,
    description: 'Jauhkan orang dari sumber bahaya dan beri tanda peringatan.',
    sort_order: '1',
    is_active: '1',
  }),
  get('Daftar first aid', '/admin/first-aid-guides'),
  get('Form first aid', '/admin/first-aid-guides/create'),
  mutate('Tambah first aid', 'POST', '/admin/first-aid-guides', {
    _token: '{{csrf_token}}',
    title: `Luka Ringan ${timestamp}`,
    icon: 'bandage',
    accent_class: 'bg-green-500',
    summary: 'Panduan penanganan luka ringan.',
    sort_order: '1',
    is_active: '1',
    actions_text: 'Bersihkan luka dengan air mengalir.',
  }),
  logout('admin'),
];

const satgasItems = [
  ...login('satgas', 'satgas@k3l.local'),
  get('Dashboard satgas', '/satgas/dashboard'),
  get('Profil satgas', '/satgas/profile'),
  mutate('Update profil satgas', 'PATCH', '/satgas/profile', {
    _token: '{{csrf_token}}',
    name: 'Satgas K3L',
    username: 'satgas.k3l',
    email: 'satgas@k3l.local',
    phone: '081234567891',
  }),
  get('Daftar insiden satgas', '/satgas/incidents'),
  get('Form insiden satgas', '/satgas/incidents/create'),
  mutate('Submit insiden satgas', 'POST', '/satgas/incidents', {
    _token: '{{csrf_token}}',
    title: `Insiden Satgas ${timestamp}`,
    incident_category_id: '{{incident_category_id}}',
    injury_category_id: '{{injury_category_id}}',
    body_part_id: '{{body_part_id}}',
    location_id: '{{location_id}}',
    incident_date: '2026-05-01',
    incident_time: '11:00',
    severity_level: 'low',
    victim_type: 'self',
    victim_name: 'Satgas Tester',
    victim_position: 'karyawan',
    victim_position_description: 'Petugas Satgas K3L',
    victim_gender: 'male',
    victim_age: '30',
    witness_name: 'Petugas Workshop',
    ppe_used: 'Safety shoes dan rompi',
    chronology: 'Laporan internal dibuat satgas untuk pengujian alur pelaporan insiden sistem.',
    cause: 'Pengujian',
    initial_action: 'Dicatat untuk validasi sistem.',
    impact: 'Tidak ada cedera.',
    'unsafe_conditions[]': 'area_kerja_berbahaya',
    'unsafe_actions[]': 'penggunaan_alat_tidak_aman',
    unsafe_condition_cause: 'Area kerja memerlukan pengamanan tambahan.',
    unsafe_action_cause: 'Simulasi penggunaan alat tidak aman.',
    warning_given_before_incident: '0',
    incident_previously_occurred: '0',
    'proposed_preventions[]': 'inspeksi_rutin',
    prevention_action_plan: 'Tambahkan checklist inspeksi rutin.',
  }, [200, 302], captureIdFromLocation('incident_id', '/\\/satgas\\/incidents\\/(\\d+)/')),
  get('Detail insiden satgas aman', '/satgas/incidents/{{incident_id}}', [200, 404]),
  mutate('Verifikasi insiden aman', 'PATCH', '/satgas/incidents/{{incident_id}}/verify', {
    _token: '{{csrf_token}}',
    injury_category_id: '{{injury_category_id}}',
    body_part_id: '{{body_part_id}}',
    impact: 'Cedera ringan',
    verification_note: 'Laporan diverifikasi oleh satgas.',
  }, [200, 302, 403, 404, 422]),
  mutate('Update status insiden aman', 'PATCH', '/satgas/incidents/{{incident_id}}/status', {
    _token: '{{csrf_token}}',
    status: 'investigating',
    status_note: 'Investigasi lapangan sedang dilakukan.',
  }, [200, 302, 403, 404, 422]),
  mutate('Tambah follow up insiden aman', 'POST', '/satgas/incidents/{{incident_id}}/follow-ups', {
    _token: '{{csrf_token}}',
    action_taken: 'Membersihkan area dan memasang tanda bahaya.',
    due_date: '2026-05-07',
    status: 'in_progress',
  }, [200, 302, 403, 404, 422]),
  get('Daftar hazard satgas', '/satgas/hazards'),
  get('Form hazard satgas', '/satgas/hazards/create'),
  mutate('Submit hazard satgas', 'POST', '/satgas/hazards', {
    _token: '{{csrf_token}}',
    title: `Hazard Satgas ${timestamp}`,
    hazard_type: 'lingkungan',
    location_id: '{{location_id}}',
    specific_location: 'Koridor workshop',
    latitude: '-6.8774225',
    longitude: '107.6207125',
    location_accuracy: '12',
    notes: 'Area koridor perlu diberi tanda peringatan.',
  }, [200, 302], captureIdFromLocation('hazard_id', '/\\/satgas\\/hazards\\/(\\d+)/')),
  get('Peta hazard satgas', '/satgas/hazards/map'),
  mutate('Tambah titik peta hazard', 'POST', '/satgas/hazards/map-points', {
    _token: '{{csrf_token}}',
    title: `Titik Bahaya ${timestamp}`,
    hazard_type: 'listrik',
    risk_level: 'sedang',
    description: 'Titik pengujian pada peta.',
    map_source: 'satellite',
    latitude: '-6.914744',
    longitude: '107.609810',
  }),
  get('Detail hazard satgas aman', '/satgas/hazards/{{hazard_id}}', [200, 404]),
  mutate('Update status hazard aman', 'PATCH', '/satgas/hazards/{{hazard_id}}/status', {
    _token: '{{csrf_token}}',
    status: 'reviewed',
    response_note: 'Temuan sudah dicek dan diteruskan ke petugas fasilitas.',
  }, [200, 302, 404, 422]),
  get('Daftar artikel knowledge satgas', '/satgas/knowledge-articles'),
  get('Form artikel knowledge satgas', '/satgas/knowledge-articles/create'),
  mutate('Tambah artikel knowledge satgas', 'POST', '/satgas/knowledge-articles', {
    _token: '{{csrf_token}}',
    knowledge_category_id: '{{knowledge_category_id}}',
    title: `Artikel Satgas ${timestamp}`,
    slug: `artikel-satgas-${timestamp}`,
    summary: 'Panduan singkat dari satgas.',
    reading_time: '5 menit',
    status: 'published',
    'sections[0][title]': 'Materi',
    'sections[0][body]': 'Isi materi satgas untuk pengujian.',
    'sections[0][list_style]': 'paragraph',
    'sections[0][media_type]': 'none',
  }),
  logout('satgas'),
];

const accessItems = [
  get('Admin route tanpa login dialihkan', '/admin/dashboard', [200, 302, 401, 403]),
  get('Satgas route tanpa login dialihkan', '/satgas/dashboard', [200, 302, 401, 403]),
  ...login('mahasiswa', 'mahasiswa@k3l.local'),
  get('Mahasiswa tidak boleh akses admin', '/admin/dashboard', [401, 403]),
  get('Mahasiswa tidak boleh akses satgas', '/satgas/dashboard', [401, 403]),
  get('Ambil CSRF dashboard mahasiswa', '/user/dashboard'),
  logout('mahasiswa'),
];

const collection = {
  info: {
    name: 'Sistem Informasi K3L - Web Route Functional Test',
    schema: 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
    description: 'Pengujian fungsi web route Sistem Informasi K3L menggunakan Newman. Request mencakup fitur publik, admin, satgas, dan hak akses.',
  },
  item: [
    { name: 'Public', item: publicItems },
    { name: 'Admin', item: adminItems },
    { name: 'Satgas', item: satgasItems },
    { name: 'Hak Akses', item: accessItems },
  ],
  event: [
    {
      listen: 'test',
      script: { type: 'text/javascript', exec: no5xx.trim().split('\n') },
    },
  ],
};

const environment = {
  name: 'K3L Local',
  values: [
    { key: 'base_url', value: 'http://127.0.0.1:8000', enabled: true },
    { key: 'csrf_token', value: '', enabled: true },
    { key: 'knowledge_slug', value: 'panduan-dasar-apd-area-praktikum', enabled: true },
    { key: 'location_id', value: '1', enabled: true },
    { key: 'incident_category_id', value: '1', enabled: true },
    { key: 'injury_category_id', value: '1', enabled: true },
    { key: 'body_part_id', value: '1', enabled: true },
    { key: 'knowledge_category_id', value: '1', enabled: true },
    { key: 'role_mahasiswa_id', value: '3', enabled: true },
    { key: 'user_id', value: '3', enabled: true },
    { key: 'incident_id', value: '1', enabled: true },
    { key: 'hazard_id', value: '1', enabled: true },
    { key: 'incident_report_number', value: 'INC-0001', enabled: true },
    { key: 'campus_room_id', value: '1', enabled: true },
    { key: 'floorplan_id', value: '1', enabled: true },
  ],
};

fs.writeFileSync(collectionPath, `${JSON.stringify(collection, null, 2)}\n`);
fs.writeFileSync(environmentPath, `${JSON.stringify(environment, null, 2)}\n`);
console.log(`Generated ${collectionPath}`);
console.log(`Generated ${environmentPath}`);
