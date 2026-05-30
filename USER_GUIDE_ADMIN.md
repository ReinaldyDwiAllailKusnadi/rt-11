# Panduan Penggunaan Halaman Admin RT.011

Selamat datang di Panduan Penggunaan Halaman Administrator Aplikasi RT.011. Dokumen ini ditulis dalam bahasa Indonesia yang sederhana agar mudah dipahami oleh pengurus RT (Ketua, Sekretaris, atau Bendahara) dalam mengelola keuangan dan administrasi warga sehari-hari.

---

## DAFTAR ISI
1. [Cara Masuk ke Halaman Admin (Login)](#1-cara-masuk-ke-halaman-admin-login)
2. [Mengubah Kata Sandi (Password) Admin](#2-mengubah-kata-sandi-password-admin)
3. [Mengelola Data Warga (Tambah, Edit, Hapus)](#3-mengelola-data-warga-tambah-edit-hapus)
4. [Mencatat Pemasukan Iuran Warga (Kas & Keamanan)](#4-mencatat-pemasukan-iuran-warga-kas--keamanan)
5. [Mencatat Pengeluaran RT](#5-mencatat-pengeluaran-rt)
6. [Melihat Status Lunas & Tunggakan Warga](#6-melihat-status-lunas--tunggakan-warga)
7. [Mencetak Kwitansi Pembayaran](#7-mencetak-kwitansi-pembayaran)
8. [Membuat Surat Keterangan Pengantar RT](#8-membuat-surat-keterangan-pengantar-rt)
9. [Mengunduh Laporan Keuangan ke Excel](#9-mengunduh-laporan-keuangan-ke-excel)
10. [Mencadangkan (Backup) dan Memulihkan (Restore) Data](#10-mencadangkan-backup-dan-memulihkan-restore-data)

---

## 1. Cara Masuk ke Halaman Admin (Login)

Untuk dapat menginput data dan mencatat keuangan, Anda harus masuk sebagai Admin terlebih dahulu:
1. Buka browser Anda (Google Chrome, Edge, atau Mozilla Firefox).
2. Ketik alamat aplikasi Anda di kolom pencarian (misalnya: `http://localhost:8000/login` atau nama domain website RT Anda).
3. Masukkan nama pengguna (**Username**): `admin`.
4. Masukkan sandi (**Password**) bawaan: `@rt011`.
5. Klik tombol **Login**. Anda akan diarahkan masuk ke Dashboard Admin.

---

## 2. Mengubah Kata Sandi (Password) Admin

Demi menjaga keamanan data keuangan warga dari tangan yang tidak bertanggung jawab, silakan ubah password bawaan Anda:
1. Pada halaman Dashboard Admin, perhatikan kotak **Ganti Password** di sebelah kanan bawah.
2. Masukkan password lama Anda (`@rt011`).
3. Masukkan password baru yang mudah Anda ingat namun sulit ditebak orang lain.
4. Ulangi password baru tersebut pada kolom konfirmasi.
5. Klik **Simpan Password**. Mulai login berikutnya, gunakan password baru Anda tersebut.

---

## 3. Mengelola Data Warga (Tambah, Edit, Hapus)

Menu ini digunakan untuk mencatat siapa saja kepala keluarga yang tinggal di lingkungan RT.011:
1. Klik tombol **Data Warga** pada navigasi cepat dashboard.
2. **Tambah Warga**: Klik tombol **Tambah Warga Baru** di pojok kanan atas. Isi nama lengkap dan nomor rumah (misal: `I.1` atau `J.12`), lalu klik **Simpan**.
3. **Edit Warga**: Klik tombol **Edit** (ikon pensil hijau) di samping kanan nama warga. Ubah nama atau nomor rumahnya, lalu klik **Update**.
4. **Hapus Warga**: Klik tombol **Hapus** (ikon tempat sampah merah) di samping nama warga. *Perhatian: Menghapus warga juga akan menghapus semua riwayat transaksi pembayaran iuran atas nama warga tersebut.*
5. **Reset Data Warga**: Jika ingin mengembalikan data ke daftar asli 66 KK bawaan aplikasi, klik tombol **Reset ke Data Default** di pojok kanan atas.

---

## 4. Mencatat Pemasukan Iuran Warga (Kas & Keamanan)

Setiap kali warga membayar iuran bulanan (Kas RT Rp20.000 dan Keamanan Rp55.000):
1. Klik tombol **Input Transaksi** di Dashboard Admin.
2. Pilih jenis iuran yang dibayarkan warga (Klik tab **Iuran Kas RT** atau **Iuran Keamanan**).
3. **Pilih Nama Warga**: Ketik atau cari nama warga yang membayar di kolom pilihan.
4. **Pilih Tahun & Bulan**: Pilih tahun pembayaran dan beri tanda centang (checklist) pada bulan-bulan yang ingin dibayarkan (Anda bisa mencentang beberapa bulan sekaligus, misal: Januari, Februari, dan Maret).
5. **Sesuaikan Nominal**: Nominal total pembayaran akan dihitung otomatis berdasarkan jumlah bulan yang dicentang.
6. **Tanggal & Keterangan**: Tanggal pembayaran akan otomatis terisi hari ini (bisa Anda ubah jika pembayaran terjadi kemarin). Anda juga bisa menambahkan catatan opsional di kolom keterangan.
7. Klik **Simpan Transaksi**.
   - *Pencegahan Error*: Jika warga bersangkutan ternyata sudah pernah membayar di bulan/tahun yang Anda centang tersebut, aplikasi akan menampilkan pesan peringatan berwarna kuning untuk mencegah pencatatan ganda.

---

## 5. Mencatat Pengeluaran RT

Bendahara dapat mencatat setiap pengeluaran kas atau biaya keamanan yang terjadi:
1. Klik tombol **Input Transaksi** di Dashboard Admin.
2. Pilih jenis pengeluaran pada pilihan menu kategori (Klik tab **Pengeluaran Dana**).
3. **Kategori Pengeluaran**: Pilih salah satu dari pilihan yang ada:
   - *Sakit* (mengurangi saldo Kas RT)
   - *Kemalangan* (mengurangi saldo Kas RT)
   - *Konsumsi Rapat* (mengurangi saldo Kas RT)
   - *Lain-lain* (mengurangi saldo Kas RT)
   - *Bayar Satpam* (mengurangi saldo Keamanan)
4. Masukkan **Jumlah Pengeluaran (Nominal Rp)** dan **Tanggal Pengeluaran**.
5. Jika memilih pengeluaran *Bayar Satpam*, isi nama petugas satpam yang menerima gaji di kolom **Nama Satpam**.
6. Tulis alasan pengeluaran secara jelas di kolom **Keterangan** (misalnya: "Membeli karangan bunga duka cita warga rumah J.5").
7. Klik **Simpan Transaksi**.
   - *Pencegahan Minus*: Bendahara tidak bisa menginput nominal pengeluaran yang melebihi sisa uang kas/keamanan yang ada saat ini. Aplikasi akan otomatis menolak transaksi jika uang kas tidak mencukupi.

---

## 6. Melihat Status Lunas & Tunggakan Warga

Untuk mengetahui warga mana saja yang belum membayar iuran sejak Januari 2026:
1. Klik tombol **Status Bayar** di Dashboard Admin.
2. Halaman ini menampilkan tabel warga beserta sisa tunggakan bulanan mereka.
3. Warga yang menunggak **lebih dari 3 bulan** akan ditandai dengan badge bertuliskan **TUNGGAKAN X BLN** berwarna merah mencolok sebagai pengingat untuk penagihan.
4. Anda juga bisa melihat daftar riwayat total iuran terkumpul per warga melalui menu **Riwayat Warga** yang dapat diakses di tab navigasi atas.

---

## 7. Mencetak Kwitansi Pembayaran

Jika warga membutuhkan bukti cetak pembayaran iuran mereka:
1. Masuk ke halaman Dashboard Admin -> **Status Bayar** (atau cari nama warga tersebut di beranda utama publik).
2. Klik tombol **Kwitansi** (ikon printer biru) di samping kanan nama warga yang bersangkutan.
3. Halaman Kwitansi Pembayaran resmi RT.011 akan terbuka di tab baru.
4. Tekan tombol **Cetak Kwitansi / Print** di pojok kanan atas. Jendela printer browser akan terbuka, dan Anda bisa langsung mencetaknya ke printer fisik atau menyimpannya sebagai file PDF.

---

## 8. Membuat Surat Keterangan Pengantar RT

Sekretaris RT dapat membuat surat keterangan pengantar bagi warga untuk berbagai keperluan administratif (misalnya pembuatan KTP, pengantar nikah, atau izin usaha):
1. Klik menu **Surat RT** di Dashboard Admin.
2. Klik tombol **Buat Surat Pengantar Baru** di kanan atas.
3. **Pilih Warga**: Pilih nama warga pemohon.
4. **Pilih Jenis Surat**:
   - *Surat Pengantar Umum* (untuk urusan administrasi kelurahan, nikah, dll)
   - *Surat Keterangan Domisili* (keterangan tempat tinggal)
   - *Surat Keterangan Usaha* (untuk izin dagang/usaha)
5. **Isi Detail**:
   - Masukkan Nomor Surat (opsional).
   - Isi keperluan surat secara lengkap (misal: "Persyaratan pembuatan KTP Baru di Kantor Kecamatan").
   - Khusus surat keterangan usaha, isi kolom Nama Usaha dan Alamat Usaha.
6. Klik **Simpan & Preview**.
7. Anda akan melihat tampilan draf surat pengantar lengkap dengan format kop RT resmi.
   - Klik **Cetak Surat (Print)** untuk mencetak langsung ke kertas fisik.
   - Klik **Ekspor ke Word (.docx)** jika Anda ingin mengedit tulisan surat lebih lanjut menggunakan Microsoft Word di komputer Anda.

---

## 9. Mengunduh Laporan Keuangan ke Excel

Bendahara dapat mengunduh rekapitulasi data keuangan bulanan untuk keperluan rapat warga:
1. Klik menu **Laporan Excel** di Dashboard Admin.
2. Klik tombol **Unduh Laporan Keuangan (Excel)**.
3. File Excel akan terunduh. Ketika dibuka, file tersebut berisi **4 Lembar Kerja (Sheet)** yang rapi:
   - *Ringkasan*: Menampilkan total saldo awal, pemasukan, pengeluaran, dan saldo bersih kas & keamanan saat ini.
   - *Rekap_Bulanan_Warga*: Tabel centang bulanan (Jan-Des) status bayar iuran per warga.
   - *Status_Warga*: Daftar lengkap warga beserta jumlah nominal iuran terkumpul dan status tunggakannya.
   - *Daftar_Transaksi*: Log riwayat seluruh transaksi masuk dan keluar secara kronologis.

---

## 10. Mencadangkan (Backup) dan Memulihkan (Restore) Data

Untuk menjaga keamanan database jika komputer pengurus mengalami kerusakan:
1. Klik menu **Backup Data** di Dashboard Admin.
2. **Cara Backup**: Klik tombol **Export Database JSON**. File backup bernama `rt011_backup_tanggal_waktu.json` akan terunduh. Simpan file ini di flashdisk atau Google Drive Bendahara secara berkala.
3. **Cara Restore (Pemulihan)**:
   - Jika ingin memulihkan data setelah instalasi ulang, masuk ke menu **Backup Data**.
   - Pada bagian **Restore Data**, klik tombol pilih file dan cari file `.json` cadangan yang disimpan sebelumnya.
   - Klik **Restore Database**. Sistem akan memeriksa data tersebut terlebih dahulu. Jika cocok, data lama akan ditimpa dengan data cadangan tersebut.
