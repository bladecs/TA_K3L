const fs = require('fs');
const path = require('path');

const outDir = __dirname;

function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function urlToString(url) {
  if (!url) {
    return '-';
  }

  const protocol = url.protocol ? `${url.protocol}://` : '';
  const host = Array.isArray(url.host) ? url.host.join('.') : (url.host || '');
  const port = url.port ? `:${url.port}` : '';
  const pathname = Array.isArray(url.path)
    ? `/${url.path.filter(Boolean).join('/')}`
    : (url.path ? `/${url.path}` : '/');
  const query = Array.isArray(url.query) && url.query.length > 0
    ? `?${url.query.map((item) => `${item.key}=${item.value ?? ''}`).join('&')}`
    : '';

  return `${protocol}${host}${port}${pathname}${query}`;
}

function layout(title, summaryCards, body) {
  return `<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>${escapeHtml(title)}</title>
  <style>
    :root { color-scheme: light; --blue:#0f4fb8; --green:#15803d; --red:#b91c1c; --slate:#334155; --line:#dbe3ef; --bg:#f4f7fb; }
    * { box-sizing: border-box; }
    body { margin: 0; font-family: Arial, Helvetica, sans-serif; background: var(--bg); color: #0f172a; }
    header { background: linear-gradient(120deg, #0f4fb8, #11316e); color: white; padding: 32px 40px; }
    header h1 { margin: 0 0 8px; font-size: 28px; }
    header p { margin: 0; opacity: .9; }
    main { padding: 28px 40px 44px; }
    .cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 14px; margin-bottom: 24px; }
    .card { background: white; border: 1px solid var(--line); border-radius: 8px; padding: 16px; }
    .card span { display: block; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; }
    .card strong { display: block; margin-top: 8px; font-size: 28px; color: var(--blue); }
    section { background: white; border: 1px solid var(--line); border-radius: 8px; padding: 20px; overflow: auto; }
    table { border-collapse: collapse; width: 100%; min-width: 900px; }
    th, td { border-bottom: 1px solid #e5edf7; padding: 10px 12px; text-align: left; vertical-align: top; font-size: 13px; }
    th { background: #f8fafc; color: #334155; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; }
    .passed { color: var(--green); font-weight: 700; }
    .failed { color: var(--red); font-weight: 700; }
    .code { font-family: Consolas, Monaco, monospace; }
    a { color: var(--blue); text-decoration: none; }
    a:hover { text-decoration: underline; }
  </style>
</head>
<body>
  <header>
    <h1>${escapeHtml(title)}</h1>
    <p>Sistem Informasi K3L Politeknik Manufaktur Bandung</p>
  </header>
  <main>
    <div class="cards">${summaryCards}</div>
    ${body}
  </main>
</body>
</html>`;
}

function generateNewmanHtml() {
  const source = path.join(outDir, 'newman-results.json');
  if (!fs.existsSync(source)) {
    throw new Error('newman-results.json belum ditemukan.');
  }

  const data = JSON.parse(fs.readFileSync(source, 'utf8'));
  const stats = data.run.stats;
  const duration = ((data.run.timings.completed - data.run.timings.started) / 1000).toFixed(1);
  const rows = data.run.executions.map((execution, index) => {
    const assertionFailed = (execution.assertions || []).some((assertion) => assertion.error);
    const status = assertionFailed ? 'failed' : 'passed';
    return `<tr>
      <td>${index + 1}</td>
      <td>${escapeHtml(execution.item.name)}</td>
      <td class="code">${escapeHtml(execution.request.method)}</td>
      <td class="code">${escapeHtml(urlToString(execution.request.url))}</td>
      <td class="code">${escapeHtml(execution.response?.code ?? '-')}</td>
      <td class="${status}">${status}</td>
    </tr>`;
  }).join('\n');

  const cards = [
    ['Total Request', stats.requests.total],
    ['Request Gagal', stats.requests.failed],
    ['Total Assertion', stats.assertions.total],
    ['Assertion Gagal', stats.assertions.failed],
    ['Durasi', `${duration} detik`],
  ].map(([label, value]) => `<div class="card"><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`).join('');

  const body = `<section>
    <table>
      <thead>
        <tr><th>No</th><th>Nama Pengujian</th><th>Method</th><th>URL</th><th>Code</th><th>Status</th></tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>
  </section>`;

  fs.writeFileSync(path.join(outDir, 'newman-report.html'), layout('Laporan Pengujian Fungsi Newman', cards, body));
}

function generateUiHtml() {
  const source = path.join(outDir, 'ui-test-results.json');
  if (!fs.existsSync(source)) {
    throw new Error('ui-test-results.json belum ditemukan.');
  }

  const data = JSON.parse(fs.readFileSync(source, 'utf8'));
  const rows = data.results.map((result, index) => {
    const status = result.status === 'passed' ? 'passed' : 'failed';
    return `<tr>
      <td>${index + 1}</td>
      <td>${escapeHtml(result.role)}</td>
      <td class="code">${escapeHtml(result.path)}</td>
      <td>${escapeHtml(result.expectedText.join(', '))}</td>
      <td class="${status}">${escapeHtml(result.status)}</td>
      <td><a href="${escapeHtml(result.screenshot)}">${escapeHtml(result.screenshot)}</a></td>
    </tr>`;
  }).join('\n');

  const cards = [
    ['Total Skenario', data.total],
    ['Berhasil', data.passed],
    ['Gagal', data.failed],
    ['Base URL', data.base_url],
  ].map(([label, value]) => `<div class="card"><span>${escapeHtml(label)}</span><strong>${escapeHtml(value)}</strong></div>`).join('');

  const body = `<section>
    <table>
      <thead>
        <tr><th>No</th><th>Role</th><th>Halaman</th><th>Validasi Teks</th><th>Status</th><th>Screenshot</th></tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>
  </section>`;

  fs.writeFileSync(path.join(outDir, 'ui-smoke-report.html'), layout('Laporan Pengujian UI Smoke Test', cards, body));
}

generateNewmanHtml();
generateUiHtml();

console.log('Generated docs/test/newman-report.html');
console.log('Generated docs/test/ui-smoke-report.html');
