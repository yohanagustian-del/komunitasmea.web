# komunitasmea.web.id — tema `komunitasmea` + plugin `mea-router`

Brief lengkap: [`docs/BRIEF.md`](docs/BRIEF.md) (MEA SEO Room, 29 Okt 2026).

```
komunitasmea/   tema klasik PHP, tanpa Elementor (wp-content/themes/komunitasmea)
mea-router/     plugin CTA artikel + UTM + event GA4 (wp-content/plugins/mea-router)
docs/BRIEF.md   brief asli
```

> **Belum dipasang di mana pun.** Kode ini hanya diuji di WordPress 7.1 lokal (SQLite) dengan data contoh. Aturan brief §0 berlaku: pasang di **staging** dulu, jangan aktifkan di produksi tanpa persetujuan tertulis Yohan, tema lama jangan dihapus.

## Pasang di staging

```bash
cd komunitasmea.web
zip -r komunitasmea.zip komunitasmea
zip -r mea-router.zip mea-router
```

1. Plugins → Add New → Upload `mea-router.zip` → Activate. Aktivasi mengisi pengaturan bawaan dan daftar "Paling banyak dibaca" awal.
2. Appearance → Themes → Upload `komunitasmea.zip` → Activate. Tema lama tetap terpasang untuk rollback.
3. Settings → Reading: Front page tetap halaman **homepage (ID 102135)**. Tema selalu merender `front-page.php` di sana, walau halaman itu bertemplate Elementor Canvas, jadi URL dan meta Rank Math tidak berubah.
4. Cek Settings → MEA Router: nama kategori di bawah setiap daftar ID harus cocok (ID yang tidak ada ditandai merah).
5. Opsional: Appearance → Menus, isi 4 lokasi "Footer: …". Selama kosong, isi bawaan dari brief §3.9 yang tampil.

## Yang masih menunggu Yohan (§7)

Semua slot ini sudah ada dan **tersembunyi dari pengunjung** sampai diisi. Admin yang login melihat penanda kuning `[ISI: Yohan]`.

| Isi | Tempat mengisi |
|---|---|
| Copy final "Tentang komunitas" (§3.2). Sekarang draf dari prototipe. | Appearance → Customize → Komunitas MEA → Homepage: teks |
| Jawaban FAQ no. 7 (berbayar atau tidak). Item 7 tidak tampil dan tidak masuk schema selama kosong. | sama |
| Foto kegiatan asli, 3–6 (§3.5) | … → Homepage: foto kegiatan |
| Testimoni terverifikasi (§3.7) | … → Homepage: testimoni |
| Kontak footer | … → Footer |
| Publish Kebijakan Privasi (ID 104983). Tautan footer muncul otomatis setelah publish. | Pages |

## Perilaku penting

**Tema**
- Hero: H1 teks HTML. `poster.webp` (640×986, `fetchpriority="high"`) adalah LCP; `hero-1092.webp` hanya untuk desktop beresolusi tinggi. Video (`loop.webm` → `loop.mp4`) baru disisipkan `hero.js` setelah `load` + idle, hanya di layar ≥ 821px, tanpa `prefers-reduced-motion`, dan tanpa Save-Data. Video dijeda saat hero tidak terlihat.
- 6 ruangan = `<a>` biasa (bisa di-Tab, ada outline fokus), UTM `utm_campaign=homepage_hero`. Hover/fokus "Saya Seller" menyorot ruangan oranye, "Saya Affiliator" menyorot ruangan teal.
- Critical CSS (token, header, hero) di-inline. `main.css` adalah satu-satunya file CSS tema; di homepage dimuat tanpa memblokir render. Font self-host (OFL), `font-display: swap`, 2 file di-preload.
- Di homepage tema melepas CSS blok Gutenberg dan aset Elementor/ElementsKit (filter `komunitasmea_front_strip_prefixes`). Tema tidak memuat jQuery.
- `page.php`: halaman Elementor (`_elementor_edit_mode = builder`) dirender polos `the_content()` tanpa judul dan pembatas lebar, dan dipaksa mode terang (desain lama berlatar terang). Template Canvas/Full Width tetap ditangani Elementor.
- SEO: tema tidak mencetak title/meta/OG/schema sendiri (`title-tag` → Rank Math), kecuali JSON-LD **FAQPage di homepage saja**. Breadcrumb Rank Math dipakai kalau aktif.
- Dark mode mengikuti `prefers-color-scheme`.

**Plugin MEA Router**
- Jalur per post: override meta box → kategori. Sub-kategori menang atas induknya; kategori tak terpetakan mewarisi induk; seller + kreator = ganda; tidak terpetakan sama sekali = `default_path` (ganda).
- Kartu ringkas setelah paragraf ke-3 tingkat atas (paragraf di dalam kutipan, daftar, atau tabel tidak dihitung), hanya kalau masih ada ≥ 2 paragraf sesudahnya. Kartu lengkap di akhir artikel: di slot `do_action( 'mea_router_slot', 'end' )` kalau `single.php` tema ini yang merender (ia memasang filter `mea_router_use_slot`); kalau template lain yang merender (tema lain, template Elementor Pro), kartu ditempel ke akhir konten. Hanya post, tidak di page.
- UTM `utm_source=komunitasmea&utm_medium=referral&utm_campaign=artikel_<seller|kreator|ganda>&utm_content=<mid|end>_<post_id>`, query lama (`ref=AFAKADEMI-2992`) dipertahankan.
- Shortcode `[mea_cta path="seller|kreator|ganda" pos="end|mid"]`. Kalau shortcode dipakai di artikel, kartu mid otomatis tidak ditambahkan.
- Kolom "Jalur CTA" di daftar Posts untuk memeriksa 10 artikel sampel dengan cepat.
- Uninstall menghapus `mea_router_settings`, `mea_router_popular_kreator`, `mea_router_popular_jualan`, dan semua meta `_mea_router_path`.

**GA4**: semua tautan ber-`data-mea-dest` (CTA artikel, ruangan hero, tombol tangga, tautan keluar footer) mengirim `gtag('event', 'mea_cta_click', { mea_dest, mea_path, mea_pos, link_url })` lewat `gtag` dari Site Kit. Tanpa `gtag` tetapi ada `dataLayer` (GTM4WP), event dikirim sebagai `dataLayer.push({ event: 'mea_cta_click', … })`. Tanpa keduanya, tidak terjadi apa-apa. Daftarkan `mea_dest`, `mea_path`, dan `mea_pos` sebagai custom dimension (event-scoped) di GA4 supaya bisa dilaporkan.

## Keputusan yang sudah disetujui Yohan (29 Okt 2026)

1. **Teks di atas tombol oranye/teal memakai tinta gelap, bukan putih.** Putih di atas `#E8611A` hanya 3,4:1 dan di atas `#0F9E95` 3,3:1 (gagal WCAG AA). Tinta `#1D1A17` memberi 5,1:1 dan 5,2:1. Untuk teks kecil berwarna di atas kertas dipakai varian lebih gelap (`#B4470E`, `#0A746D`). Token brief tetap dipakai untuk isian, garis, dan aksen.
2. **Post yang punya Sosial Media (ID 1) plus satu kategori spesifik** memakai jalur spesifiknya (mis. Sosial Media + TikTok Affiliate → kreator). Ganda hanya kalau ada seller dan kreator sekaligus, atau kalau semua kategorinya umum. Bisa diubah per post lewat meta box.
3. Parameter event GA4 memakai awalan `mea_` (`mea_dest`, `mea_path`, `mea_pos`); ketiganya perlu didaftarkan sebagai custom dimension di GA4.
4. Kontak footer, FAQ 7, dan draf "Tentang" tidak menampilkan placeholder ke pengunjung (hanya ke admin), supaya halaman tidak bocor `[ISI: Yohan]` kalau go-live sebelum diisi.

Copy kartu CTA artikel dan tangga tetap bisa diubah di Settings → MEA Router dan di template (sapaan seller "Anda", kreator "kamu"; syarat MCN "minimal 1.000 follower, berlaku untuk MCN Shopee maupun TikTok").

## Temuan dari situs produksi (dibaca 30 Okt 2026, read-only)

Lewat koneksi WordPress "Jasa Digital Marketing Bandung", yang ternyata mengarah ke komunitasmea.web.id produksi. Tidak ada yang diubah.

- **Kategori**: ID, nama, induk, dan jumlah post sama persis dengan pemetaan di plugin (5710/5711 di bawah Shopee 1123/TikTok 905, dst.).
- **Banyak artikel diberi beberapa kategori sekaligus**, sering termasuk *Cara Memulai Affiliate* (5713). Contoh: 91471 (5713, 5709, 5710) dan 64269 (5708, 5711, 5713) jadi **ganda**; 104135 (5713, 5708) jadi **kreator**. Ini sesuai aturan brief, tapi artinya kolom "Untuk jualan" di homepage berisi artikel yang CTA-nya kreator atau ganda (sejalan dengan catatan §8).
- **12 artikel "Paling banyak dibaca" semuanya publish** (termasuk 92860).
- **Kebijakan Privasi (104983) masih draft** → tautan footer belum tampil.
- **Homepage 102135 bertemplate `elementor_header_footer`**; `front-page.php` tetap dipakai.
- **Tema lama: Soledad Child (induk Soledad 8.5.1)** = jalur rollback. Tidak ditemukan shortcode `[penci…` di isi post.
- **Plugin aktif yang relevan**: Elementor 4.3.5 + **Elementor Pro 4.2.1**, **Classic Editor** (meta box MEA Router tampil normal), Rank Math, Site Kit (GA4 481996454 + Search Console aktif), **GTM4WP**, **Debloat**, **Inline Related Posts**, Wordfence, Header Footer Code Manager.
- **Elementor Pro Theme Builder** menyimpan kondisi Single untuk *semua post* (template 101016) dan *semua page* (101046), tapi 101016 tidak ditemukan sebagai template dan 101046 ternyata revisi, jadi kemungkinan besar sisa lama yang tidak aktif. Belum dipastikan; lihat checklist.

## Hasil uji lokal (WordPress 7.1.4 + SQLite, data contoh)

Bukan pengganti uji staging: PageSpeed, Rank Math, Elementor asli, dan Cloudflare belum diuji.

- Homepage: CLS 0,00003 (mobile), LCP = poster, tanpa scroll horizontal di 390px. Video dimuat di desktop, tidak dimuat di mobile maupun reduced-motion. JS homepage: `hero.js` 1,9 KB + `mea-router.js` 0,6 KB. `main.css` 13,7 KB, critical CSS 5,8 KB, CSS plugin 2,4 KB.
- 6 URL hero dan 6 URL tangga benar; Tab berurutan gudang → toko → rumah → studio → kos → warung; klik mengirim `mea_cta_click`.
- Jalur 25 post contoh sesuai aturan (seller, kreator, ganda, sub-kategori tak terpetakan, override off). Kartu mid tepat setelah paragraf ke-3 di Gutenberg dan Classic Editor, dilewati di artikel pendek; kartu end di slot tema.
- Halaman Elementor tiruan dirender polos dengan `data-theme="light"`; homepage tetap `front-page.php` walau halaman 102135 bertemplate Canvas.
- Menu footer: tautan keluar otomatis ber-UTM, UTM manual tidak ditimpa, tautan internal tidak diubah. Tautan Kebijakan privasi muncul hanya setelah halaman publish.
- Post draft di daftar populer dilewati. Tema tetap jalan tanpa plugin. Uninstall bersih. `debug.log` kosong setelah 50 page view.

## Checklist uji terima di staging (§5)

- [ ] PageSpeed mobile & desktop homepage: LCP < 2,5 s, CLS < 0,1, INP < 200 ms
- [ ] H1 terbaca dengan JS dimatikan; video tidak dimuat di mobile/reduced-motion (tab Network)
- [ ] 6 ruangan bisa diklik dan di-Tab, 6 URL UTM benar
- [ ] `gabung-mcn-mea`, `kelas-shopee`, `jasa-shopee`, `about-us`, `testimoni-mitra-kami` tampil normal
- [ ] 10 artikel sampel (tiap kategori §4): CTA mid & end benar (lihat kolom "Jalur CTA")
- [ ] Rich Results Test: schema Rank Math tetap keluar, FAQPage hanya di homepage, tidak ada schema ganda
- [ ] Light & dark mode rapi
- [ ] Log PHP bersih setelah 50 page view
- [ ] Elementor Pro → Templates → Theme Builder: pastikan tidak ada template Single Post/Page/Header/Footer yang aktif. Kondisi tersimpan menunjuk ke 101016 (semua post) dan 101046 (semua page). Kalau ternyata masih aktif, artikel dan halaman akan dirender Elementor, bukan `single.php`/`page.php` (CTA tetap muncul karena kartu akhir otomatis pindah ke akhir konten). Buka satu artikel dan pastikan `<body>` tidak memuat class `elementor-template-*` / `elementor-page-101016`
- [ ] Debloat: kecualikan `komunitasmea` dan `mea-router` dari "Remove Unused CSS" / optimasi JS, atau pastikan hasilnya tidak mengubah critical CSS, main.css async, dan hero.js
- [ ] Inline Related Posts: cek tidak menempel tepat di sebelah kartu CTA mid; kalau berdempetan, geser salah satunya
- [ ] GA4: klik CTA → event `mea_cta_click` muncul di DebugView (Site Kit gtag; kalau GTM dipakai, buat trigger Custom Event `mea_cta_click`)
