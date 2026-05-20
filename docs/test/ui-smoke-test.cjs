const fs = require('fs');
const path = require('path');
const os = require('os');
const http = require('http');
const { spawn } = require('child_process');

const baseUrl = process.env.K3L_BASE_URL || 'http://127.0.0.1:8000';
const outDir = __dirname;
const screenshotDir = path.join(outDir, 'ui-screenshots');
fs.mkdirSync(screenshotDir, { recursive: true });

const chromePath = [
  'C:/Program Files/Google/Chrome/Application/chrome.exe',
  'C:/Program Files (x86)/Microsoft/Edge/Application/msedge.exe',
  'C:/Program Files (x86)/Google/Chrome/Application/chrome.exe',
].find((candidate) => fs.existsSync(candidate));

if (!chromePath) {
  console.error('Chrome atau Edge tidak ditemukan untuk UI smoke test.');
  process.exit(1);
}

function sleep(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

function getJson(url) {
  return new Promise((resolve, reject) => {
    http.get(url, (res) => {
      let data = '';
      res.on('data', (chunk) => { data += chunk; });
      res.on('end', () => {
        try {
          resolve(JSON.parse(data));
        } catch (error) {
          reject(error);
        }
      });
    }).on('error', reject);
  });
}

async function waitForDevtools(port) {
  for (let attempt = 0; attempt < 60; attempt += 1) {
    try {
      return await getJson(`http://127.0.0.1:${port}/json/version`);
    } catch {
      await sleep(250);
    }
  }

  throw new Error('Chrome DevTools endpoint tidak aktif.');
}

function cdp(wsUrl) {
  return new Promise((resolve, reject) => {
    const ws = new WebSocket(wsUrl);
    let id = 0;
    const pending = new Map();
    const listeners = new Map();

    ws.onopen = () => {
      resolve({
        send(method, params = {}) {
          return new Promise((res, rej) => {
            const messageId = ++id;
            pending.set(messageId, { res, rej });
            ws.send(JSON.stringify({ id: messageId, method, params }));
          });
        },
        once(method, timeout = 15000) {
          return new Promise((res, rej) => {
            const timer = setTimeout(() => rej(new Error(`Timeout menunggu ${method}`)), timeout);
            listeners.set(method, (payload) => {
              clearTimeout(timer);
              listeners.delete(method);
              res(payload);
            });
          });
        },
        close() {
          ws.close();
        },
      });
    };

    ws.onerror = reject;
    ws.onmessage = (event) => {
      const message = JSON.parse(event.data);
      if (message.id && pending.has(message.id)) {
        const { res, rej } = pending.get(message.id);
        pending.delete(message.id);
        if (message.error) {
          rej(new Error(JSON.stringify(message.error)));
        } else {
          res(message.result);
        }
      } else if (message.method && listeners.has(message.method)) {
        listeners.get(message.method)(message.params || {});
      }
    };
  });
}

async function navigate(page, url) {
  const load = page.once('Page.loadEventFired').catch(() => null);
  await page.send('Page.navigate', { url });
  await load;
  await sleep(700);
}

async function screenshot(page, name) {
  await page.send('Emulation.setDeviceMetricsOverride', {
    width: 1366,
    height: 900,
    deviceScaleFactor: 1,
    mobile: false,
  });
  const metrics = await page.send('Page.getLayoutMetrics');
  const size = metrics.cssContentSize || metrics.contentSize;
  const shot = await page.send('Page.captureScreenshot', {
    format: 'png',
    fromSurface: true,
    captureBeyondViewport: true,
    clip: {
      x: 0,
      y: 0,
      width: Math.ceil(Math.max(size.width, 1366)),
      height: Math.ceil(Math.min(Math.max(size.height, 900), 3500)),
      scale: 1,
    },
  });
  const file = path.join(screenshotDir, `${name}.png`);
  fs.writeFileSync(file, Buffer.from(shot.data, 'base64'));
  return file;
}

async function getText(page) {
  const result = await page.send('Runtime.evaluate', {
    expression: 'document.body ? document.body.innerText : ""',
    returnByValue: true,
  });
  return result.result.value || '';
}

async function getLocation(page) {
  const result = await page.send('Runtime.evaluate', {
    expression: 'location.href',
    returnByValue: true,
  });
  return result.result.value || '';
}

async function login(page, username) {
  await navigate(page, `${baseUrl}/login`);
  await page.send('Runtime.evaluate', {
    expression: `
      document.querySelector('[name=login]').value = ${JSON.stringify(username)};
      document.querySelector('[name=password]').value = 'password';
      document.querySelector('button[type=submit]').click();
    `,
  });
  await sleep(1500);
}

async function clearSession(page) {
  await page.send('Network.clearBrowserCookies');
}

async function assertPage(page, test) {
  await navigate(page, `${baseUrl}${test.path}`);
  const text = await getText(page);
  const href = await getLocation(page);
  const ok = test.contains.every((needle) => text.toLowerCase().includes(needle.toLowerCase()));
  const screenshotPath = await screenshot(page, test.name);

  return {
    name: test.name,
    path: test.path,
    finalUrl: href,
    expectedText: test.contains,
    status: ok ? 'passed' : 'failed',
    screenshot: path.relative(outDir, screenshotPath).replace(/\\/g, '/'),
  };
}

const scenarios = [
  {
    role: 'public',
    login: null,
    tests: [
      { name: 'public-dashboard', path: '/user/dashboard', contains: ['SIAGA POLMAN', 'Lapor Insiden'] },
      { name: 'public-incident-form', path: '/user/incidents/create', contains: ['Form Pelaporan K3L', 'Data korban', 'Analisa awal kejadian', 'Usulan pencegahan'] },
      { name: 'public-incident-status', path: '/user/incidents/status', contains: ['Cek Status', 'Nomor'] },
      { name: 'public-hazard-form', path: '/user/hazard-reports/create', contains: ['Form Pelaporan K3L', 'Laporkan potensi bahaya', 'Jenis potensi bahaya'] },
      { name: 'public-hazard-map', path: '/user/hazard-map', contains: ['Peta', 'Titik'] },
      { name: 'public-knowledge', path: '/user/knowledge-center', contains: ['Materi', 'K3'] },
      { name: 'public-emergency', path: '/user/emergency-center', contains: ['Darurat', 'Pertolongan'] },
    ],
  },
  {
    role: 'satgas',
    login: 'satgas@k3l.local',
    tests: [
      { name: 'satgas-dashboard', path: '/satgas/dashboard', contains: ['Dashboard', 'Satgas'] },
      { name: 'satgas-incidents', path: '/satgas/incidents', contains: ['Insiden'] },
      { name: 'satgas-hazards', path: '/satgas/hazards', contains: ['Hazard'] },
      { name: 'satgas-map', path: '/satgas/hazards/map', contains: ['Peta', 'Titik'] },
      { name: 'satgas-knowledge', path: '/satgas/knowledge-articles', contains: ['Artikel'] },
    ],
  },
  {
    role: 'admin',
    login: 'admin@k3l.local',
    tests: [
      { name: 'admin-dashboard', path: '/admin/dashboard', contains: ['Dashboard', 'Admin'] },
      { name: 'admin-users', path: '/admin/users', contains: ['Kelola Semua Akun'] },
      { name: 'admin-locations', path: '/admin/locations', contains: ['Lokasi'] },
      { name: 'admin-incident-categories', path: '/admin/incident-categories', contains: ['Kategori'] },
      { name: 'admin-emergency-contacts', path: '/admin/emergency-contacts', contains: ['Kontak'] },
      { name: 'admin-first-aid', path: '/admin/first-aid-guides', contains: ['Pertolongan'] },
    ],
  },
];

(async () => {
  const port = 9345;
  const chrome = spawn(chromePath, [
    `--remote-debugging-port=${port}`,
    `--user-data-dir=${path.join(os.tmpdir(), `k3l-ui-test-${Date.now()}`)}`,
    '--headless=new',
    '--disable-gpu',
    '--no-first-run',
    '--no-default-browser-check',
    '--window-size=1366,900',
    `${baseUrl}/user/dashboard`,
  ], { stdio: 'ignore' });

  const results = [];

  try {
    await waitForDevtools(port);
    const targets = await getJson(`http://127.0.0.1:${port}/json`);
    const pageTarget = targets.find((target) => target.type === 'page');
    const page = await cdp(pageTarget.webSocketDebuggerUrl);
    await page.send('Page.enable');
    await page.send('Runtime.enable');
    await page.send('Network.enable');

    for (const scenario of scenarios) {
      await clearSession(page);
      if (scenario.login) {
        await login(page, scenario.login);
      }

      for (const test of scenario.tests) {
        results.push({ role: scenario.role, ...(await assertPage(page, test)) });
      }
    }

    page.close();
  } finally {
    chrome.kill();
  }

  const passed = results.filter((result) => result.status === 'passed').length;
  const failed = results.length - passed;
  const summary = {
    generated_at: new Date().toISOString(),
    base_url: baseUrl,
    total: results.length,
    passed,
    failed,
    results,
  };

  fs.writeFileSync(path.join(outDir, 'ui-test-results.json'), `${JSON.stringify(summary, null, 2)}\n`);

  const markdown = [
    '# Hasil Pengujian UI',
    '',
    `Base URL: ${baseUrl}`,
    `Total skenario: ${summary.total}`,
    `Berhasil: ${summary.passed}`,
    `Gagal: ${summary.failed}`,
    '',
    '| No | Role | Halaman | Status | Screenshot |',
    '|---|---|---|---|---|',
    ...results.map((result, index) => `| ${index + 1} | ${result.role} | ${result.path} | ${result.status} | ${result.screenshot} |`),
    '',
  ].join('\n');

  fs.writeFileSync(path.join(outDir, 'ui-test-results.md'), markdown);

  console.log(JSON.stringify({ total: summary.total, passed, failed }, null, 2));
  if (failed > 0) {
    process.exit(1);
  }
})().catch((error) => {
  console.error(error);
  process.exit(1);
});
