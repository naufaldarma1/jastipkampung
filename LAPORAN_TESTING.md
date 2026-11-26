# Laporan Pengujian (Testing Report) Aplikasi Web JastipKampung
*Berdasarkan Perspektif Pembeli (POV Pembeli) dan Perspektif Jastiper (POV Jastiper) pada 15 Halaman Aplikasi & Prototipe Stitch*

---

## 1. Identitas Proyek & Lingkungan Pengujian

| Komponen | Keterangan |
| :--- | :--- |
| **Nama Aplikasi** | **JastipKampung** — Platform Pre-Order & Jasa Titip Kuliner Kampung Halaman Mahasiswa UNNES |
| **Framework & Bahasa** | Laravel Framework `11.56.1` · PHP `8.4.28` |
| **Frontend & Bundler** | Tailwind CSS, Alpine.js, Vite `5.4.21` |
| **Database** | SQLite (produksi lokal: `database/database.sqlite`; pengujian otomatis: `:memory:` via `phpunit.xml`) |
| **Referensi Prototipe** | Paket Export Google Stitch (`stitch_jastipkampung_marketplace_design_system.zip` — 15 Halaman + `DESIGN.md`) |
| **Tanggal Pengujian** | 26 November 2026 |
| **Tujuan Pengujian** | Memverifikasi seluruh alur fungsional, otorisasi peran, sistem penilaian toko/produk ala Shopee, serta kesiapan *deployment* produksi dari sudut pandang **Pembeli** dan **Jastiper**. |

---

## 2. Ruang Lingkup & Metode Pengujian

Pengujian dilakukan menggunakan tiga pendekatan yang saling melengkapi:

1. **Automated Unit & Feature Testing (`php artisan test` / PHPUnit):**
   Menguji fungsionalitas *end-to-end* pada tingkat HTTP request, `RoleMiddleware` (`pembeli` vs `jastiper`), validasi input, transaksi database, sistem rating, dan proteksi alur bisnis.
2. **Black-Box Functional Testing per POV (15 Halaman):**
   Menguji setiap halaman aplikasi baik dari alur **POV Pembeli** maupun **POV Jastiper**, dipetakan langsung ke 15 folder tampilan hasil export Stitch (`code.html` & `screen.png`).
3. **Production Build & Cache Verification:**
   Menguji kompilasi aset frontend (`npm run build`) serta kompatibilitas *caching* konfigurasi, rute, dan template Blade (`config:cache`, `route:cache`, `view:cache`) untuk simulasi server produksi.

---

## 3. Pemetaan 15 Halaman Aplikasi terhadap POV Pengguna

Berdasarkan aturan akses pada `routes/web.php`, 15 halaman di proyek JastipKampung terbagi sebagai berikut:

- **Halaman Umum / Pra-Login (3 Halaman — Shared):**
  1. `jastipkampung_home_landing_page` (`/`)
  2. `jastipkampung_masuk_login` (`/login`)
  3. `jastipkampung_daftar_register` (`/register`)
- **Alur Utama POV Pembeli (9 Halaman):**
  4. `jastipkampung_dashboard_pembeli` (`/dashboard-pembeli` — *Eksklusif Pembeli*)
  5. `jastipkampung_katalog_pre_order` (`/katalog` — *Utama Pembeli; read-only untuk Jastiper*)
  6. `jastipkampung_profil_toko_jastiper_rizky_f.` (`/jastiper/{slug}` — *Utama Pembeli; pratinjau toko untuk Jastiper*)
  7. `jastipkampung_keranjang_pesanan` (`/keranjang` — *Eksklusif Pembeli*)
  8. `jastipkampung_checkout_pesanan` (`/checkout` — *Eksklusif Pembeli*)
  9. `jastipkampung_pembayaran_pesanan` (`/pembayaran/{reference}` — *Eksklusif Pembeli*)
  10. `jastipkampung_konfirmasi_pembayaran_terkirim_struk` (`/pembayaran/{reference}/struk` — *Eksklusif Pembeli*)
  11. `jastipkampung_pesanan_saya_pelacak_titipan` (`/pesanan` — *Utama Pembeli*)
  12. `jastipkampung_jadi_jastiper` (`/jadi-jastiper` — *Transisi Pembeli menjadi Jastiper*)
- **Alur Utama POV Jastiper (3 Halaman Inti + 2 Halaman Pantau/Pratinjau):**
  13. `jastipkampung_dashboard_jastiper` (`/dashboard-jastiper` — *Eksklusif Jastiper*)
  14. `jastipkampung_buat_po_perjalanan` (`/dashboard-jastiper/po/baru` — *Eksklusif Jastiper*)
  15. `jastipkampung_profil_akun_reputasi_jastiper` (`/profile` — *Kartu Reputasi & Info Toko khusus POV Jastiper*)
  *(Ditambah pemantauan `/katalog` mode read-only & pratinjau `/jastiper/{slug}`)*

---

## 4. Pengujian Berdasarkan POV Pembeli

Pengujian POV Pembeli berfokus pada alur mencari kuliner kampung, menilai kredibilitas jastiper, bertransaksi hingga konfirmasi bayar, melacak titipan 5 tahap, dan memberikan rating produk serta toko.

### 4.1 Matriks Pengujian POV Pembeli

| ID | Halaman & Folder Stitch (`screen.png`) | Route | Skenario Pengujian | Ekspektasi Hasil | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :---: |
| **PB-01** | Home / Landing Page<br>(`jastipkampung_home_landing_page`) | `/` | Mengakses halaman beranda sebagai tamu, memeriksa katalog unggulan, rating bintang, dan tombol *Lihat Semua Jadwal Rute Jastip*. | Halaman tampil lengkap dengan 4 kartu PO unggulan, rating (`★ 4.9`), nama jastiper tertaut ke toko, dan tombol CTA mengarah ke `/katalog`. | Sesuai ekspektasi | **PASS** |
| **PB-02** | Daftar & Masuk (Pembeli)<br>(`jastipkampung_daftar_register`, `jastipkampung_masuk_login`) | `/register`, `/login` | Mendaftar dengan memilih peran **Daftar sebagai Pembeli**, lalu login kembali menggunakan akun Pembeli. | Akun tersimpan dengan `role = pembeli`; setelah registrasi/login berhasil, pengguna langsung diarahkan ke `/katalog`. | Sesuai ekspektasi | **PASS** |
| **PB-03** | Dashboard Pembeli<br>(`jastipkampung_dashboard_pembeli`) | `/dashboard-pembeli` | Membuka halaman Dashboard Pembeli melalui menu navigasi atau `/dashboard`. | Menampilkan banner sambutan Pembeli serta kartu akses cepat ke **Katalog** dan **Pesanan Saya**. | Sesuai ekspektasi | **PASS** |
| **PB-04** | Katalog Pre-Order<br>(`jastipkampung_katalog_pre_order`) | `/katalog` | Filter kota (`Solo`), pencarian kata kunci (`Mendoan`), urutkan (`★ Rating Tertinggi`), serta validasi jumlah melebihi sisa slot (`10` pada sisa `4` slot). | Filter/pencarian/pengurutan menampilkan item yang sesuai; input melebihi kuota ditolak dengan pesan validasi sisa kuota; input valid masuk ke keranjang. | Sesuai ekspektasi | **PASS** |
| **PB-05** | Profil Toko Jastiper<br>(`jastipkampung_profil_toko_jastiper_rizky_f.`) | `/jastiper/{slug}` | Mengklik badge toko jastiper (`★ 4.9 Toko`) di katalog untuk melihat metrik kepercayaan, etalase PO, dan filter ulasan `?star=4`. | Menampilkan header toko terverifikasi KTM, 6 metrik performa, etalase PO aktif, ringkasan skor bintang, dan daftar ulasan terfilter bintang. | Sesuai ekspektasi | **PASS** |
| **PB-06** | Keranjang Pesanan<br>(`jastipkampung_keranjang_pesanan`) | `/keranjang` | Mengubah jumlah item di keranjang (`2` menjadi `1`), mengecek perhitungan subtotal (`Rp 35.000 + Rp 5.000`), dan menghapus item. | Subtotal & estimasi total diperbarui akurat (`Rp 40.000`) dan tombol `Lanjut Checkout →` aktif saat keranjang terisi. | Sesuai ekspektasi | **PASS** |
| **PB-07** | Checkout Pesanan<br>(`jastipkampung_checkout_pesanan`) | `/checkout` | Memastikan titik kumpul UNNES sesuai jadwal jastiper, memeriksa ringkasan pesanan, dan memilih metode pembayaran (`QRIS` / `Transfer Bank` / `COD`). | Pesanan tersimpan dengan `checkout_reference` unik, status awal `pending`, `payment_status = unpaid` (atau `due_on_pickup` untuk COD), keranjang dikosongkan. | Sesuai ekspektasi | **PASS** |
| **PB-08** | Pembayaran Pesanan<br>(`jastipkampung_pembayaran_pesanan`) | `/pembayaran/{reference}` | Membuka halaman instruksi QRIS dan menekan tombol `Saya Sudah Membayar — Kirim Konfirmasi`. | Status pembayaran berubah menjadi `submitted` (menunggu verifikasi jastiper) dan pengguna diarahkan ke halaman struk konfirmasi. | Sesuai ekspektasi | **PASS** |
| **PB-09** | Struk Konfirmasi Bayar<br>(`jastipkampung_konfirmasi_pembayaran_terkirim_struk`) | `/pembayaran/{reference}/struk` | Memeriksa kode referensi pesanan, total tagihan, daftar item, titik pengambilan, dan tombol `Lihat Dashboard Pesanan →`. | Seluruh ringkasan konfirmasi tampil akurat dan tombol mengarahkan ke `/pesanan`. | Sesuai ekspektasi | **PASS** |
| **PB-10** | Pesanan Saya & Penilaian<br>(`jastipkampung_pesanan_saya_pelacak_titipan`) | `/pesanan` | Menguji larangan batal setelah bayar dikirim (`submitted`), melacak 5 tahap status, klik `✓ Pesanan Diterima`, dan mengirim rating produk (`5/5`) + toko (`4/5`). | Pesanan `submitted` tidak bisa dibatalkan sepihak; ulasan ditolak sebelum status `completed`; setelah diterima, penilaian tersimpan di tabel `reviews` dan ulasan ganda ditolak. | Sesuai ekspektasi | **PASS** |
| **PB-11** | Jadi Jastiper (Upgrade Peran)<br>(`jastipkampung_jadi_jastiper`) | `/jadi-jastiper` | Pembeli yang sudah login membuka halaman Jadi Jastiper dan menekan `Aktifkan Akun Jastiper`. | Peran akun berubah dari `pembeli` menjadi `jastiper` dan pengguna langsung diarahkan ke `/dashboard-jastiper`. | Sesuai ekspektasi | **PASS** |
| **PB-12** | Uji Negatif Keamanan Pembeli | `/dashboard-jastiper` | Akun Pembeli mencoba mengakses langsung URL `/dashboard-jastiper` atau `/dashboard-jastiper/po/baru`. | `RoleMiddleware` menolak akses dengan status HTTP `403 Forbidden`. | Sesuai ekspektasi | **PASS** |

---

## 5. Pengujian Berdasarkan POV Jastiper

Pengujian POV Jastiper berfokus pada alur membuka PO perjalanan, mengelola ketersediaan PO di katalog, memverifikasi pembayaran pembeli, memperbarui 5 tahap pengiriman secara berurutan, serta memantau reputasi toko.

### 5.1 Matriks Pengujian POV Jastiper

| ID | Halaman & Folder Stitch (`screen.png`) | Route | Skenario Pengujian | Ekspektasi Hasil | Hasil Aktual | Status |
| :--- | :--- | :--- | :--- | :--- | :--- | :---: |
| **JT-01** | Daftar & Masuk (Jastiper)<br>(`jastipkampung_daftar_register`, `jastipkampung_masuk_login`) | `/register`, `/login` | Mendaftar dengan memilih peran **Daftar sebagai Jastiper**, lalu login ulang sebagai Jastiper. | Akun tersimpan dengan `role = jastiper`; setelah registrasi/login, pengguna langsung diarahkan ke `/dashboard-jastiper`. | Sesuai ekspektasi | **PASS** |
| **JT-02** | Buat PO Perjalanan<br>(`jastipkampung_buat_po_perjalanan`) | `/dashboard-jastiper/po/baru` | Mengisi formulir PO baru (produk, asal `Solo`, tujuan `Sekaran, UNNES`, kuota `10`, harga `25000`, biaya titip `5000`, deadline & jadwal kembali valid, titik temu) lalu klik `Terbitkan PO`. | PO tersimpan dengan status `open`, pengguna kembali ke `/dashboard-jastiper`, dan PO otomatis tampil di `/katalog`. | Sesuai ekspektasi | **PASS** |
| **JT-03** | Katalog Mode Pantau Jastiper<br>(`jastipkampung_katalog_pre_order`) | `/katalog` | Jastiper membuka halaman katalog untuk mengecek penayangan PO dan mencoba endpoint order PO miliknya sendiri. | Tombol tambah keranjang disembunyikan & diganti badge `Mode Jastiper · Kelola PO di Dashboard`; pemesanan ke PO sendiri ditolak dengan `403 Forbidden`. | Sesuai ekspektasi | **PASS** |
| **JT-04** | Dashboard Jastiper — KPI & Status PO<br>(`jastipkampung_dashboard_jastiper`) | `/dashboard-jastiper` | Memeriksa 4 kartu statistik (`Total PO`, `PO Aktif`, `Permintaan masuk`, `Rating Toko ★`), lalu menekan tombol `Tutup PO` dan `Buka lagi`. | Angka KPI akurat; status trip berubah menjadi `closed` lalu kembali `open` sesuai validasi batas waktu PO. | Sesuai ekspektasi | **PASS** |
| **JT-05** | Dashboard Jastiper — Verifikasi Pembayaran<br>(`jastipkampung_dashboard_jastiper`) | `/dashboard-jastiper` | Jastiper mencoba konfirmasi pesanan yang pembayarannya belum diverifikasi (`submitted`), lalu klik `Tandai Pembayaran Diterima`. | Konfirmasi ditolak sebelum pembayaran diverifikasi; setelah diverifikasi (`payment_status = paid`), tombol konfirmasi pesanan aktif. | Sesuai ekspektasi | **PASS** |
| **JT-06** | Dashboard Jastiper — 5 Tahap Pengiriman<br>(`jastipkampung_dashboard_jastiper`) | `/dashboard-jastiper` | Mencoba melompati tahap status (`pending` langsung ke `in_transit`), lalu menjalankan tahapan berurutan (`confirmed` → `purchased` → `in_transit` → `ready_for_pickup` → `completed`). | Lompat tahap ditolak dengan pesan error validasi; update berurutan berhasil hingga `completed` dan ulasan pembeli muncul di kartu pesanan. | Sesuai ekspektasi | **PASS** |
| **JT-07** | Profil Akun & Reputasi Jastiper<br>(`jastipkampung_profil_akun_reputasi_jastiper`) | `/profile` | Membuka halaman `/profile` sebagai Jastiper, melihat kartu Rating Toko & distribusi bintang, lalu memperbarui `faculty`, `origin_city`, dan `store_bio`. | Kartu reputasi menampilkan rating toko, rating produk, total penilaian, dan distribusi 5–1 bintang; perubahan data toko tersimpan di tabel `users`. | Sesuai ekspektasi | **PASS** |
| **JT-08** | Pratinjau Halaman Toko Publik<br>(`jastipkampung_profil_toko_jastiper_rizky_f.`) | `/jastiper/{slug}` | Mengklik tombol `★ Lihat Toko Publik` dari Dashboard Jastiper atau `/profile` setelah mendapat ulasan baru. | Halaman publik toko menampilkan profil terbaru Jastiper, skor rating gabungan terbaru, serta komentar ulasan dari pembeli. | Sesuai ekspektasi | **PASS** |
| **JT-09** | Uji Negatif Keamanan Jastiper | `/keranjang`, `/dashboard-pembeli` | Akun Jastiper mencoba mengakses langsung halaman `/keranjang` atau `/dashboard-pembeli`. | `RoleMiddleware` menolak akses dengan status HTTP `403 Forbidden`. | Sesuai ekspektasi | **PASS** |

---

## 6. Hasil Automated Testing (PHPUnit)

Eksekusi seluruh *test suite* melalui perintah `php artisan test` menghasilkan **37 pengujian lulus (100% PASS)** dengan total **173 assertions** dalam waktu **21.21 detik** tanpa ada pengujian yang gagal atau dilewati.

### 6.1 Ringkasan per Test Suite

| No | Test Suite | Lokasi File | Jumlah Test | Cakupan POV | Status |
| :---: | :--- | :--- | :---: | :--- | :---: |
| 1 | Unit Example Test | `tests/Unit/ExampleTest.php` | 1 | Umum | PASS |
| 2 | Application Health & Home | `tests/Feature/ExampleTest.php` | 1 | Umum | PASS |
| 3 | Authentication | `tests/Feature/Auth/AuthenticationTest.php` | 5 | Pembeli & Jastiper | PASS |
| 4 | Registration | `tests/Feature/Auth/RegistrationTest.php` | 3 | Pembeli & Jastiper | PASS |
| 5 | Email Verification | `tests/Feature/Auth/EmailVerificationTest.php` | 3 | Umum | PASS |
| 6 | Password Confirmation | `tests/Feature/Auth/PasswordConfirmationTest.php` | 3 | Umum | PASS |
| 7 | Password Reset | `tests/Feature/Auth/PasswordResetTest.php` | 4 | Umum | PASS |
| 8 | Password Update | `tests/Feature/Auth/PasswordUpdateTest.php` | 2 | Umum | PASS |
| 9 | User & Jastiper Profile | `tests/Feature/ProfileTest.php` | 5 | Pembeli & Jastiper | PASS |
| 10 | **Rating Toko & Produk Jastiper** | `tests/Feature/JastiperRatingTest.php` | 3 | Pembeli & Jastiper | PASS |
| 11 | **End-to-End Marketplace Flow** | `tests/Feature/MarketplaceFlowTest.php` | 7 | Pembeli & Jastiper | PASS |
| **Total** | **11 Test Suites** | — | **37 Tests (173 Assertions)** | **Lengkap** | **PASS** |

### 6.2 Detail Pengujian Fitur Inti & Rating (`MarketplaceFlowTest` & `JastiperRatingTest`)

| Test Case | Deskripsi Skenario yang Diuji | Durasi | Hasil |
| :--- | :--- | :---: | :---: |
| `public pages and healthcheck render cleanly` | Halaman beranda (`/`), `/up` (healthcheck), redirect `/jadi-jastiper`, dan proteksi auth `/katalog`. | 0.18s | PASS |
| `authenticated users visiting guest auth pages are redirected by role` | Redirect otomatis Pembeli ke `/katalog` dan Jastiper ke `/dashboard-jastiper`; rute `/dashboard` mengarahkan sesuai peran. | 0.04s | PASS |
| `role middleware enforces strict access boundaries` | Pembeli ditolak (`403`) saat mengakses area Jastiper; Jastiper ditolak (`403`) saat mengakses area Pembeli/Keranjang; tampilan `/katalog` aman untuk Jastiper. | 0.77s | PASS |
| `pembeli can upgrade account to jastiper` | Pembeli dapat mengaktifkan akun menjadi Jastiper melalui `/jadi-jastiper`. | 0.23s | PASS |
| `katalog filter and search work` | Filter kota asal (`?filter=Solo`) dan pencarian nama makanan (`?search=Mendoan`) menampilkan data yang sesuai. | 0.05s | PASS |
| `full cart checkout qris payment and cancel rules` | Validasi kuota keranjang, ubah jumlah item, checkout QRIS (`Rp 40.000`), konfirmasi pembayaran, pencegahan pembatalan sepihak setelah bayar, hingga halaman struk & tracker. | 1.28s | PASS |
| `full jastiper trip lifecycle payment verification and five step fulfillment` | Pembuatan PO baru oleh Jastiper, larangan pesan PO sendiri (`403`), verifikasi pembayaran QRIS, penegakan urutan 5 tahap pesanan (`confirmed` → `purchased` → `in_transit` → `ready_for_pickup` → `completed`), serta tutup/buka ulang PO. | 0.19s | PASS |
| `katalog displays product and store ratings` | Katalog menampilkan skor bintang (`4.9`), jumlah penilaian, jumlah terjual, tautan profil toko (`/jastiper/{slug}`), dan pengurutan `?sort=rating`. | 3.03s | PASS |
| `public sample jastiper store profile and star filter work` | Halaman publik profil toko jastiper (`/jastiper/rizky-f`) dan filter ulasan berdasarkan bintang (`?star=4`) berjalan akurat. | 0.57s | PASS |
| `buyer can complete and review live jastiper order` | Penolakan ulasan sebelum pesanan selesai, konfirmasi `Pesanan Diterima`, pengiriman rating produk (`5/5`) dan pelayanan toko (`4/5`) + komentar, pencegahan ulasan ganda, serta pembaruan profil toko & form edit profil Jastiper. | 6.01s | PASS |

---

## 7. Log Temuan Bug & Perbaikan (*Issue Tracking*)

Selama proses audit dan pengujian pra-deploy, ditemukan **5 bug/celah kesiapan produksi** yang seluruhnya telah diperbaiki dan diverifikasi ulang:

| No | Temuan Masalah | Dampak Sebelum Perbaikan | Perbaikan yang Dilakukan | Status |
| :---: | :--- | :--- | :--- | :---: |
| **1** | `Route [dashboard] not defined` pada alur konfirmasi password, verifikasi email, dan redirect user login. | Memicu **Error 500** saat pengguna menyelesaikan konfirmasi password atau verifikasi email. | Menambahkan rute `/dashboard` (`DashboardController::redirect`) dan `redirectUsersTo` pada `bootstrap/app.php` yang mengarahkan sesuai peran (`pembeli` / `jastiper`). | **Fixed** |
| **2** | Konfigurasi default `APP_TIMEZONE=UTC` dan `APP_NAME=Laravel`. | Jadwal kembali & batas akhir PO bergeser +7 jam saat tampil sebagai WIB; judul aplikasi masih tertulis `Laravel`. | Mengubah default ke `Asia/Jakarta` dan `JastipKampung` di `config/app.php`, `.env`, dan `.env.example`. | **Fixed** |
| **3** | Tombol `Tambah ke Pesanan` di halaman `/katalog` tetap tampil untuk akun Jastiper. | Akun Jastiper yang menekan tombol tersebut terkena **Error 403** karena route keranjang khusus Pembeli. | Menampilkan tombol informatif `Mode Jastiper · Kelola PO di Dashboard` khusus ketika login sebagai Jastiper (`resources/views/katalog/index.blade.php`). | **Fixed** |
| **4** | Tautan mati (`href="#"`) di menu mobile dan tombol CTA Home. | Menu mobile `Jadi Jastiper` dan tombol `Lihat Semua Jadwal Rute Jastip` tidak berpindah halaman saat diklik. | Menghubungkan masing-masing ke `route('jastiper.join')` dan `route('katalog')`. | **Fixed** |
| **5** | Kesiapan *reverse proxy* HTTPS dan `route:cache` produksi. | Berisiko *Mixed Content* aset Vite saat deploy HTTPS serta closure route `/` tidak dapat di-cache. | Menambahkan `$middleware->trustProxies(at: '*')` di `bootstrap/app.php`, mengganti route `/` menjadi `Route::view()`, dan menambahkan filter `whereNull('trip_id')` pada hitung kuota produk contoh. | **Fixed** |

---

## 8. Hasil Uji Build & Cache Produksi

Pengujian simulasi lingkungan produksi dijalankan untuk memastikan aplikasi dapat dikompilasi dan di-*cache* tanpa error:

| Perintah Uji | Hasil yang Diamati | Status |
| :--- | :--- | :---: |
| `npm run build` | `59 modules transformed`, menghasilkan `manifest.json` (`0.27 kB`), `app-BFC-yVD-.css` (`53.44 kB`), `app-D99hXOC0.js` (`107.37 kB`) dalam `1.53s`. | **PASS** |
| `php artisan config:cache` | `Configuration cached successfully.` | **PASS** |
| `php artisan route:cache` | `Routes cached successfully.` | **PASS** |
| `php artisan view:cache` | `Blade templates cached successfully.` | **PASS** |
| `php artisan optimize:clear` | Seluruh cache sementara dibersihkan kembali tanpa error. | **PASS** |

---

## 9. Kesimpulan & Checklist Lampiran Screenshot (Per POV)

### Kesimpulan
Berdasarkan pengujian terpisah pada **POV Pembeli (`PB-01` s/d `PB-12`)** dan **POV Jastiper (`JT-01` s/d `JT-09`)**, hasil eksekusi automated testing (**37 test cases / 173 assertions lulus 100%**), perbaikan 5 bug pra-deploy, serta verifikasi build dan cache produksi, aplikasi **JastipKampung dinyatakan berjalan sesuai rancangan dan layak di-deploy ke lingkungan produksi**.

### Checklist Lampiran Gambar (`screen.png` dari Zip Stitch & Terminal)
Untuk melengkapi dokumen laporan akhir Anda, lampirkan gambar dari folder zip Stitch berikut sesuai babnya:
- **Lampiran A — Bukti Terminal Automated Test & Build:**
  - [ ] Screenshot terminal `php artisan test` (`37 passed, 173 assertions`)
  - [ ] Screenshot terminal `npm run build`
- **Lampiran B — Alur Umum & POV Pembeli (`stitch_jastipkampung_marketplace_design_system/`):**
  - [ ] `jastipkampung_home_landing_page/screen.png`
  - [ ] `jastipkampung_masuk_login/screen.png` & `jastipkampung_daftar_register/screen.png`
  - [ ] `jastipkampung_dashboard_pembeli/screen.png`
  - [ ] `jastipkampung_katalog_pre_order/screen.png`
  - [ ] `jastipkampung_profil_toko_jastiper_rizky_f./screen.png`
  - [ ] `jastipkampung_keranjang_pesanan/screen.png`
  - [ ] `jastipkampung_checkout_pesanan/screen.png`
  - [ ] `jastipkampung_pembayaran_pesanan/screen.png` & `jastipkampung_konfirmasi_pembayaran_terkirim_struk/screen.png`
  - [ ] `jastipkampung_pesanan_saya_pelacak_titipan/screen.png`
  - [ ] `jastipkampung_jadi_jastiper/screen.png`
- **Lampiran C — POV Jastiper (`stitch_jastipkampung_marketplace_design_system/`):**
  - [ ] `jastipkampung_dashboard_jastiper/screen.png`
  - [ ] `jastipkampung_buat_po_perjalanan/screen.png`
  - [ ] `jastipkampung_profil_akun_reputasi_jastiper/screen.png`
