# Aplikasi Manajemen Keuangan & Administrasi RT.011

Aplikasi Manajemen RT.011 adalah sistem informasi berbasis web yang dirancang khusus untuk mempermudah Rukun Tetangga 011 / RW 003 Perumahan Karanggintung dalam mengelola keuangan iuran bulanan (Kas RT & Keamanan), pencatatan transaksi pengeluaran, pembuatan surat pengantar warga, cetak kwitansi pembayaran, serta pelaporan keuangan terpadu.

Aplikasi ini dimigrasikan dari sistem legacy berbasis HTML & localStorage menjadi aplikasi modern berbasis kerangka kerja PHP **Laravel 13** dan database **MySQL** yang aman, responsif, dan mudah dipelihara.

---

## Fitur Utama

1. **Dashboard Publik Warga**:
   - Ringkasan Keuangan Real-Time (Saldo Kas RT, Saldo Keamanan, dan Saldo Bersih).
   - Pencarian status iuran kas dan keamanan berdasarkan nama warga atau nomor rumah.
   - Fitur cetak kwitansi mandiri untuk warga.
2. **Autentikasi & Panel Admin**:
   - Pembatasan akses aman bagi pengurus RT dengan sistem login.
   - Fitur ganti password admin untuk menjaga keamanan akses.
3. **Manajemen Data Warga (CRUD)**:
   - Pengelolaan data warga RT.011 (66 Kepala Keluarga default).
   - Fitur cari warga dan reset data warga ke konfigurasi awal bawaan seeder.
4. **Input Transaksi & Validasi**:
   - Pencatatan Iuran Bulanan (Kas & Keamanan) warga per tahun berjalan dengan checklist pilihan bulan yang interaktif.
   - Pencatatan Transaksi Pengeluaran (Gaji Satpam, Kemalangan, Sakit, Konsumsi Rapat, Lain-lain).
   - **Validasi Cerdas**:
     - Deteksi dini pembayaran ganda pada bulan yang sama.
     - Pencegahan transaksi pengeluaran yang melebihi batas saldo aktif.
     - Pengamanan saldo agar tidak menjadi bernilai minus saat transaksi diedit atau dihapus.
5. **Ekspor Laporan & Surat**:
   - Ekspor Laporan Rekapitulasi Keuangan ke format Excel berisi 4 Sheet (Ringkasan, Rekap Bulanan, Status Warga, Daftar Transaksi).
   - Pembuatan Surat Pengantar Keterangan RT secara dinamis (Surat Pengantar Umum, Domisili, dan Usaha) dengan fitur cetak PDF/kertas langsung serta ekspor ke file Microsoft Word (.docx).
   - Cetak Kwitansi berformat ramah cetak (print-friendly) untuk arsip warga.


---

## Persyaratan Server (Requirements)

Untuk menjalankan aplikasi ini, server atau komputer Anda harus memenuhi persyaratan berikut:
- PHP >= 8.3 (dengan ekstensi: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML`, `ZIP`, `GD`)
- MySQL atau MariaDB Server
- Composer (Dependency Manager untuk PHP)
- Node.js & NPM (untuk kompilasi aset frontend)

---

## Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk memasang dan menjalankan proyek di komputer lokal Anda:

### 1. Kloning atau Dapatkan Project
Ekstrak atau buka folder proyek ini pada direktori kerja Anda (misalnya `f:/project reinaldy serius/rt 11/rt-11-app`).

### 2. Instalasi Dependensi PHP & JavaScript
Jalankan perintah berikut di terminal/Powershell Anda pada root direktori proyek:
```bash
# Instal dependensi PHP
composer install

# Instal dependensi Javascript/CSS
npm install
```

### 3. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env`:
```bash
copy .env.example .env
```
Buka file `.env` yang baru dibuat dan sesuaikan konfigurasi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rt11_db
DB_USERNAME=root
DB_PASSWORD=
```
*Catatan: Pastikan Anda telah membuat database kosong bernama `rt11_db` di server MySQL lokal Anda.*

### 4. Generate Application Key
Jalankan perintah berikut untuk mengamankan enkripsi session aplikasi:
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi Database & Seeder
Buat tabel-tabel di database sekaligus isi dengan data warga awal sebanyak 66 KK beserta transaksi contoh:
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server Lokal
Kompilasi aset frontend menggunakan Vite dan jalankan server pengembangan Laravel:
```bash
# Jalankan aset compiler (pada tab terminal 1)
npm run dev

# Jalankan server lokal Laravel (pada tab terminal 2)
php artisan serve
```
Aplikasi Anda kini dapat diakses melalui browser di alamat: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Autentikasi Admin Default

Gunakan kredensial berikut untuk masuk ke halaman administrator:
- **URL Login**: `http://127.0.0.1:8000/login`
- **Username**: `admin`
- **Password**: `@rt011`

*PENTING: Demi alasan keamanan, segera ubah kata sandi bawaan ini melalui panel "Ganti Password" di Dashboard Admin setelah Anda berhasil login pertama kali.*

---

## Menjalankan Pengujian Otomatis (Testing)

Proyek ini dilengkapi dengan unit & feature testing menggunakan PHPUnit untuk memvalidasi logika perhitungan saldo dan fungsionalitas transaksi. Untuk menjalankan test suite, gunakan perintah:
```bash
vendor/bin/phpunit
```
Atau menggunakan perintah Laravel:
```bash
php artisan test
```

---

## Panduan Backup Database

Backup database dilakukan melalui fasilitas hosting/phpMyAdmin atau command database server, bukan dari aplikasi. Hal ini untuk menjamin keamanan data dan efisiensi performa server.

---

## Panduan Singkat Deployment ke VPS / Shared Hosting

Untuk deploy ke production server, ikuti langkah ringkas berikut:
1. Upload folder proyek ke VPS atau letakkan di direktori utama shared hosting Anda.
2. Sesuaikan konfigurasi database di file `.env` production.
3. Jalankan `composer install --no-dev --optimize-autoloader`.
4. Jalankan perintah optimasi:
   - `php artisan config:cache`
   - `php artisan route:cache`
   - `php artisan view:cache`
5. Hubungkan folder publik storage: `php artisan storage:link`.
6. Untuk panduan deployment mendalam, silakan baca file **[DEPLOYMENT.md](file:///DEPLOYMENT.md)**.
