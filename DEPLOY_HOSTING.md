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

## Langkah deploy (dilakukan sendiri via cPanel/hosting)

1. Login cPanel idwebhost → **File Manager** → masuk folder domain (`public_html`).
2. Upload `ciptacoding-deploy.zip` → Extract.
3. Arahkan document root domain ke folder `public/` yang terekstrak
   (di cPanel: Subdomains/Domains → Document Root, atau pindahkan isi `public/`
   langsung ke dalam `public_html` — keduanya valid; yang penting `index.html`
   berada tepat di document root).
4. Pastikan `.htaccess` ikut terekstrak (file tersembunyi — aktifkan "Show Hidden
   Files" di File Manager).
5. Buka https://ciptacoding.com — selesai.

Catatan:
- `.htaccess`: HTTP→HTTPS 301 + www→apex 301. Diuji penuh di Apache lokal
  (9 skenario PASS). Kompatibel LiteSpeed.
- SSL di hosting sudah aktif dari pihak hosting (tidak perlu setup tambahan).

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
