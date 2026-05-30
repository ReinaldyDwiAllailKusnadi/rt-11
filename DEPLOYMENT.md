# Panduan Penyebaran (Deployment) Aplikasi RT.011

Dokumen ini berisi panduan lengkap langkah-demi-langkah untuk melakukan deployment aplikasi Manajemen RT.011 ke server production, baik menggunakan Virtual Private Server (VPS) maupun Shared Hosting (cPanel).

---

## Langkah 1: Persiapan Server & Unggah File

1. **Persiapan Folder**:
   - **VPS**: Arahkan root direktori Nginx/Apache Anda ke folder `/public` di dalam proyek (misalnya: `/var/www/rt11-app/public`).
   - **Shared Hosting**:
     - Unggah seluruh folder proyek ke server (bisa di luar folder `public_html` atau setara).
     - Pindahkan isi folder `/public` dari proyek ke dalam `public_html` hosting Anda, kemudian sesuaikan path di file `public_html/index.php` pada baris:
       ```php
       require __DIR__.'/../bootstrap/app.php'
       ```
       (Sesuaikan tanda `../` agar mengarah ke letak direktori bootstrap proyek yang sebenarnya).
2. **Unggah File**: Unggah source code proyek menggunakan Git, FTP, atau File Manager cPanel. Pastikan folder `vendor` dan `node_modules` tidak ikut diunggah karena akan di-generate langsung di server.

---

## Langkah 2: Instalasi Dependensi Server

Masuk ke terminal server Anda melalui SSH (atau gunakan terminal cPanel jika didukung) di root folder proyek, lalu jalankan instalasi dependensi PHP production:
```bash
composer install --no-dev --optimize-autoloader
```
*Perintah ini akan menginstal semua package yang diperlukan tanpa menyertakan library pengujian (PHPUnit) serta mengoptimalkan classmap agar loading aplikasi jauh lebih cepat.*

---

## Langkah 3: Konfigurasi Environment File (.env)

Buat file `.env` baru di server atau salin dari lokal, kemudian ubah nilai berikut untuk mode production:
```env
APP_NAME="RT.011 Karanggintung"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nama-domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_nama_rt011
DB_USERNAME=user_db_rt011
DB_PASSWORD=password_database_anda

SESSION_DRIVER=file
CACHE_STORE=file
```
*PENTING: Jangan biarkan `APP_DEBUG=true` di server production karena dapat membocorkan informasi sensitif kredensial database Anda saat terjadi error.*

---

## Langkah 4: Generate Application Key

Jika file `.env` baru saja dibuat dari awal, generate key pengaman dengan perintah:
```bash
php artisan key:generate
```

---

## Langkah 5: Migrasi Database & Seeding Awal

Jalankan migrasi database di server dengan parameter `--force` agar Laravel bersedia mengeksekusi perubahan skema pada lingkungan production:
```bash
# Membuat struktur tabel baru di database production
php artisan migrate --force

# Mengisi data warga default 66 KK (jika database masih kosong)
php artisan db:seed --force
```

---

## Langkah 6: Optimasi Cache Aplikasi

Laravel menyediakan mekanisme caching bawaan untuk mempercepat pemrosesan aplikasi. Jalankan perintah optimasi berikut:
```bash
# Caching file konfigurasi .env
php artisan config:cache

# Caching routing web
php artisan route:cache

# Caching kompilasi template Blade
php artisan view:cache
```
*Catatan: Jika Anda melakukan perubahan pada file `.env` di masa mendatang, Anda harus menjalankan kembali `php artisan config:clear` dan `php artisan config:cache` agar perubahan terbaca oleh server.*

---

## Langkah 7: Pengaturan Izin Folder (Permissions)

Web server membutuhkan akses tulis pada beberapa folder untuk menyimpan log, session, cache, dan file yang diunggah. Sesuaikan izin akses folder berikut:

### Pada Lingkungan VPS (Linux/Ubuntu):
Arahkan kepemilikan folder ke user web server (biasanya `www-data` atau `nginx`):
```bash
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Pada Shared Hosting (cPanel):
Pastikan folder `storage` dan `bootstrap/cache` memiliki permission **775** atau minimal **755** (dapat diatur langsung melalui File Manager -> Change Permissions).

---

## Langkah 8: Hubungkan Storage Link (Symlink)

Agar foto/dokumen iuran yang diunggah admin dapat diakses publik, buat link folder storage ke folder public:
```bash
php artisan storage:link
```
*Catatan untuk Shared Hosting:* Jika hosting Anda tidak menyediakan akses terminal SSH untuk menjalankan perintah di atas, Anda bisa membuat route sementara di `routes/web.php` untuk memicunya melalui browser:
```php
Route::get('/symlink-trigger', function () {
    Artisan::call('storage:link');
    return 'Symlink berhasil dibuat!';
});
```
Hapus route pemicu tersebut segera setelah link berhasil terbuat.

---

## Panduan Rollback Sederhana

Jika setelah deployment v1.0.0 dirilis terjadi kendala kritis pada sistem, ikuti langkah pengembalian (rollback) berikut:

1. **Kembalikan Source Code**:
   Jika menggunakan Git di VPS, kembali ke commit sebelumnya:
   ```bash
   git checkout <id-commit-sebelumnya>
   ```
   Jika menggunakan FTP/File Manager, unggah kembali backup file versi sebelumnya yang telah Anda kompres sebelum rilis baru dilakukan.
2. **Rollback Migrasi Database**:
   Jika tabel baru menyebabkan error, jalankan perintah rollback migrasi:
   ```bash
   php artisan migrate:rollback --force
   ```
3. **Bersihkan Cache**:
   Pastikan cache dibersihkan agar web server merender source code lama dengan benar:
   ```bash
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```
4. **Verifikasi Jalur Aplikasi**:
   Akses kembali halaman publik dan pastikan saldo kas serta data warga tampil normal kembali.
