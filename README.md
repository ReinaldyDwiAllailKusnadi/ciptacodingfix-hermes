# CiptaCoding - Digital Studio, Software House & Management ERP

Website resmi, landing page, dan sistem manajemen studio terintegrasi CiptaCoding:
- **Web Publik**: Landing page, portofolio dinamis, katalog layanan, alur pemesanan, dan FAQ.
- **Sistem Keuangan (Finance ERP)**: Invoicing otomatis (DP, Termin, Pelunasan), Arus Kas Bulanan (Cashflow), Catatan Biaya Pengeluaran (Expenses), Laporan Laba/Rugi.
- **Manajemen Operasional & CRM**: Database Klien, Status Pengerjaan Proyek IT, Timeline/Deadline, dan Direct Chat WhatsApp.
- **Showcase CMS**: Pengelolaan katalog portofolio proyek yang otomatis tersinkronisasi ke web publik.

## 🚀 Teknologi Utama
- **Backend**: Laravel 11 (PHP 8.5)
- **Frontend Admin**: Inertia.js v2 + Vue 3 (Single Page Application, snappy tanpa reload)
- **Styling**: Tailwind CSS v3 + Material Symbols Icons
- **Visualisasi & Charts**: Chart.js + Vue-ChartJS
- **Database**: SQLite (Lokal) / MySQL (cPanel Produksi)
- **Routing Client**: Tighten Ziggy (Vue `route()` helper)

## 🛠️ Menjalankan di Lokal

### 1. Jalankan Server Web (Laravel)
```bash
php artisan serve --port=8000
```
Server akan aktif di: **[http://localhost:8000](http://localhost:8000)**

### 2. Mode Pengembangan Frontend (Hot Reload Vite - Opsional)
Jika ingin mengubah komponen Vue/Tailwind dengan hot reload:
```bash
npm run dev
```
Untuk mengompilasi aset produksi:
```bash
npm run build
```

---

## 🔐 Kredensial Login Admin Studio:
- **URL Login**: [http://localhost:8000/login](http://localhost:8000/login)
- **Email**: `admin@ciptacoding.com`
- **Password**: `ciptacoding2026`

---

## 🌐 Halaman Utama Sistem:
- **Landing Page Publik**: [http://localhost:8000/](http://localhost:8000/) atau [http://localhost:8000/index.html](http://localhost:8000/index.html)
- **Portofolio Publik**: [http://localhost:8000/portofolio.html](http://localhost:8000/portofolio.html)
- **Dashboard Studio**: [http://localhost:8000/dashboard](http://localhost:8000/dashboard)
- **Invoice & Tagihan**: [http://localhost:8000/finance/invoices](http://localhost:8000/finance/invoices)
- **Catatan Pengeluaran**: [http://localhost:8000/finance/expenses](http://localhost:8000/finance/expenses)
- **Laporan Arus Kas & Laba Rugi**: [http://localhost:8000/finance/cashflow](http://localhost:8000/finance/cashflow)
- **Manajemen Proyek**: [http://localhost:8000/projects](http://localhost:8000/projects)
- **Buku Kontak Klien (CRM)**: [http://localhost:8000/clients](http://localhost:8000/clients)
- **Portofolio Manager**: [http://localhost:8000/portfolio-manager](http://localhost:8000/portfolio-manager)

---

## 📞 Kontak
- WhatsApp: 0877-2305-7547
- Instagram: @ciptacoding
