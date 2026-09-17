# CiptaCoding - Digital Studio & Software House

Website resmi dan landing page CiptaCoding dengan arsitektur bersih, responsive, modern, dan performa tinggi.

## 🚀 Fitur Utama
- **Beranda (`index.html`)**: Hero section interaktif, value proposition, statistik pencapaian, running marquee tech stack logo (Devicon), ringkasan layanan, standar keunggulan, ulasan klien, dan WhatsApp CTA.
- **Layanan (`layanan.html`)**: Katalog 6 layanan utama IT (Custom Software, Debugging & Refactoring, Website Bisnis, Kasir POS & ERP, Fix Bug, Integrasi API/Payment/Cloud) dengan filter tab dinamis dan SLA 4 tahap pengerjaan.
- **Cara Order (`cara-order.html`)**: 4 langkah alur pemesanan terstruktur + form pembuat draf pesan WhatsApp otomatis.
- **Portofolio (`portofolio.html`)**: Galeri studi kasus proyek dengan filter tab dinamis (Bisnis Website, Mobile, IT Project).
- **Tanya Jawab (`tanya-jawab.html`)**: Pusat bantuan FAQ dengan pencarian live, quick chips, dan accordion expand/collapse.

## 📁 Struktur Folder (Laravel-style — semua file yang di-serve ada di `public/`)
```text
├── public/                 # document root / isi hosting
│   ├── index.html
│   ├── layanan.html
│   ├── cara-order.html
│   ├── portofolio.html
│   ├── tanya-jawab.html
│   ├── sitemap.xml
│   ├── robots.txt
│   ├── .htaccess           # HTTP→HTTPS + www→apex (kompatibel LiteSpeed)
│   ├── css/
│   │   └── style.css       # Design system tokens (satu-satunya sumber CSS)
│   └── assets/
│       ├── images/         # logo.png, logo-dark-mode.png, foto & ilustrasi SVG (self-hosted)
│       ├── icons/          # ikon tech stack (self-hosted)
│       └── fonts/
├── README.md
├── HANDOVER_AI_NOTES.md
├── DEPLOY_HOSTING.md
└── .gitignore
```

Catatan: situs statis tanpa build step — Tailwind dimuat via CDN, token desain ada di `tailwind.config` inline di tiap halaman plus `css/style.css`. Tidak butuh PHP/Node/database.

## 🛠️ Menjalankan Secara Lokal
Jalankan local web server dari folder `public/`:
```bash
cd public && python3 -m http.server 8000
```
Lalu buka `http://localhost:8000` di browser.

## 📞 Kontak
- WhatsApp: 0877-2305-7547
- Instagram: @ciptacoding
