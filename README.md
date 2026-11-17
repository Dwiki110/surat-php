# Sistem Administrasi Surat &mdash; Kecamatan Cibitung

Implementasi web berdasarkan Tugas Akhir **"Penerapan Algoritma Levenshtein
Distance untuk Website Administrasi Surat"** (Muhammad Dwiki Wicaksono,
Universitas Esa Unggul). Dibangun dengan PHP native + MySQL sesuai batasan
lingkup penelitian (Bab 1.5) dan environment XAMPP.

## Struktur folder

```
surat-app/
├── assets/css/style.css        Tampilan (navy/emas, gaya institusional)
├── config/database.php         Konfigurasi koneksi PDO ke MySQL
├── database/
│   ├── schema.sql               DDL tabel (pengguna, surat_masuk, hasil_surat,
│   │                             surat_keluar, notifikasi) sesuai ERD Bab 3
│   └── seed.php                 Skrip pengisi akun & data contoh (jalankan sekali)
├── includes/
│   ├── auth.php                 require_login() / require_role()
│   ├── functions.php            Algoritma Levenshtein, similarity ratio, traceback
│   ├── header.php / sidebar.php / footer.php   Layout bersama
├── dashboard.php                Router tipis: arahkan sesuai peran yang login
├── dashboard/
│   ├── resepsionis.php           Dashboard Resepsionis (surat yang ia ajukan)
│   └── sekretaris.php             Dashboard Sekretaris (perlu ditindak + draf keluar)
├── surat_masuk/
│   ├── index.php                 Daftar + filter status
│   ├── tambah.php                 Upload surat baru (Resepsionis)
│   └── detail.php                 Detail + aksi Proses/Selesai/Upload hasil (Sekretaris)
├── surat_keluar/
│   ├── index.php                 Daftar + draf surat keluar dengan upload berkas (Sekretaris)
│   └── detail.php                 Detail surat keluar + lihat berkas
├── pencarian/index.php            Pencarian cerdas &mdash; hasil + tombol lihat berkas
├── notifikasi/index.php           Riwayat notifikasi per peran
├── uploads/                       Folder penyimpanan berkas upload
├── login.php / logout.php / index.php
```

## Cara menjalankan (XAMPP)

1. Salin folder ini ke `C:\xampp\htdocs\surat-app` (Windows) atau
   `/Applications/XAMPP/htdocs/surat-app` (Mac).
2. Jalankan Apache & MySQL dari XAMPP Control Panel.
3. Buka **phpMyAdmin**, import `database/schema.sql` untuk membuat
   database `db_surat_kecamatan` beserta tabel-tabelnya.
   (Atau lewat CLI: `mysql -u root < database/schema.sql`)

   > Sudah pernah import `schema.sql` versi lama (sebelum surat keluar
   > punya fitur upload berkas)? Jalankan juga `database/upgrade_v2.sql`
   > lewat tab **SQL** di phpMyAdmin.
   >
   > Mau query tetap cepat walau datanya sudah ribuan baris? Jalankan
   > juga `database/upgrade_v3_index.sql` untuk menambahkan index pada
   > kolom `status`, `created_at`, dan `penerima_role` (aman dijalankan
   > kapan saja, tidak mengubah data yang ada).
4. Sesuaikan kredensial di `config/database.php` jika MySQL kamu
   memakai user/password selain default XAMPP (`root` tanpa password).
5. Buka **http://localhost/surat-app/database/seed.php** sekali di
   browser untuk mengisi akun & data contoh.
6. Buka **http://localhost/surat-app/login.php** dan masuk dengan:
   - Sekretaris &rarr; `sekretaris01` / `123456`
   - Resepsionis &rarr; `resepsionis01` / `123456`

   > Sudah pernah menjalankan `seed.php` sebelumnya dengan password lama?
   > Buka **http://localhost/surat-app/database/reset_password.php**
   > sekali untuk mengubah password kedua akun tersebut menjadi `123456`
   > tanpa menghapus data surat yang sudah ada.

## Fitur inti yang diimplementasikan

- **Login berbasis peran** (Resepsionis / Sekretaris) sesuai Use Case Diagram.
- **Dashboard terpisah per peran** (`dashboard/resepsionis.php` &amp;
  `dashboard/sekretaris.php`) &mdash; `dashboard.php` di root hanya router
  yang mengarahkan sesuai peran yang login. Resepsionis melihat surat yang
  ia ajukan sendiri; Sekretaris melihat surat yang perlu ditindaklanjuti
  (diurutkan dari yang paling lama menunggu) beserta draf surat keluar.
- **Surat masuk**: upload &rarr; status `Baru` &rarr; `Diproses` &rarr; `Selesai`,
  dengan notifikasi otomatis ke pihak terkait di setiap perubahan status.
  Berkas surat (dan berkas hasil balasan) bisa dilihat/diunduh langsung
  lewat tombol **Lihat File**, oleh Resepsionis maupun Sekretaris.
- **Surat keluar**: draf surat keluar juga bisa dilampiri berkas, dan
  punya halaman detail sendiri dengan tombol lihat berkas.
- **Algoritma Levenshtein Distance** tetap diimplementasikan langsung di
  PHP (`includes/functions.php` &rarr; `levenshtein_matrix()`) untuk
  menghitung tingkat kecocokan &mdash; ditampilkan sebagai meter/skor
  yang bersih tanpa istilah teknis di layar pengguna.
- **Similarity Ratio** dihitung dengan rumus S(i,j) = (1 &minus; D/max(|i|,|j|)) &times; 100%,
  dengan ambang batas (threshold) 60% sesuai hasil wawancara di Bab 3.2.2 &mdash;
  perhitungannya tetap sesuai skripsi, hanya tidak lagi ditampilkan mentah
  ke pengguna akhir.
- **Pencarian cerdas** lintas surat masuk & surat keluar: setiap hasil
  langsung punya tombol **Lihat** dan **Unduh** menuju detail + berkasnya,
  bukan sekadar daftar judul.
- **Tampilan responsif** &mdash; sidebar berubah jadi menu geser (drawer)
  di layar HP, tabel bisa di-scroll horizontal, dan kartu/statistik
  menyesuaikan lebar layar.
- **Paginasi & batas pindai** &mdash; daftar surat masuk, surat keluar,
  dan notifikasi memakai `LIMIT`/`OFFSET` (15&ndash;20 baris per halaman)
  supaya query tidak menarik seluruh tabel sekaligus. Pencarian cerdas
  membatasi pemindaian pada beberapa ribu arsip terbaru per kategori
  (`SEARCH_SCAN_LIMIT` di `includes/functions.php`) dan hanya menampilkan
  30 hasil teratas, supaya tetap ringan walau data sudah ribuan baris.

## Yang masih perlu dikembangkan (opsional, untuk Bab 4/5)

- Modul **disposisi** dan **laporan/statistik** (terlihat pada mockup
  dashboard sekretaris di Bab 4, belum diimplementasikan di prototipe ini).
- Validasi & sanitasi upload file lebih ketat (tipe MIME, ukuran maksimum).
- Pengujian akurasi algoritma otomatis (unit test) untuk Tabel 3.4 skripsi.
