const http = require('http');
const fs = require('fs');
const path = require('path');

const PORT = process.env.PORT || 8000;
const PUBLIC_DIR = path.join(__dirname, 'public');
const DATA_FILE = path.join(PUBLIC_DIR, 'data', 'portofolio.json');
const UPLOAD_DIR = path.join(PUBLIC_DIR, 'assets', 'images');

// Ensure directories exist
if (!fs.existsSync(path.join(PUBLIC_DIR, 'data'))) {
  fs.mkdirSync(path.join(PUBLIC_DIR, 'data'), { recursive: true });
}
if (!fs.existsSync(UPLOAD_DIR)) {
  fs.mkdirSync(UPLOAD_DIR, { recursive: true });
}

const MIME_TYPES = {
  '.html': 'text/html; charset=utf-8',
  '.css': 'text/css; charset=utf-8',
  '.js': 'application/javascript; charset=utf-8',
  '.json': 'application/json; charset=utf-8',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon',
  '.webp': 'image/webp',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ttf': 'font/ttf',
  '.xml': 'application/xml; charset=utf-8',
  '.txt': 'text/plain; charset=utf-8'
};

// Helper: Read portfolio data
function getPortfolioData() {
  try {
    if (!fs.existsSync(DATA_FILE)) {
      return [];
    }
    const raw = fs.readFileSync(DATA_FILE, 'utf8');
    return JSON.parse(raw);
  } catch (e) {
    console.error('Error reading portfolio data:', e);
    return [];
  }
}

// Helper: Save portfolio data
function savePortfolioData(data) {
  fs.writeFileSync(DATA_FILE, JSON.stringify(data, null, 2), 'utf8');
}

// Helper: Parse request body
function parseRequestBody(req) {
  return new Promise((resolve, reject) => {
    let body = '';
    req.on('data', chunk => {
      body += chunk.toString();
      if (body.length > 25 * 1024 * 1024) { // 25MB max
        reject(new Error('Body too large'));
      }
    });
    req.on('end', () => {
      try {
        resolve(body ? JSON.parse(body) : {});
      } catch (err) {
        resolve({});
      }
    });
    req.on('error', reject);
  });
}

// Helper: Send JSON response
function sendJson(res, statusCode, data) {
  res.writeHead(statusCode, {
    'Content-Type': 'application/json; charset=utf-8',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
    'Access-Control-Allow-Headers': 'Content-Type, Authorization'
  });
  res.end(JSON.stringify(data));
}

const server = http.createServer(async (req, res) => {
  // CORS Preflight
  if (req.method === 'OPTIONS') {
    res.writeHead(204, {
      'Access-Control-Allow-Origin': '*',
      'Access-Control-Allow-Methods': 'GET, POST, PUT, DELETE, OPTIONS',
      'Access-Control-Allow-Headers': 'Content-Type, Authorization'
    });
    return res.end();
  }

  const parsedUrl = new URL(req.url, `http://${req.headers.host}`);
  const pathname = parsedUrl.pathname;

  // ==========================================
  // API ROUTING
  // ==========================================

  // 1. Auth Login
  if (pathname === '/api/login' && req.method === 'POST') {
    const { username, password } = await parseRequestBody(req);
    // Default admin credential (can be changed via env)
    const validUser = process.env.ADMIN_USER || 'admin';
    const validPass = process.env.ADMIN_PASS || 'ciptacoding2026';

    if (username === validUser && password === validPass) {
      // Return a pseudo token
      return sendJson(res, 200, {
        success: true,
        message: 'Login berhasil',
        token: 'ciptacoding-adm-token-' + Date.now()
      });
    } else {
      return sendJson(res, 401, {
        success: false,
        message: 'Username atau password salah!'
      });
    }
  }

  // 2. Portfolio API: GET
  if (pathname === '/api/portofolio' && req.method === 'GET') {
    const list = getPortfolioData();
    return sendJson(res, 200, { success: true, data: list });
  }

  // 3. Portfolio API: POST (Create)
  if (pathname === '/api/portofolio' && req.method === 'POST') {
    try {
      const item = await parseRequestBody(req);
      if (!item.title) {
        return sendJson(res, 400, { success: false, message: 'Judul proyek wajib diisi' });
      }

      const list = getPortfolioData();
      const newId = list.length > 0 ? Math.max(...list.map(p => Number(p.id) || 0)) + 1 : 1;
      
      const newProject = {
        id: newId,
        code: item.code || `PRJ-${String(newId).padStart(2, '0')}`,
        title: item.title,
        categories: Array.isArray(item.categories) ? item.categories : ['bisnis'],
        categoryLabel: item.categoryLabel || 'Sistem Digital',
        description: item.description || '',
        image: item.image || 'assets/images/pos-erp-multi-outlet.svg',
        badgeTop: item.badgeTop || { icon: '', dot: true, text: 'Live Project' },
        badgeBottom: item.badgeBottom || 'Digital Solution',
        techStack: Array.isArray(item.techStack) ? item.techStack : [],
        highlight: item.highlight || 'Siap Digunakan',
        highlightIcon: item.highlightIcon || 'check_circle',
        ctaText: item.ctaText || 'Konsultasi',
        whatsappText: item.whatsappText || `Halo CiptaCoding, saya tertarik dengan proyek ${item.title}`,
        order: Number(item.order) || newId
      };

      list.push(newProject);
      savePortfolioData(list);

      return sendJson(res, 201, { success: true, data: newProject, message: 'Proyek berhasil ditambahkan' });
    } catch (e) {
      return sendJson(res, 500, { success: false, message: e.message });
    }
  }

  // 4. Portfolio API: PUT (Update)
  const putMatch = pathname.match(/^\/api\/portofolio\/(\w+)$/);
  if (putMatch && req.method === 'PUT') {
    try {
      const id = putMatch[1];
      const updates = await parseRequestBody(req);
      const list = getPortfolioData();
      const index = list.findIndex(p => String(p.id) === String(id));

      if (index === -1) {
        return sendJson(res, 404, { success: false, message: 'Proyek tidak ditemukan' });
      }

      list[index] = {
        ...list[index],
        ...updates,
        id: list[index].id // preserve id
      };

      savePortfolioData(list);
      return sendJson(res, 200, { success: true, data: list[index], message: 'Proyek berhasil diperbarui' });
    } catch (e) {
      return sendJson(res, 500, { success: false, message: e.message });
    }
  }

  // 5. Portfolio API: DELETE
  const deleteMatch = pathname.match(/^\/api\/portofolio\/(\w+)$/);
  if (deleteMatch && req.method === 'DELETE') {
    try {
      const id = deleteMatch[1];
      let list = getPortfolioData();
      const initialLength = list.length;
      list = list.filter(p => String(p.id) !== String(id));

      if (list.length === initialLength) {
        return sendJson(res, 404, { success: false, message: 'Proyek tidak ditemukan' });
      }

      savePortfolioData(list);
      return sendJson(res, 200, { success: true, message: 'Proyek berhasil dihapus' });
    } catch (e) {
      return sendJson(res, 500, { success: false, message: e.message });
    }
  }

  // 6. Upload Image API
  if (pathname === '/api/upload' && req.method === 'POST') {
    try {
      const { filename, base64Data } = await parseRequestBody(req);
      if (!base64Data) {
        return sendJson(res, 400, { success: false, message: 'Data gambar tidak valid' });
      }

      // Extract raw base64
      const matches = base64Data.match(/^data:([A-Za-z-+\/]+);base64,(.+)$/);
      const fileBuffer = Buffer.from(matches ? matches[2] : base64Data, 'base64');

      const ext = path.extname(filename || 'image.jpg') || '.jpg';
      const cleanName = 'upload-' + Date.now() + ext;
      const targetFile = path.join(UPLOAD_DIR, cleanName);

      fs.writeFileSync(targetFile, fileBuffer);

      return sendJson(res, 200, {
        success: true,
        url: `assets/images/${cleanName}`,
        message: 'Gambar berhasil diunggah'
      });
    } catch (e) {
      return sendJson(res, 500, { success: false, message: e.message });
    }
  }

  // ==========================================
  // STATIC FILE SERVING
  // ==========================================
  let urlPath = decodeURI(pathname);
  if (urlPath === '/') {
    urlPath = '/index.html';
  }

  const filePath = path.normalize(path.join(PUBLIC_DIR, urlPath));

  // Security check: prevent directory traversal
  if (!filePath.startsWith(PUBLIC_DIR)) {
    res.writeHead(403, { 'Content-Type': 'text/plain; charset=utf-8' });
    return res.end('403 Forbidden');
  }

  fs.stat(filePath, (err, stats) => {
    let targetPath = filePath;

    if (err) {
      // Try resolving with .html if extension is missing
      if (!path.extname(filePath)) {
        const potentialHtml = filePath + '.html';
        if (fs.existsSync(potentialHtml)) {
          targetPath = potentialHtml;
        } else {
          res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
          return res.end('404 Not Found');
        }
      } else {
        res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
        return res.end('404 Not Found');
      }
    } else if (stats.isDirectory()) {
      targetPath = path.join(filePath, 'index.html');
      if (!fs.existsSync(targetPath)) {
        res.writeHead(404, { 'Content-Type': 'text/plain; charset=utf-8' });
        return res.end('404 Not Found');
      }
    }

    const ext = path.extname(targetPath).toLowerCase();
    const contentType = MIME_TYPES[ext] || 'application/octet-stream';

    fs.readFile(targetPath, (readErr, content) => {
      if (readErr) {
        res.writeHead(500, { 'Content-Type': 'text/plain; charset=utf-8' });
        return res.end('500 Internal Server Error');
      }
      res.writeHead(200, {
        'Content-Type': contentType,
        'Cache-Control': 'no-cache'
      });
      res.end(content);
    });
  });
});

server.listen(PORT, () => {
  console.log(`\n🚀 CiptaCoding Local Server is running!`);
  console.log(`📡 URL: http://localhost:${PORT}`);
  console.log(`📁 Serving directory: ${PUBLIC_DIR}\n`);
});
