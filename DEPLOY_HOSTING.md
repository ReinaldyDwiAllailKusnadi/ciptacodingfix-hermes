# Panduan Deploy CiptaCoding ke ciptacoding.com (hosting idwebhost / LiteSpeed)

Kondisi terverifikasi (16 September 2026):
- Domain `ciptacoding.com` → A record `203.161.184.124`, nameserver `ns1/ns2.idwebhost.com`.
- Hosting LiteSpeed AKTIF dengan HTTPS valid (HTTP/2, h3), TAPI document root masih KOSONG
  (template default idwebhost tampil, `robots.txt`/`sitemap.xml` 404).
- VPS kerja Hermes (159.223.84.40) BUKAN server hosting — tidak ada akses SSH/FTP/cPanel
  ke hosting idwebhost dari VPS ini.

## Paket deploy siap unggah

`/root/ciptacoding-deploy.tar.gz` (±1.9 MB) berisi seluruh situs:
5 halaman HTML, `css/`, `assets/`, `robots.txt`, `sitemap.xml`, `.htaccess`.

## Langkah deploy manual (butuh akses hosting — salah satu dari):

### Opsi A — cPanel File Manager (paling mudah)
1. Login cPanel idwebhost (URL & kredensial dari email aktifasi hosting / panel member idwebhost).
2. Buka **File Manager** → masuk ke folder domain (biasanya `public_html`).
3. Upload `ciptacoding-deploy.tar.gz` → klik kanan → **Extract**.
4. Pastikan `index.html` langsung di dalam `public_html` (bukan di dalam subfolder).
5. Buka https://ciptacoding.com — selesai.

### Opsi B — FTP
1. Upload isi tarball (diekstrak dulu di komputer lokal) ke `public_html` via FileZilla.
2. Sama seperti Opsi A langkah 4-5.

### Opsi C — beri akses SSH/FTP/cPanel ke Hermes
Jika kredensial hosting diberikan (misal via vault/panel), deploy bisa diotomasi penuh.

## Yang sudah disiapkan & diuji dari sisi repo

- `.htaccess`: HTTP→HTTPS 301 + www→apex 301. Diuji penuh di Apache lokal (9 skenario PASS:
  redirect apex/www, path & file terjaga, kasus proxy X-Forwarded-Proto tidak menyebabkan loop,
  HTTPS langsung 200, aset 200). Kompatibel LiteSpeed.
- `sitemap.xml` (5 URL publik) + `robots.txt` sudah ada di root repo.
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
