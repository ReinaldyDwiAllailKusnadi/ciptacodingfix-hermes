# DOKUMENTASI LENGKAP SERAH TERIMA PROYEK (HANDOVER UNTUK AI SELANJUTNYA)
**Proyek:** CiptaCoding - Digital Studio & Software House  
**Repository GitHub:** `https://github.com/ReinaldyDwiAllailKusnadi/ciptacodingfix.git` (Branch: `main`)  
**Terakhir Diperbarui:** 16 September 2026

---

## 1. IKHTISAR & IDENTITAS BISNIS CIPTACODING
- **Brand Name:** CiptaCoding
- **Tagline / Identitas:** Digital Studio & Software House
- **Target Audiens Utama:** Bisnis, UMKM, Personal, dan Klien Kebutuhan Project IT (Website, Aplikasi Kasir/POS, ERP, Custom Software, API Gateway, Debugging/Fix Bug, serta Konsultasi Teknologi).
- **Tone of Voice:** Profesional, modern, transparan, solutif, mudah dipahami orang awam, tidak kaku, dan **BUKAN template AI**.
- **PANTANGAN KONTEN UTAMA (STRICT RULES):**
  1. ❌ **JANGAN PERNAH** menggunakan bahasa joki akademik/skripsi/tugas/mahasiswa/sidang.
  2. ❌ **JANGAN** menggunakan kata "privat", "privasi" (ganti dengan istilah formal industri: **"Keamanan Data"** atau **"Kerahasiaan Proyek"**).
  3. ❌ **JANGAN** menggunakan kata "diskusi" secara berlebihan jika konteksnya adalah layanan (gunakan **"Konsultasi"**).
  4. ❌ **JANGAN** mengarang testimoni fiktif dengan klaim berlebihan yang tidak dapat dibuktikan.
  5. ❌ **JANGAN** mengubah tata letak section atau merusak responsivitas mobile/desktop.

---

## 2. STRUKTUR ARSITEKTUR
Semua file halaman berada di folder **`public/`** (docroot, format Laravel-style). Untuk testing lokal: `cd public && python3 -m http.server 8000` (docroot = folder `public/`), lalu buka `http://localhost:8000/`.

Tidak ada lagi langkah "copy subfolder ke root" — cukup edit file di dalam `public/`.

### Daftar 5 File Halaman Utama:
1. `index.html` → Halaman Utama / Beranda (**MASTER DESIGN SYSTEM / GOLD STANDARD**)
2. `layanan.html` → Katalog Layanan Digital & Project IT
3. `cara-order.html` → Alur & Form Estimasi Pemesanan Proyek
4. `portofolio.html` → Showroom Proyek & Studi Kasus
5. `tanya-jawab.html` → Tanya Jawab (FAQ) Lengkap seputar Layanan

### Aturan Edit:
Edit file HTML langsung di root directory. Pastikan hanya `css/style.css` yang jadi sumber CSS (style.css duplikat di root sudah dihapus).

---

## 3. DESIGN SYSTEM & VISUAL TOKENS (GOLD STANDARD)
Semua halaman harus 100% konsisten mengikuti standar `index.html`:

### A. Palet Warna Resmi
- **Primary Electric Blue:** `#0062ff` (Brand Color, tombol utama, highlight kata "Coding")
- **Primary Hover:** `#0051d4`
- **Dark Surface / Navy:** `#08143a` (Warna teks heading, Top Bar, Footer, Card gelap)
- **Text Muted / Body Secondary:** `#495874`
- **Border / Outline:** `#d2dced`
- **Soft Blue Background / Badge:** `#edf4ff`
- **Body Background:** `#f8faff`
- **Green Accent (Online / Sukses):** `#10b981` (Emerald-500)

### B. Tipografi
- **Font Utama & Display:** `Plus Jakarta Sans`, sans-serif (CDN Google Fonts)
- **Font Kode / Teknis:** `JetBrains Mono`, monospace

### C. Logo & Favicon Resmi
- **File Asset:** `assets/images/logo.png` (Monogram CC transparan berkualitas tinggi: C atas Navy `#08143a`, C bawah Biru `#0062ff`).
- **Dark Mode / Background Gelap:** `assets/images/logo-dark-mode.png` atau `logo.png` di dalam wadah kontainer `bg-white p-1 rounded-lg`.
- **Favicon di Head:**
  ```html
  <link rel="icon" type="image/png" href="assets/images/logo.png"/>
  ```
- **Teks Brand di Samping Logo:**
  - Di Navbar (terang):
    ```html
    <span class="font-display text-xl sm:text-2xl text-[#08143a] font-extrabold tracking-tight">Cipta<span class="text-[#0062ff]">Coding</span></span>
    ```
  - Di Footer (gelap):
    ```html
    <span class="font-display text-xl text-white font-extrabold">Cipta<span class="text-[#0062ff]">Coding</span></span>
    ```

---

## 4. KONSISTENSI NAVBAR & FOOTER DI SELURUH HALAMAN

### A. Navbar Header (Sticky)
- Logo resmi CiptaCoding (`assets/images/logo.png`) + Teks `CiptaCoding`.
- **5 Menu Navigasi:**
  1. `Beranda` (`index.html`)
  2. `Layanan` (`layanan.html`)
  3. `Cara Order` (`cara-order.html`)
  4. `Portofolio` (`portofolio.html`)
  5. `Tanya Jawab` (`tanya-jawab.html`)
- Active State: `bg-[#edf4ff] text-[#0062ff] font-bold`
- CTA Button: `Konsultasi Project` (diarahkan ke WhatsApp).

### B. Footer
- Link menu cepat menggunakan label: **`Cara Order`** (BUKAN "Alur Order").
- Jam operasional resmi: **`Setiap Hari (24/7 Siap Melayani)`** (menggantikan jam kerja terbatas).
- Garansi dan keamanan: **`Source Code & Data Aman` • `Garansi Perbaikan Bug`**.

---

## 5. REKAP PERUBAHAN & PERBAIKAN TERBARU
1. **Pembaruan Footer:**
   - Mengubah link navigasi footer dari "Alur Order" menjadi "Cara Order" di semua 5 file HTML.
   - Mengubah jam operasional menjadi "Setiap Hari (24/7 Siap Melayani)".
2. **Refinement Copywriting Beranda (`index.html`):**
   - Menghapus seluruh kata "privasi" dan "privat" -> diganti dengan **"Keamanan Data: Terjamin & Aman"** dan **"Keamanan & Kerahasiaan Data"**.
   - Menghapus kata "diskusi" yang kaku -> diganti dengan **"Konsultasi 1-on-1"**, **"Konsultasi Kasir"**, **"Konsultasi & Estimasi"**, **"Konsultasi Terbuka & Gratis"**.
   - Wording langkah order: Step 1 = **"Kirim Brief Kebutuhan"**, Step 2 = **"Konsultasi & Estimasi"**.
3. **Pemasangan Logo Baru:**
   - Mengekstrak ikon monogram CC dari gambar desain asli milik user.
   - Mengubahnya menjadi format PNG berlatar transparan tanpa artefak putih (`assets/images/logo.png`).
   - Menerapkannya ke seluruh Navbar, Footer, serta Favicon tab browser di 5 halaman.
4. **Git Repository Status:**
   - Repositori lokal dan remote GitHub sudah tersinkron penuh di commit branch `main`.

---

## 6. CATATAN PEMBERSIHAN (16 September 2026)
1. Gitlink `ciptacodingfix` (submodule rusak tanpa .gitmodules) dihapus dari index; folder kosongnya juga dihapus.
2. `style.css` duplikat di root dihapus — satu-satunya sumber CSS adalah `css/style.css` (isi keduanya identik saat dihapus).
3. README.md ditulis ulang: referensi "bimbingan tugas/skripsi" dan filter "Skripsi" dihapus (melanggar aturan konten no.1 dan tidak ada di halaman aktual).
4. Seluruh wording "diskusi" dalam konteks layanan diganti "konsultasi" di 4 halaman.
5. `index.html`: efek tilt-card kini hanya aktif di perangkat ber-hover (`(hover: hover)` guard), sama seperti 4 halaman lain.

---

## 7. PANDUAN PENTING UNTUK AI SELANJUTNYA
Jika Anda (AI berikutnya) menerima instruksi atau request lanjutan dari user:
1. **Pertahankan Estetika & Responsivitas:**
   Desain saat ini sudah melalui audit responsif dan aesthetic audit yang ketat. Jangan pernah merusak grid Tailwind, padding, atau utility classes yang ada.
2. **Gunakan Wording Konsisten:**
   Gunakan istilah: *Digital Studio, Software House, Konsultasi, Keamanan Data, Kerahasiaan Proyek, Garansi Perbaikan Bug, Siap Digunakan*.
3. **Workflow Git Wajib:**
   Jika ada file yang diubah:
   - Edit file di root repository.
   - Commit, lakukan `git pull --rebase origin main` lalu `git push origin main`.
