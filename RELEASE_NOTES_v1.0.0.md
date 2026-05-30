# Catatan Rilis (Release Notes) - v1.0.0

Kami dengan bangga merilis **Aplikasi Manajemen Keuangan & Administrasi RT.011 Versi 1.0.0**. Rilis ini menandai selesainya fase migrasi sistem lama berbasis HTML tunggal dengan penyimpanan lokal browser (localStorage) ke arsitektur modern berbasis Laravel dan database relasional.

---

## Ikhtisar Migrasi

Sistem lama yang menggunakan satu file HTML tunggal dengan localStorage memiliki keterbatasan keamanan, data mudah terhapus secara tidak sengaja dari browser warga, serta tidak mendukung akses multi-user. 

Pada versi **v1.0.0** ini, seluruh data dan logika bisnis telah dipindahkan ke **Laravel 13** dan **MySQL DB** dengan arsitektur MVC (Model-View-Controller). Seluruh data warga default (66 KK) dan riwayat iuran awal telah dipertahankan dan disalin dengan aman ke database baru.

---

## Fitur yang Selesai di Versi 1.0.0

1. **Dashboard Utama & Keuangan**:
   - Beranda publik warga menampilkan sisa Saldo Kas RT, Saldo Keamanan, dan Saldo Bersih secara real-time.
   - Papan pencarian interaktif bagi warga untuk mengecek status lunas/tunggakan pembayaran iuran mereka.
2. **Administrasi Data Warga (CRUD)**:
   - Panel lengkap bagi Pengurus RT untuk menambah, melihat, mengedit, dan menghapus data warga.
   - Fitur "Reset Data Warga" untuk mengembalikan daftar warga ke setelan awal 66 KK.
3. **Pencatatan Transaksi Finansial**:
   - Form input iuran bulanan warga (pembayaran Kas RT Rp20.000/bulan dan Keamanan Rp55.000/bulan) per tahun berjalan dengan layout checklist bulan yang intuitif.
   - Form pencatatan pengeluaran dana Kas RT (untuk Kemalangan, Sakit, Konsumsi Rapat, Lain-lain) dan dana Keamanan (untuk Gaji Satpam).
4. **Ekspor & Cetak Surat RT**:
   - Pembuatan Surat Keterangan Pengantar RT (Umum, Domisili, Usaha) secara instan.
   - Ekspor surat pengantar ke file Word (.docx) dan kwitansi pembayaran ke layout ramah cetak.
5. **Laporan Rekapitulasi Excel**:
   - Fitur ekspor Excel yang menghasilkan file berformat lengkap dengan 4 sheet terpisah (`Ringkasan`, `Rekap_Bulanan_Warga`, `Status_Warga`, `Daftar_Transaksi`).
6. **Mekanisme Backup & Restore JSON**:
   - Kemudahan ekspor seluruh isi database ke file JSON dan impor data kembali jika terjadi migrasi server.

---

## Penguatan Keamanan & Audit (Hardening)

Dalam rilis v1.0.0 ini, kami telah melakukan audit mendalam pada seluruh kode program untuk memperkokoh keamanan aplikasi:
- **Proteksi Admin**: Seluruh halaman dashboard admin, manajemen warga, transaksi, dan surat telah dilindungi di balik middleware autentikasi Laravel.
- **Proteksi CSRF**: Semua form inputan metode POST, PUT, dan DELETE telah dilengkapi dengan token CSRF untuk menangkal serangan Cross-Site Request Forgery.
- **Pengamanan Saldo Negatif**: Penambahan filter validasi ketat yang memblokir perubahan atau penghapusan transaksi pemasukan jika tindakan tersebut berpotensi menyebabkan saldo aktif Kas RT atau Keamanan menjadi minus.
- **Deep Validation Impor Backup**: Restore JSON kini memvalidasi struktur data dan format setiap baris data sebelum melakukan pengosongan database, menghindari kegagalan impor setengah jalan.
- **Dekopling Logika di Blade**: Seluruh perhitungan aritmatika dan penjumlahan iuran telah dipindahkan dari view Blade ke `FinanceService` demi transparansi dan kemudahan pengujian.

---

## Hasil Pengujian Otomatis (Test Suite)

Kami telah menjalankan serangkaian pengujian terotomatisasi (unit & feature testing) untuk menjamin stabilitas aplikasi sebelum rilis ini dipublikasikan:
- **Total Pengujian**: 13 Skenario Test
- **Total Assersi**: 42 Assertions
- **Status Akhir**: **PASSED (100% Lolos)**

*Skenario yang diuji meliputi: Autentikasi Admin, Kalkulasi Saldo Awal & Transaksi, Batas Pengeluaran Kas/Keamanan, Validasi Duplikasi Iuran, serta Impor/Ekspor data JSON.*

---

## ⚠️ PERINGATAN PENTING SEBELUM DEPLOY

1. **Ganti Password Admin Bawaan**:
   Username default admin adalah `admin` dan kata sandi bawaan adalah `@rt011`. **Sandi ini wajib langsung diganti** sesaat setelah aplikasi dipasang di server production melalui menu "Ganti Password" yang terletak di Dashboard Administrator.
2. **Mode Debug**:
   Pastikan variabel `APP_DEBUG` pada file `.env` di server Anda diatur ke `false` agar informasi sistem internal tidak terekspos ke publik jika terjadi kendala jaringan.
3. **Backup Data**:
   Selalu lakukan backup database secara berkala melalui menu Backup Data di admin panel.
