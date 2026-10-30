# Brief Claude Code — Tema `komunitasmea` + Plugin `mea-router`

Situs: **https://www.komunitasmea.web.id** (WordPress 7.1.3, Rank Math aktif, Site Kit aktif, Cloudflare di depan).
Pemilik & approver: Yohan Agustian (CEO MEA Grup). Disusun dari MEA SEO Room, 29 Okt 2026.

## 0. Aturan kerja (wajib dibaca dulu)

1. **Kerjakan di staging dulu.** Jangan aktifkan tema atau plugin di produksi tanpa persetujuan tertulis Yohan.
2. **Jangan publish konten apa pun.** Halaman/post baru dibuat sebagai draft.
3. **Jangan menyentuh redirect Rank Math, slug, atau status post.** Semua perubahan URL lewat MEA SEO Room.
4. **Jangan mengarang angka.** Angka yang boleh tampil hanya: 12.000+ kreator, 3.000+ seller, syarat MCN minimal 1.000 follower (Shopee & TikTok). Angka lain = placeholder `[ISI: Yohan]`.
5. **Testimoni hanya yang asli.** Ambil dari halaman lama `testimoni-mitra-kami` (ID 50517) atau dari Yohan. Tidak boleh membuat testimoni, wajah, atau screenshot hasil palsu.
6. **Elementor tetap aktif sebagai plugin.** Ada ±80 halaman lama (landing page kelas, webinar, jasa) yang dibangun dengan Elementor. Tema baru tidak memakai Elementor, tapi harus tetap bisa merender halaman Elementor lama dengan benar.
7. Tema lama tetap terpasang (jangan dihapus) sebagai jalur rollback.

## 1. Tujuan

Homepage = **rumah Komunitas MEA** (12.000+ kreator, 3.000+ seller), bukan halaman MCN. Tugas homepage dan artikel adalah mengarahkan pengunjung ke jalur konversi yang tepat:

| Jalur | Sapaan | Anak tangga | Tujuan |
|---|---|---|---|
| Seller | "Anda" | Belajar | https://skilskul.co.id/ |
| Seller | "Anda" | Didampingi | https://ordal.co.id/ |
| Seller | "Anda" | Dikelola (dikerjakan tim MEA) | https://meagency.co.id/ |
| Kreator/Affiliator | "kamu" | Mulai | https://affiliateakademi.id/?ref=AFAKADEMI-2992 |
| Kreator/Affiliator | "kamu" | Gabung MCN (min. 1.000 follower) | https://meamcn.com/ |
| Kreator — **cabang terpisah** | "kamu" | Kreator kuliner & TikTok GO | https://meago.id/ |

Ordal vs MEA Agency **tidak dibedakan dengan batas omzet**, melainkan dengan wording: "mau didampingi" → Ordal, "mau dikerjakan tim MEA" → MEA Agency.

Prioritas bisnis untuk urutan CTA ganda: **kreator (MCN/MEAGO!) dulu**, seller kedua.

## 2. Tema `komunitasmea` (classic PHP theme, tanpa Elementor)

### Struktur file
```
komunitasmea/
  style.css            (header tema saja)
  functions.php        (setup, enqueue, image sizes, menu, hapus bloat)
  front-page.php       (homepage — section di §3)
  single.php           (artikel + slot CTA MEA Router)
  archive.php / category.php / search.php / 404.php / page.php
  header.php / footer.php
  template-parts/      (hero, ladder-seller, ladder-kreator, faq, post-card, footer-ecosystem)
  assets/css/main.css  (satu file, tanpa framework)
  assets/js/hero.js    (≤ 3 KB, video lazy + hover jalur)
  assets/hero/         (poster.webp, loop.mp4, loop.webm, hero-1092.webp)
  assets/fonts/        (self-host Bricolage Grotesque 800, Plus Jakarta Sans 400/500/700, woff2, subset latin)
```

### Kompatibilitas
- `page.php` harus menjalankan `the_content()` polos, supaya halaman Elementor (template Canvas/Full Width) tetap tampil. Uji minimal 5 halaman lama: `gabung-mcn-mea`, `kelas-shopee`, `jasa-shopee`, `about-us`, `testimoni-mitra-kami`.
- Rank Math: jangan cetak `<title>`, meta description, OG, atau schema sendiri, **kecuali FAQPage di homepage** (lihat §3.8). Pakai `add_theme_support('title-tag')`.
- Gutenberg core blocks harus tampil rapi di `single.php`: heading, list, tabel, gambar, quote.

### Anggaran performa (diukur di staging, mobile, PageSpeed)
- LCP < 2,5 detik, CLS < 0,1, INP < 200 ms.
- JS di homepage tanpa video ≤ 15 KB. Tanpa jQuery di frontend.
- CSS tunggal ≤ 40 KB, *critical CSS* hero di-inline.
- Font `font-display: swap`, preload hanya 2 file woff2.

### Desain
- Token: oranye seller `#E8611A`, teal kreator `#0F9E95`, kertas `#F4F1EC`, tinta `#1D1A17`, abu `#5E574F`. Dark mode wajib (lihat prototipe).
- Tipografi: Bricolage Grotesque 800 untuk H1/H2, Plus Jakarta Sans untuk teks.
- Acuan visual: **prototipe hero** https://claude.ai/artifact/7rcsTH7fWSTfeC9JYwHmKs (salin perilaku dan CSS-nya, bukan base64-nya).

## 3. Homepage — urutan section

Urutan final (disetujui 29 Okt 2026): hero → tentang komunitas → tangga seller → tangga kreator → paling banyak dibaca (12 artikel) → kegiatan komunitas → testimoni → FAQ → fat footer. Seluruh urutan, copy draf, dan perilaku bisa dilihat di prototipe homepage penuh (link di §2).

### 3.1 Hero
- Kiri: logo teks "Komunitas MEA", **H1 teks HTML**: `Rumah bagi 12.000+ kreator dan 3.000+ seller`, lede, dua tombol: "Saya Seller" (→ `#tangga-seller`) dan "Saya Affiliator" (→ `#tangga-kreator`).
- Kanan: kartu gambar rasio 640:986, sudut 22px.
  - `<img src=poster.webp fetchpriority="high" width="640" height="986">` sebagai LCP.
  - Video (`loop.webm`, fallback `loop.mp4`, `muted loop playsinline autoplay`) **baru disisipkan setelah `load` + idle**, hanya kalau `min-width: 821px` dan tidak `prefers-reduced-motion`. Mobile = poster saja.
- Area ruangan bisa diklik (`<a>` absolut, persentase terhadap kartu):

| Ruangan | left | top | width | height | Label | Tujuan + `utm_content` |
|---|---|---|---|---|---|---|
| Gudang (kiri atas) | 0% | 0% | 50% | 32% | Dikelola / MEA Agency | meagency.co.id — `seller_dikelola` |
| Toko (kiri tengah) | 0% | 33% | 33% | 33% | Didampingi / Ordal | ordal.co.id — `seller_didampingi` |
| Rumah (kiri bawah) | 0% | 67% | 50% | 33% | Belajar / SkilSkul | skilskul.co.id — `seller_belajar` |
| Studio live (kanan atas) | 67% | 0% | 33% | 38% | Gabung / MCN MEA | meamcn.com — `kreator_gabung` |
| Kamar kos (kanan tengah) | 67% | 39% | 33% | 28% | Mulai / Affiliate Akademi | affiliateakademi.id (pertahankan `ref=AFAKADEMI-2992`) — `kreator_mulai` |
| Warung (kanan bawah) | 67% | 68% | 33% | 32% | Kreator kuliner / MEAGO! | meago.id — `kreator_meago` |

  UTM hero: `utm_source=komunitasmea&utm_medium=referral&utm_campaign=homepage_hero&utm_content=<lihat tabel>`.
  Hover tombol "Saya Seller" menyorot ruangan oranye dan meredupkan ruangan teal, begitu juga sebaliknya.

### 3.2 Tentang komunitas
Dua sampai tiga paragraf: siapa Komunitas MEA, dibangun MEA sejak 2020 di Bandung, 12.000+ kreator dan 3.000+ seller. Copy final: `[ISI: Yohan]` atau draf yang disetujui.

### 3.3 Tangga Seller (`id="tangga-seller"`, sapaan "Anda")
Tiga anak tangga (Belajar → Didampingi → Dikelola), masing-masing berisi satu kalimat siapa yang cocok + tombol ke tujuan (UTM `utm_campaign=homepage_tangga`). Wording Ordal vs Agency sesuai §1.

### 3.4 Tangga Kreator (`id="tangga-kreator"`, sapaan "kamu")
Mulai (Affiliate Akademi) → Gabung MCN MEA, sebutkan syarat **minimal 1.000 follower Shopee & TikTok**. Yang belum 1.000 follower diarahkan ke Affiliate Akademi. **MEAGO! ditampilkan sebagai cabang samping**, bukan anak tangga ke-3: "Kreator kuliner atau ikut TikTok GO? → MEAGO!".

### 3.5 Kegiatan komunitas
Grid 3–6 foto kegiatan **asli** (upload dari tim MEA). Sebelum foto tersedia, section disembunyikan (jangan pakai foto AI atau stok).

### 3.6 Paling banyak dibaca (12 artikel kurasi, `id="artikel"`)
Dua kolom bernomor: "Untuk kreator" (garis teal) dan "Untuk jualan" (garis oranye), masing-masing 6 artikel. Tanpa thumbnail (judul + label kategori saja) supaya ringan. Tautan "Semua artikel kreator" → `/category/cara-memulai-affiliate/`, "Semua artikel jualan" → `/category/tips-jualan-di-marketplace/`.

**Sumber daftar = kurasi manual**, disimpan sebagai daftar ID post di pengaturan MEA Router (`mea_router_popular_kreator`, `mea_router_popular_jualan`), diperbarui MEA SEO Room tiap audit bulanan berdasarkan klik Google Search Console. **Jangan** pakai plugin penghitung view.

Daftar awal (klik GSC 28 Apr – 27 Okt 2026):

| # | Untuk kreator (ID) | Untuk jualan (ID) |
|---|---|---|
| 1 | 103943 bio-tiktok-affiliate-aesthetic | 104135 hook-jualan-baju-anak |
| 2 | 103795 nama-tiktok-affiliate-yang-bagus | 104144 hook-jualan-celana |
| 3 | 103899 contoh-bio-tiktok-affiliate-fashion | 91471 judul-live-shopee-yang-menarik |
| 4 | 103202 contoh-deskripsi-koleksiku-di-shopee-affiliate | 64269 contoh-cta-tiktok |
| 5 | 99015 komisi-shopee-affiliate-tiba-tiba-hilang | 59853 contoh-deskripsi-grup-wa |
| 6 | 104395 cek-gmv-shopee-affiliate | 92860 kenapa-viewers-shopee-video-menurun |

Judul ditarik dari post (bukan di-hardcode). Kalau post di daftar tidak lagi berstatus publish, lewati dan tampilkan sisanya.

### 3.7 Testimoni
Hanya testimoni asli (sumber: halaman ID 50517 atau dari Yohan). Jika belum terverifikasi, section disembunyikan.

### 3.8 FAQ + schema FAQPage
Render `<details>` dan JSON-LD FAQPage **hanya di homepage**. Draf (perlu dicek Yohan sebelum go-live):
1. Apa itu Komunitas MEA?
2. Saya seller pemula, mulai dari mana? → SkilSkul.
3. Apa bedanya didampingi Ordal dan dikelola MEA Agency?
4. Apa syarat gabung MCN MEA? → minimal 1.000 follower di Shopee dan TikTok.
5. Belum punya 1.000 follower, bisa ikut? → Mulai dari Affiliate Akademi.
6. MEAGO! itu untuk siapa? → kreator kuliner dan program TikTok GO.
7. Apakah gabung komunitas berbayar? → `[ISI: Yohan]`.

### 3.9 Fat footer (semua halaman, bukan hanya homepage)
Empat kolom (mobile: 2 kolom, lalu 1 kolom di < 520px):

| Kolom | Isi |
|---|---|
| Komunitas MEA | Deskripsi singkat, "Dikelola oleh PT MEA Digital Marketing", tautan Tentang kami (`/about-us/`), kontak `[ISI: Yohan]` |
| Untuk seller | SkilSkul, Ordal, MEA Agency + arsip kategori Shopee Seller, TikTok Seller, Tips Jualan di Marketplace |
| Untuk kreator | Affiliate Akademi (pertahankan `ref=AFAKADEMI-2992`), MCN MEA, MEAGO! + arsip kategori TikTok Affiliate, Shopee Affiliate, Cara Memulai Affiliate |
| Ekosistem MEA Grup | Linkreator, Sebari, Shopeasytools (`/shopeasytools/`), TikTok @101bisnis, Instagram @yohanagustian |

Baris bawah: `© <tahun berjalan> PT MEA Digital Marketing` + tautan **Kebijakan privasi** (`/kebijakan-privasi/`, halaman ID 104983, saat ini masih draft; tampilkan tautan hanya jika halaman sudah publish).
Tautan keluar di footer diberi UTM `utm_campaign=footer&utm_content=<brand>`. Footer dibuat lewat menu WordPress (4 lokasi menu) supaya bisa diedit tanpa kode.

## 4. Plugin `mea-router`

Tujuan: setiap artikel otomatis punya CTA ke halaman konversi yang tepat, dengan UTM konsisten dan event GA4.

### Pemetaan kategori → jalur (ID kategori produksi)

| Kategori (ID) | Jumlah post | Jalur |
|---|---|---|
| Shopee Seller (5710), TikTok Seller (5711), Tips Jualan di Marketplace (904), Cara Memulai Bisnis Online (1323) | 282 / 272 / 159 / 413 | **Seller** |
| TikTok Affiliate (5708), Shopee Affiliate (5709), Cara Memulai Affiliate (5713) | 227 / 173 / 200 | **Kreator** |
| Sosial Media (1), Kisah Sukses (3924), induk TikTok (905) / Shopee (1123) tanpa sub-kategori | 609 / 28 / – | **Ganda** (kreator dulu, seller kedua) |

Aturan: kalau post punya kategori di dua jalur, gunakan **Ganda**. Sub-kategori lebih menentukan daripada induk. Override per post lewat meta box (`_mea_router_path`: auto / seller / kreator / ganda / off).

### CTA
- **Jalur Seller:** CTA utama SkilSkul ("Belajar jualan dari dasar"), CTA sekunder teks "Mau didampingi? → Ordal" dan "Mau dikerjakan tim MEA? → MEA Agency".
- **Jalur Kreator:** CTA utama Affiliate Akademi, sekunder "Sudah 1.000+ follower? → Gabung MCN MEA", plus tautan MEAGO! untuk kreator kuliner.
- **Ganda:** dua kartu berdampingan, kreator di kiri.
- Posisi: setelah paragraf ke-3 (kartu ringkas) dan di akhir artikel (kartu lengkap). Tidak tampil di halaman/page, hanya post.
- Copy CTA bisa diedit di halaman pengaturan (Settings → MEA Router). Default copy di atas.

### UTM builder
`utm_source=komunitasmea&utm_medium=referral&utm_campaign=artikel_<seller|kreator|ganda>&utm_content=<posisi>_<post_id>`
- `<posisi>` = `mid` atau `end`.
- Pertahankan query yang sudah ada di URL tujuan (contoh: `ref=AFAKADEMI-2992`), tambahkan UTM dengan `&`.

### Tracking
- Setiap tombol diberi `data-mea-dest`, `data-mea-path`, `data-mea-pos`.
- Kirim event GA4 `mea_cta_click` dengan parameter yang sama, lewat `gtag` yang sudah dipasang Site Kit. Kalau `gtag` tidak ada, jangan error.

### Teknis
- Output statis di HTML (aman untuk cache, tanpa AJAX).
- CSS plugin ≤ 6 KB, ikut token warna tema. Tanpa JS kecuali listener klik ≤ 1 KB.
- Shortcode `[mea_cta path="seller|kreator|ganda"]` untuk penempatan manual.
- Uninstall bersih (hapus option & post meta milik plugin).

## 5. Uji terima (wajib lulus di staging)

- [ ] Homepage lulus anggaran performa §2, di mobile dan desktop.
- [ ] H1 teks, terbaca tanpa JS. Video tidak dimuat di mobile dan reduced-motion.
- [ ] Keenam area ruangan bisa diklik dan difokus dengan keyboard; UTM benar (cek 6 URL).
- [ ] 5 halaman Elementor lama tampil normal.
- [ ] 10 artikel sampel (dari tiap kategori di §4) menampilkan CTA yang benar di posisi mid dan end.
- [ ] Rank Math: title, meta, canonical, dan schema artikel tetap keluar; FAQPage hanya di homepage; tidak ada schema ganda (uji Rich Results Test).
- [ ] Dark mode dan light mode rapi.
- [ ] Tidak ada error di log PHP setelah 50 page view.

## 6. Go-live (hanya setelah Yohan menyetujui)

1. Backup penuh (file + database).
2. Aktifkan plugin `mea-router`, lalu tema `komunitasmea`.
3. Set homepage statis: pertahankan halaman `homepage` (ID 102135) sebagai Front Page supaya URL dan meta Rank Math tetap.
4. Bersihkan cache (situs + Cloudflare jika APO aktif).
5. Laporkan ke MEA SEO Room: URL yang diuji, skor PageSpeed, dan screenshot.
6. Rollback: aktifkan kembali tema lama.

## 7. Masih perlu dari Yohan

- Copy final §3.2 dan jawaban FAQ no. 7.
- Foto kegiatan komunitas asli (§3.5).
- Konfirmasi testimoni yang boleh dipakai (§3.7).
- Info hosting dan akses staging (subdomain, SFTP/SSH, atau panel).
- Lengkapi dan publish draft **Kebijakan Privasi** (ID 104983): tanggal berlaku, alamat, email, WhatsApp.

## 8. Catatan untuk MEA SEO Room (bukan tugas Claude Code)

- Tidak ada artikel berkategori seller yang masuk 40 besar traffic. Kolom "Untuk jualan" sementara diisi artikel affiliate yang isinya relevan untuk seller. Ini jadi agenda riset artikel seller baru.
