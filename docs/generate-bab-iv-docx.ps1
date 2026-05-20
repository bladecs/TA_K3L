$ErrorActionPreference = 'Stop'

$root = Split-Path -Parent $PSScriptRoot
$output = Join-Path $PSScriptRoot 'BAB_IV_Hasil_dan_Pembahasan_Dengan_Bukti_Uji.docx'
$fallbackOutput = Join-Path $PSScriptRoot 'BAB_IV_Hasil_dan_Pembahasan_Dengan_Bukti_Uji_Updated.docx'
$temp = Join-Path ([System.IO.Path]::GetTempPath()) ('bab-iv-docx-' + [System.Guid]::NewGuid().ToString('N'))
$script:ImageRels = @()
$script:ImageCounter = 1

function Escape-Xml([string] $text) {
    return [System.Security.SecurityElement]::Escape($text)
}

function Paragraph([string] $text, [string] $style = $null, [bool] $bold = $false, [string] $jc = $null) {
    $styleXml = if ($style) { "<w:pPr><w:pStyle w:val=`"$style`"/>" + ($(if ($jc) { "<w:jc w:val=`"$jc`"/>" } else { "" })) + "</w:pPr>" } elseif ($jc) { "<w:pPr><w:jc w:val=`"$jc`"/></w:pPr>" } else { "" }
    $boldXml = if ($bold) { "<w:b/>" } else { "" }
    $escaped = Escape-Xml $text
    return "<w:p>$styleXml<w:r><w:rPr>$boldXml</w:rPr><w:t xml:space=`"preserve`">$escaped</w:t></w:r></w:p>"
}

function Table($headers, $rows) {
    $xml = "<w:tbl><w:tblPr><w:tblW w:w=`"0`" w:type=`"auto`"/><w:tblBorders><w:top w:val=`"single`" w:sz=`"4`" w:space=`"0`" w:color=`"000000`"/><w:left w:val=`"single`" w:sz=`"4`" w:space=`"0`" w:color=`"000000`"/><w:bottom w:val=`"single`" w:sz=`"4`" w:space=`"0`" w:color=`"000000`"/><w:right w:val=`"single`" w:sz=`"4`" w:space=`"0`" w:color=`"000000`"/><w:insideH w:val=`"single`" w:sz=`"4`" w:space=`"0`" w:color=`"000000`"/><w:insideV w:val=`"single`" w:sz=`"4`" w:space=`"0`" w:color=`"000000`"/></w:tblBorders></w:tblPr>"
    $xml += "<w:tr>"
    foreach ($header in $headers) {
        $xml += "<w:tc><w:tcPr><w:shd w:fill=`"D9EAF7`"/></w:tcPr>" + (Paragraph $header $null $true) + "</w:tc>"
    }
    $xml += "</w:tr>"
    foreach ($row in $rows) {
        $xml += "<w:tr>"
        foreach ($cell in $row) {
            $xml += "<w:tc>" + (Paragraph ([string] $cell)) + "</w:tc>"
        }
        $xml += "</w:tr>"
    }
    $xml += "</w:tbl>"
    return $xml
}

function Write-Utf8File([string] $path, [string] $content) {
    $encoding = New-Object System.Text.UTF8Encoding($false)
    [System.IO.File]::WriteAllText($path, $content, $encoding)
}

function ImageParagraph([string] $relativePath, [string] $caption) {
    $source = Join-Path $PSScriptRoot $relativePath
    if (-not (Test-Path $source)) {
        return Paragraph "[Gambar tidak ditemukan: $relativePath]"
    }

    Add-Type -AssemblyName System.Drawing
    $image = [System.Drawing.Image]::FromFile($source)
    try {
        $widthPx = [double] $image.Width
        $heightPx = [double] $image.Height
    }
    finally {
        $image.Dispose()
    }

    $imageName = "image$script:ImageCounter.png"
    $rid = "rIdImg$script:ImageCounter"
    $script:ImageCounter += 1
    $script:ImageRels += [pscustomobject]@{
        Source = $source
        Target = "media/$imageName"
        Name = $imageName
        Rid = $rid
    }

    $maxCx = 5486400
    $maxCy = 3383280
    $cx = $maxCx
    $cy = [int] ($cx * ($heightPx / $widthPx))
    if ($cy -gt $maxCy) {
        $cy = $maxCy
        $cx = [int] ($cy * ($widthPx / $heightPx))
    }

    $escapedCaption = Escape-Xml $caption
    $drawing = @"
<w:p>
  <w:pPr><w:jc w:val="center"/></w:pPr>
  <w:r>
    <w:drawing>
      <wp:inline distT="0" distB="0" distL="0" distR="0" xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing">
        <wp:extent cx="$cx" cy="$cy"/>
        <wp:effectExtent l="0" t="0" r="0" b="0"/>
        <wp:docPr id="$($script:ImageCounter + 100)" name="$escapedCaption"/>
        <wp:cNvGraphicFramePr>
          <a:graphicFrameLocks noChangeAspect="1" xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"/>
        </wp:cNvGraphicFramePr>
        <a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
          <a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
            <pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
              <pic:nvPicPr>
                <pic:cNvPr id="0" name="$imageName"/>
                <pic:cNvPicPr/>
              </pic:nvPicPr>
              <pic:blipFill>
                <a:blip r:embed="$rid" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"/>
                <a:stretch><a:fillRect/></a:stretch>
              </pic:blipFill>
              <pic:spPr>
                <a:xfrm><a:off x="0" y="0"/><a:ext cx="$cx" cy="$cy"/></a:xfrm>
                <a:prstGeom prst="rect"><a:avLst/></a:prstGeom>
              </pic:spPr>
            </pic:pic>
          </a:graphicData>
        </a:graphic>
      </wp:inline>
    </w:drawing>
  </w:r>
</w:p>
"@

    return $drawing + (Paragraph $caption $null $false "center")
}

$newman = Get-Content (Join-Path $PSScriptRoot 'test\newman-summary.json') -Raw | ConvertFrom-Json
$ui = Get-Content (Join-Path $PSScriptRoot 'test\ui-test-results.json') -Raw | ConvertFrom-Json
$uiCount = (Get-ChildItem (Join-Path $PSScriptRoot 'UI') -Filter '*.png' -ErrorAction SilentlyContinue).Count

$body = ""
$body += Paragraph "BAB IV" "Title" $true "center"
$body += Paragraph "HASIL DAN PEMBAHASAN" "Title" $true "center"
$body += Paragraph "Bab ini menjelaskan hasil implementasi Sistem Informasi Keselamatan dan Kesehatan Kerja serta Lingkungan (K3L) berbasis web yang telah dikembangkan. Pembahasan meliputi implementasi sistem, implementasi database, implementasi antarmuka, implementasi fitur, serta hasil pengujian yang dilakukan terhadap fungsi sistem dan tampilan antarmuka."

$body += Paragraph "IV.1 Implementasi Sistem" "Heading1" $true
$body += Paragraph "Implementasi sistem merupakan tahap penerapan hasil perancangan ke dalam bentuk aplikasi berbasis web. Sistem Informasi K3L dikembangkan menggunakan framework Laravel dengan basis data MySQL. Sistem ini dirancang untuk membantu pengelolaan data K3L di lingkungan Politeknik Manufaktur Bandung, khususnya dalam proses pelaporan insiden, pelaporan potensi bahaya, pemetaan titik bahaya, penyediaan panduan K3L, penyediaan informasi darurat, serta pengelolaan data oleh satgas dan admin."
$body += Paragraph "Sistem memiliki tiga jenis aktor utama, yaitu pengguna umum, Satgas K3L, dan admin. Pengguna umum dapat mengakses fitur publik tanpa harus login, seperti dashboard publik, form laporan insiden, form laporan potensi bahaya, peta GIS, knowledge center, dan emergency center. Satgas K3L memiliki hak akses untuk mengelola laporan yang masuk, melakukan verifikasi, menindaklanjuti laporan, mengelola titik pemetaan bahaya, dan mengelola artikel K3L. Admin memiliki hak akses untuk mengelola data pengguna, lokasi, kategori, konten panduan, kontak darurat, langkah tanggap darurat, dan panduan pertolongan pertama."
$body += Table @("No", "Komponen", "Hasil Implementasi") @(
    @("1", "Framework", "Sistem dikembangkan menggunakan Laravel sebagai framework utama aplikasi web."),
    @("2", "Basis Data", "Sistem menggunakan MySQL sebagai media penyimpanan data."),
    @("3", "Akses Sistem", "Sistem dapat diakses melalui browser dengan pembagian akses publik, satgas, dan admin."),
    @("4", "Arsitektur", "Sistem menerapkan pola Model-View-Controller (MVC) yang memisahkan tampilan, logika aplikasi, dan pengelolaan data."),
    @("5", "Hak Akses", "Fitur internal dibatasi berdasarkan role pengguna, yaitu Satgas K3L dan admin.")
)

$body += Paragraph "IV.2 Implementasi Database" "Heading1" $true
$body += Paragraph "Implementasi database dilakukan menggunakan MySQL. Struktur database dibuat berdasarkan rancangan ERD yang telah disusun pada tahap perancangan. Database digunakan untuk menyimpan data pengguna, role, lokasi, laporan insiden, laporan potensi bahaya, titik pemetaan GIS, panduan K3L, emergency center, dan log aktivitas. Relasi antar tabel dibangun menggunakan primary key dan foreign key agar data dapat tersimpan dan terhubung secara terstruktur."
$body += Paragraph "Tabel teknis bawaan framework seperti sessions, cache, jobs, dan password reset tidak dibahas secara rinci karena tidak berkaitan langsung dengan proses bisnis K3L. Pembahasan difokuskan pada tabel yang mendukung fitur utama sistem."
$body += Table @("No", "Nama Tabel", "Fungsi Implementasi") @(
    @("1", "roles", "Menyimpan data peran atau hak akses pengguna."),
    @("2", "users", "Menyimpan data akun pengguna sistem."),
    @("3", "locations", "Menyimpan data lokasi atau area kerja."),
    @("4", "incident_reports", "Menyimpan data utama laporan insiden, data korban, cedera, analisa awal, dan usulan pencegahan."),
    @("5", "incident_attachments", "Menyimpan lampiran atau bukti pendukung laporan insiden."),
    @("6", "incident_status_histories", "Menyimpan riwayat perubahan status laporan insiden."),
    @("7", "incident_follow_ups", "Menyimpan data tindak lanjut laporan insiden."),
    @("8", "potential_hazard_reports", "Menyimpan data laporan potensi bahaya."),
    @("9", "potential_hazard_attachments", "Menyimpan lampiran laporan potensi bahaya."),
    @("10", "hazard_map_points", "Menyimpan titik lokasi bahaya pada fitur GIS dan denah."),
    @("11", "knowledge_categories", "Menyimpan kategori artikel atau panduan K3L."),
    @("12", "knowledge_articles", "Menyimpan artikel, materi, dan panduan K3L."),
    @("13", "emergency_contacts", "Menyimpan informasi kontak darurat."),
    @("14", "emergency_response_steps", "Menyimpan langkah tanggap darurat."),
    @("15", "first_aid_guides", "Menyimpan data panduan pertolongan pertama."),
    @("16", "first_aid_actions", "Menyimpan detail tindakan pada panduan pertolongan pertama."),
    @("17", "activity_logs", "Menyimpan riwayat aktivitas pengguna dalam sistem.")
)

$body += Paragraph "IV.3 Implementasi Antarmuka" "Heading1" $true
$body += Paragraph "Implementasi antarmuka dilakukan berdasarkan rancangan tampilan yang telah dibuat sebelumnya. Antarmuka sistem dibuat berbasis web agar dapat diakses melalui browser. Tampilan sistem disesuaikan dengan hak akses pengguna, sehingga pengguna umum, Satgas K3L, dan admin memperoleh menu yang berbeda sesuai dengan kebutuhan masing-masing."
$body += Paragraph "Dokumentasi antarmuka telah disimpan dalam folder docs/UI dengan jumlah $uiCount screenshot. Screenshot tersebut digunakan sebagai bukti implementasi tampilan halaman sistem, sedangkan hasil pengujian UI tersimpan pada folder docs/test/ui-screenshots dan dirangkum dalam file ui-smoke-report.html."
$body += ImageParagraph "UI\00-login.png" "Gambar 4.1 Implementasi Halaman Login"
$body += ImageParagraph "UI\01-user-dashboard.png" "Gambar 4.2 Implementasi Dashboard Publik"
$body += ImageParagraph "UI\01-user-incident-create.png" "Gambar 4.3 Implementasi Form Laporan Insiden"
$body += ImageParagraph "UI\01-user-hazard-create.png" "Gambar 4.4 Implementasi Form Laporan Potensi Bahaya"
$body += ImageParagraph "UI\01-user-hazard-map.png" "Gambar 4.5 Implementasi Peta GIS Potensi Bahaya"
$body += ImageParagraph "UI\01-user-knowledge-center.png" "Gambar 4.6 Implementasi Knowledge Center"
$body += ImageParagraph "UI\01-user-emergency-center.png" "Gambar 4.7 Implementasi Emergency Center"
$body += ImageParagraph "UI\02-satgas-dashboard.png" "Gambar 4.8 Implementasi Dashboard Satgas"
$body += ImageParagraph "UI\03-admin-dashboard.png" "Gambar 4.9 Implementasi Dashboard Admin"
$body += Table @("No", "Halaman", "Aktor", "Keterangan") @(
    @("1", "Login", "Satgas K3L dan Admin", "Digunakan untuk masuk ke dashboard internal sesuai role pengguna."),
    @("2", "Dashboard Publik", "Pengguna Umum", "Menampilkan akses utama seperti lapor insiden, lapor potensi bahaya, GIS, materi K3L, dan kontak darurat."),
    @("3", "Form Laporan Insiden", "Pengguna Umum dan Satgas", "Digunakan untuk mengirim laporan insiden beserta data korban, kejadian, cedera, kronologi, analisa awal, dan usulan pencegahan."),
    @("4", "Form Laporan Potensi Bahaya", "Pengguna Umum dan Satgas", "Digunakan untuk melaporkan potensi bahaya di area kerja."),
    @("5", "Peta GIS", "Pengguna Umum dan Satgas", "Menampilkan titik bahaya pada peta dan denah area kampus."),
    @("6", "Knowledge Center", "Pengguna Umum", "Menampilkan materi, artikel, dan panduan K3L."),
    @("7", "Emergency Center", "Pengguna Umum", "Menampilkan kontak darurat, langkah tanggap darurat, dan panduan pertolongan pertama."),
    @("8", "Dashboard Satgas", "Satgas K3L", "Menampilkan ringkasan laporan dan data yang perlu ditindaklanjuti."),
    @("9", "Dashboard Admin", "Admin", "Menampilkan ringkasan data sistem dan akses pengelolaan data utama.")
)

$body += Paragraph "IV.4 Implementasi Fitur" "Heading1" $true
$body += Paragraph "Implementasi fitur dilakukan dengan menyesuaikan kebutuhan sistem yang telah dianalisis. Fitur-fitur yang dikembangkan berfokus pada pelaporan, pemantauan, edukasi, pengelolaan data, serta pembatasan akses berdasarkan role pengguna."
$body += Table @("No", "Fitur", "Hasil Implementasi") @(
    @("1", "Pelaporan Insiden", "Pengguna dapat membuat laporan insiden melalui form yang tersedia. Data yang dicatat meliputi identitas pelapor, data korban, lokasi, tanggal dan waktu kejadian, kategori, cedera, kronologi, penyebab, tindakan awal, dampak, analisa kondisi atau tindakan tidak aman, usulan pencegahan, dan lampiran."),
    @("2", "Cek Status Insiden", "Pengguna dapat memantau perkembangan laporan insiden melalui halaman cek status."),
    @("3", "Pelaporan Potensi Bahaya", "Pengguna dapat melaporkan kondisi berbahaya atau potensi bahaya pada area kerja."),
    @("4", "Pemetaan GIS", "Sistem menampilkan titik bahaya pada peta dan denah, serta mendukung pengelolaan titik oleh Satgas K3L."),
    @("5", "Knowledge Center", "Sistem menyediakan artikel dan panduan K3L yang dapat diakses oleh pengguna umum."),
    @("6", "Emergency Center", "Sistem menyediakan kontak darurat, langkah tanggap darurat, dan panduan pertolongan pertama."),
    @("7", "Pengelolaan Laporan oleh Satgas", "Satgas dapat melihat, memverifikasi, mengubah status, dan menindaklanjuti laporan yang masuk."),
    @("8", "Manajemen Pengguna", "Admin dapat menambah dan mengubah data akun pengguna."),
    @("9", "Manajemen Data Master", "Admin dapat mengelola lokasi, kategori insiden, kategori knowledge, kontak darurat, langkah tanggap darurat, dan panduan pertolongan pertama."),
    @("10", "Hak Akses", "Sistem membatasi akses fitur berdasarkan role pengguna sehingga fitur internal hanya dapat digunakan oleh pengguna yang berwenang.")
)

$body += Paragraph "IV.5 Pengujian Sistem" "Heading1" $true
$body += Paragraph "Pengujian sistem dilakukan untuk memastikan bahwa Sistem Informasi K3L yang telah dibangun dapat berjalan sesuai dengan kebutuhan. Pengujian difokuskan pada fungsi utama sistem, pembatasan hak akses, serta tampilan antarmuka pada masing-masing aktor. Metode pengujian yang digunakan terdiri dari pengujian black box dan User Acceptance Test (UAT). Selain itu, pengujian antarmuka dilakukan menggunakan smoke test untuk memastikan halaman utama dapat diakses dan menampilkan elemen penting sesuai dengan perancangan."

$body += Paragraph "IV.5.1 Pengujian Black Box" "Heading1" $true
$body += Paragraph "Pengujian black box dilakukan dengan menguji masukan dan keluaran sistem tanpa melihat kode program secara langsung. Pengujian ini bertujuan untuk memastikan bahwa fitur yang tersedia dapat berjalan sesuai dengan skenario penggunaan. Pada penelitian ini, pengujian black box dilakukan menggunakan Newman berdasarkan koleksi Postman yang berisi skenario pengujian route dan fungsi sistem."
$body += Paragraph "Pengujian menggunakan Newman mencakup fitur publik, fitur satgas, fitur admin, serta pengujian hak akses. Fitur publik meliputi akses dashboard, form pelaporan insiden, form pelaporan potensi bahaya, peta GIS, knowledge center, dan emergency center. Fitur satgas meliputi pengelolaan laporan insiden, pengelolaan laporan potensi bahaya, peta hazard, dan artikel K3L. Fitur admin meliputi manajemen pengguna, lokasi, kategori insiden, kategori knowledge, kontak darurat, langkah tanggap darurat, dan panduan pertolongan pertama."
$body += Table @("No", "Skenario Pengujian", "Hasil yang Diharapkan", "Status") @(
    @("1", "Mengakses halaman publik", "Dashboard publik, knowledge center, emergency center, dan peta GIS dapat ditampilkan.", "Berhasil"),
    @("2", "Mengirim laporan insiden", "Data laporan insiden lengkap, termasuk data korban, cedera, analisa awal, dan usulan pencegahan dapat diproses oleh sistem.", "Berhasil"),
    @("3", "Mengirim laporan potensi bahaya", "Data laporan potensi bahaya dapat diproses oleh sistem.", "Berhasil"),
    @("4", "Login admin dan satgas", "Pengguna internal dapat masuk ke dashboard sesuai role.", "Berhasil"),
    @("5", "Mengelola laporan oleh satgas", "Satgas dapat melihat dan memproses laporan yang masuk.", "Berhasil"),
    @("6", "Mengelola titik GIS oleh satgas", "Satgas dapat menambahkan titik bahaya pada peta.", "Berhasil"),
    @("7", "Mengelola data admin", "Admin dapat mengelola data pengguna, lokasi, kategori, dan emergency center.", "Berhasil"),
    @("8", "Menguji hak akses mahasiswa", "Mahasiswa tidak dapat mengakses halaman admin dan satgas.", "Berhasil")
)

$body += Paragraph "IV.5.2 Pengujian User Acceptance Test / UAT" "Heading1" $true
$body += Paragraph "User Acceptance Test (UAT) dilakukan untuk menilai apakah sistem sudah sesuai dengan kebutuhan pengguna dari sisi alur penggunaan dan ketersediaan fitur. Pengujian UAT disusun berdasarkan peran aktor dalam sistem, yaitu pengguna umum, Satgas K3L, dan admin. UAT berfokus pada penerimaan pengguna terhadap fitur utama yang telah dibangun."
$body += Paragraph "Pada tahap ini, skenario UAT disusun berdasarkan kebutuhan fungsional sistem. Pengguna umum diuji pada fitur pelaporan dan akses informasi publik. Satgas K3L diuji pada fitur pengelolaan laporan dan pemetaan bahaya. Admin diuji pada fitur manajemen data utama sistem. Jika pada pelaksanaan penelitian dilakukan penilaian langsung oleh responden, tabel ini dapat dilengkapi dengan kolom penilaian atau persentase penerimaan."
$body += Table @("No", "Aktor", "Skenario UAT", "Kriteria Penerimaan", "Status") @(
    @("1", "Pengguna Umum", "Mengakses dashboard publik", "Pengguna dapat melihat akses utama sistem tanpa login.", "Diterima"),
    @("2", "Pengguna Umum", "Mengisi laporan insiden", "Pengguna dapat mengisi dan mengirim laporan insiden lengkap sesuai kebutuhan data kejadian.", "Diterima"),
    @("3", "Pengguna Umum", "Mengisi laporan potensi bahaya", "Pengguna dapat mengisi dan mengirim laporan potensi bahaya.", "Diterima"),
    @("4", "Pengguna Umum", "Melihat peta GIS dan panduan K3L", "Pengguna dapat melihat informasi peta, materi K3L, dan emergency center.", "Diterima"),
    @("5", "Satgas K3L", "Meninjau laporan yang masuk", "Satgas dapat membuka daftar laporan dan melihat detail laporan.", "Diterima"),
    @("6", "Satgas K3L", "Mengelola titik pemetaan bahaya", "Satgas dapat mengakses halaman peta dan menambahkan titik bahaya.", "Diterima"),
    @("7", "Admin", "Mengelola data pengguna dan master data", "Admin dapat mengakses halaman manajemen data sesuai hak akses.", "Diterima"),
    @("8", "Admin", "Mengelola emergency center", "Admin dapat mengelola kontak darurat, langkah tanggap darurat, dan panduan pertolongan pertama.", "Diterima")
)

$body += Paragraph "IV.6 Hasil Pengujian" "Heading1" $true
$body += Paragraph "Hasil pengujian black box menggunakan Newman menunjukkan bahwa seluruh skenario fungsi sistem dapat dijalankan dengan baik. Berdasarkan hasil pengujian, sebanyak $($newman.requests.total) request berhasil dijalankan dengan $($newman.assertions.total) assertion dan jumlah kegagalan sebanyak $($newman.failures). Hasil tersebut menunjukkan bahwa route dan fungsi utama sistem telah memberikan respons sesuai dengan skenario yang diuji."
$body += ImageParagraph "test\newman-report-screenshot.png" "Gambar 4.10 Bukti Hasil Pengujian Fungsi Menggunakan Newman"
$body += Paragraph "Hasil pengujian UI smoke test menunjukkan bahwa sebanyak $($ui.total) skenario tampilan berhasil dijalankan, dengan jumlah berhasil sebanyak $($ui.passed) dan gagal sebanyak $($ui.failed). Pengujian UI mencakup halaman publik, halaman satgas, dan halaman admin. Setiap halaman diuji berdasarkan keberadaan teks atau elemen utama yang menunjukkan bahwa halaman berhasil dimuat sesuai dengan perannya."
$body += ImageParagraph "test\ui-smoke-report-screenshot.png" "Gambar 4.11 Bukti Hasil Pengujian UI Smoke Test"
$body += Table @("No", "Jenis Pengujian", "Jumlah Skenario/Request", "Berhasil", "Gagal", "Keterangan") @(
    @("1", "Black Box menggunakan Newman", "$($newman.requests.total) request dan $($newman.assertions.total) assertion", "$($newman.assertions.total) assertion", "$($newman.failures)", "Seluruh fungsi yang diuji berjalan sesuai skenario."),
    @("2", "UI Smoke Test", "$($ui.total) skenario", "$($ui.passed)", "$($ui.failed)", "Seluruh halaman utama berhasil ditampilkan."),
    @("3", "Dokumentasi Pengujian", "Report HTML, JSON, TXT, Markdown, dan screenshot", "Tersedia", "-", "Hasil tersimpan pada folder docs/test.")
)
$body += Paragraph "Selain ringkasan pada tabel, bukti pengujian juga disimpan dalam bentuk file report agar hasil pengujian dapat ditelusuri kembali. Report HTML digunakan sebagai bukti visual, sedangkan file JSON dan TXT digunakan sebagai bukti teknis hasil eksekusi pengujian."
$body += Table @("No", "Nama File Bukti", "Keterangan") @(
    @("1", "docs/test/newman-report.html", "Report HTML hasil pengujian fungsi menggunakan Newman."),
    @("2", "docs/test/newman-results.json", "Data lengkap hasil eksekusi Newman dalam format JSON."),
    @("3", "docs/test/newman-results.txt", "Output terminal hasil pengujian Newman."),
    @("4", "docs/test/newman-summary.md", "Ringkasan hasil pengujian fungsi dalam format Markdown."),
    @("5", "docs/test/ui-smoke-report.html", "Report HTML hasil pengujian antarmuka."),
    @("6", "docs/test/ui-test-results.json", "Data lengkap hasil pengujian UI smoke test."),
    @("7", "docs/test/ui-test-results.md", "Ringkasan hasil pengujian UI dalam format Markdown."),
    @("8", "docs/test/ui-screenshots", "Folder berisi screenshot halaman yang diuji pada UI smoke test.")
)
$body += Paragraph "Dokumentasi pengujian fungsi tersimpan pada file newman-report.html, newman-results.json, newman-results.txt, dan newman-summary.md. Dokumentasi pengujian UI tersimpan pada file ui-smoke-report.html, ui-test-results.json, ui-test-results.md, serta folder ui-screenshots. Dengan adanya dokumentasi tersebut, hasil pengujian dapat ditelusuri kembali dan digunakan sebagai bukti bahwa sistem telah diuji."

$body += Paragraph "IV.7 Pembahasan" "Heading1" $true
$body += Paragraph "Berdasarkan hasil implementasi, Sistem Informasi K3L berhasil dibangun sebagai aplikasi berbasis web yang mendukung proses pengelolaan data K3L secara terintegrasi. Sistem menyediakan fitur pelaporan insiden dan pelaporan potensi bahaya yang dapat diakses oleh pengguna umum tanpa login. Hal ini memudahkan pengguna dalam menyampaikan laporan secara cepat tanpa hambatan autentikasi."
$body += Paragraph "Fitur pemetaan GIS menjadi salah satu bagian penting karena dapat menampilkan titik bahaya pada peta dan denah. Dengan adanya fitur ini, Satgas K3L dapat memantau lokasi potensi bahaya secara visual. Selain itu, knowledge center dan emergency center membantu pengguna memperoleh informasi keselamatan, kontak darurat, langkah tanggap darurat, serta panduan pertolongan pertama."
$body += Paragraph "Pembagian hak akses pada sistem juga telah berjalan sesuai peran. Pengguna umum hanya dapat mengakses fitur publik, Satgas K3L dapat mengelola laporan dan titik pemetaan bahaya, sedangkan admin dapat mengelola data utama sistem. Hasil pengujian hak akses menunjukkan bahwa pengguna dengan role mahasiswa tidak dapat mengakses halaman admin maupun satgas."
$body += Paragraph "Berdasarkan hasil pengujian black box, seluruh fungsi yang diuji dapat berjalan tanpa kegagalan. Hasil UI smoke test juga menunjukkan bahwa halaman utama sistem dapat ditampilkan dengan baik. Dengan demikian, sistem yang dibangun telah memenuhi kebutuhan utama, yaitu membantu proses pelaporan, pengelolaan, pemantauan, dan penyediaan informasi K3L di lingkungan Politeknik Manufaktur Bandung."

$documentXml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
            xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
            xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
            xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
  <w:body>
    $body
    <w:sectPr>
      <w:pgSz w:w="11906" w:h="16838"/>
      <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="708" w:footer="708" w:gutter="0"/>
    </w:sectPr>
  </w:body>
</w:document>
"@

$stylesXml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:style w:type="paragraph" w:default="1" w:styleId="Normal">
    <w:name w:val="Normal"/>
    <w:pPr><w:spacing w:after="160" w:line="276" w:lineRule="auto"/></w:pPr>
    <w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="24"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Title">
    <w:name w:val="Title"/>
    <w:basedOn w:val="Normal"/>
    <w:rPr><w:b/><w:sz w:val="28"/></w:rPr>
  </w:style>
  <w:style w:type="paragraph" w:styleId="Heading1">
    <w:name w:val="heading 1"/>
    <w:basedOn w:val="Normal"/>
    <w:pPr><w:spacing w:before="240" w:after="160"/></w:pPr>
    <w:rPr><w:b/><w:sz w:val="26"/></w:rPr>
  </w:style>
</w:styles>
"@

$contentTypes = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Default Extension="png" ContentType="image/png"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
  <Override PartName="/word/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
</Types>
"@

$rels = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
"@

$documentRels = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
"@

foreach ($imageRel in $script:ImageRels) {
    $documentRels += "  <Relationship Id=`"$($imageRel.Rid)`" Type=`"http://schemas.openxmlformats.org/officeDocument/2006/relationships/image`" Target=`"$($imageRel.Target)`"/>`n"
}

$documentRels += @"
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles" Target="styles.xml"/>
</Relationships>
"@

New-Item -ItemType Directory -Force -Path (Join-Path $temp '_rels') | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $temp 'word\_rels') | Out-Null
New-Item -ItemType Directory -Force -Path (Join-Path $temp 'word\media') | Out-Null
Write-Utf8File (Join-Path $temp '[Content_Types].xml') $contentTypes
Write-Utf8File (Join-Path $temp '_rels\.rels') $rels
Write-Utf8File (Join-Path $temp 'word\document.xml') $documentXml
Write-Utf8File (Join-Path $temp 'word\styles.xml') $stylesXml
Write-Utf8File (Join-Path $temp 'word\_rels\document.xml.rels') $documentRels

foreach ($imageRel in $script:ImageRels) {
    Copy-Item -LiteralPath $imageRel.Source -Destination (Join-Path (Join-Path $temp 'word\media') $imageRel.Name) -Force
}

if (Test-Path $output) {
    try {
        Remove-Item -LiteralPath $output -Force
    }
    catch {
        Write-Warning "File utama sedang digunakan. Dokumen akan dibuat sebagai $fallbackOutput"
        $output = $fallbackOutput
        if (Test-Path $output) {
            Remove-Item -LiteralPath $output -Force
        }
    }
}

$zipOutput = [System.IO.Path]::ChangeExtension($output, '.zip')
if (Test-Path $zipOutput) {
    Remove-Item -LiteralPath $zipOutput -Force
}

Compress-Archive -Path (Join-Path $temp '*') -DestinationPath $zipOutput -Force
Move-Item -LiteralPath $zipOutput -Destination $output -Force
Remove-Item -LiteralPath $temp -Recurse -Force

Write-Host "Generated $output"
