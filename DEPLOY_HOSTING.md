# Panduan Deploy CiptaCoding ke ciptacoding.com (hosting idwebhost / LiteSpeed)

Kondisi terverifikasi (16 September 2026):
- Domain `ciptacoding.com` → A record `203.161.184.124`, nameserver `ns1/ns2.idwebhost.com`.
- Hosting LiteSpeed AKTIF dengan HTTPS valid (HTTP/2, h3), TAPI document root masih KOSONG
  (template default idwebhost tampil, `robots.txt`/`sitemap.xml` 404).
- VPS kerja Hermes (159.223.84.40) BUKAN server hosting — tidak ada akses SSH/FTP/cPanel
  ke hosting idwebhost dari VPS ini.

## Struktur repository (format Laravel-style)

Semua file yang di-serve berada di dalam folder `public/` (docroot), sesuai konvensi
Laravel. Root repo hanya berisi dokumentasi + folder `public/`:

```
public/
  index.html            (beranda)
  layanan.html
  cara-order.html
  portofolio.html
  tanya-jawab.html
  sitemap.xml
  robots.txt
  .htaccess             (HTTP→HTTPS 301 + www→apex 301)
  css/style.css
  assets/images/...     (logo, foto, ilustrasi SVG)
  assets/icons/...      (ikon tech stack, self-hosted)
  assets/fonts/
```

Website 100% statis (HTML/CSS/JS) — tidak butuh PHP, Node.js, database, atau
build process. File gambar & ikon SEMUA self-hosted (tanpa CDN pihak ketiga
kecuali Tailwind + Google Fonts).

## Paket deploy siap unggah

`/root/ciptacoding-deploy.zip` (±2.2 MB) berisi seluruh repo termasuk folder `public/`.
Isi `public/` = isi document root.

## Langkah deploy ke cPanel idwebhost (LiteSpeed)

Aplikasi CiptaCoding menggunakan arsitektur Laravel standar:
- Folder di luar `public/` (seperti `app/`, `bootstrap/`, `config/`, `database/`, `vendor/`, dll.) ditaruh di folder utama akun cPanel (satu tingkat di atas `public_html`).
- Isi folder `public/` ditaruh di dalam `public_html`.

### Cara Deploy Cepat via cPanel File Manager:
1. Jalankan `npm run build` di lokal (sudah ter-compile di folder `public/build/assets/`).
2. Kompres seluruh proyek menjadi file `.zip` (abaikan folder `node_modules` untuk menghemat ukuran zip, folder `vendor` bisa diikutsertakan atau jalankan `composer install --no-dev` di cPanel).
3. Di cPanel:
   - Upload file zip ke root home folder (`/home/username/`).
   - Extract zip tersebut ke folder misalnya `/home/username/ciptacoding_app/`.
   - Di menu **Domains / Subdomains**, ubah Document Root domain `ciptacoding.com` menjadi `/home/username/ciptacoding_app/public`.
   - Atau jika document root terkunci di `public_html`, letakkan isi folder `public/` di dalam `public_html` dan ubah path `require __DIR__.'/../vendor/autoload.php'` di `public_html/index.php`.
4. Setup Database di cPanel:
   - Buka **MySQL Database Wizard**, buat database dan user baru (misal: `user_cipta`, pass: `...`).
   - Salin `.env.example` menjadi `.env` di cPanel:
     ```bash
     cp .env.example .env
     ```
   - Sesuaikan konfigurasi database di file `.env`:
     ```env
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://ciptacoding.com

     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_DATABASE=nama_db_cpanel
     DB_USERNAME=user_db_cpanel
     DB_PASSWORD=password_db_cpanel
     ```
   - Buka Terminal cPanel dan jalankan:
     ```bash
     php artisan key:generate
     php artisan migrate --force
     php artisan db:seed --force
     php artisan storage:link
     php artisan config:cache
     php artisan route:cache
     php artisan view:cache
     ```
5. Akses Admin:
   - URL Login: `https://ciptacoding.com/login`
   - Email: `admin@ciptacoding.com`
   - Password: `ciptacoding2026` (Segera ubah password setelah login pertama)
6. Selesai! Halaman publik (`index.html`, `portofolio.html`, dll) langsung aktif secepat kilat (0.01s), dan dashboard Studio Management `/dashboard` berjalan mulus dengan Inertia.js Vue 3.

## Verifikasi setelah upload (jalankan dari VPS Hermes)

```
for u in / /layanan.html /portofolio.html /cara-order.html /tanya-jawab.html /sitemap.xml /robots.txt; do
  curl -s -o /dev/null -w "%{http_code} $u\n" https://ciptacoding.com$u
done
curl -sI http://ciptacoding.com | head -3          # harus 301 ke https
curl -sI https://www.ciptacoding.com | head -3     # harus 301 ke apex
```

## Alternatif jika ingin host di VPS Hermes (159.223.84.40)

Tidak direkomendasikan saat ini: butuh ubah A record domain di panel DNS idwebhost
(mengelola DNS domain, berisiko mempengaruhi email yang saat ini di-host di sana),
plus setup reverse proxy + SSL di VPS. Pilih ini hanya jika ingin pindah hosting.
