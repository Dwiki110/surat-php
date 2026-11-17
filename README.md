<!-- HEADER BANNER GRADASI NAVY & EMAS -->
<div align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:0f172a,50:1e3a8a,100:d97706&height=220&section=header&text=Sistem%20Administrasi%20Surat&fontSize=38&fontColor=ffffff&fontAlignY=38&desc=Kecamatan%20Cibitung%20%E2%80%A2%20Implementasi%20Algoritma%20Levenshtein%20Distance&descSize=16&descColor=fde68a&descAlignY=62" width="100%" alt="Banner Sistem Administrasi Surat"/>

  <p>
    <img src="https://img.shields.io/badge/PHP-Native-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
    <img src="https://img.shields.io/badge/MySQL-PDO-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
    <img src="https://img.shields.io/badge/XAMPP-Server-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP"/>
    <img src="https://img.shields.io/badge/Algoritma-Levenshtein_Distance-D97706?style=for-the-badge" alt="Levenshtein"/>
    <img src="https://img.shields.io/badge/Status-Prototipe_Skripsi-16A34A?style=for-the-badge" alt="Status"/>
  </p>

  **Implementasi Web Tugas Akhir:** *"Penerapan Algoritma Levenshtein Distance untuk Website Administrasi Surat"*  
  Disusun oleh **Muhammad Dwiki Wicaksono** — Program Studi Teknik Informatika, Universitas Esa Unggul
</div>

---

## 🌐 1. Tentang Website Ini

**Sistem Administrasi Surat Kecamatan Cibitung** adalah aplikasi berbasis web yang dirancang khusus untuk mendigitalisasi, menertibkan, dan mempercepat seluruh alur pengelolaan surat-menyurat instansi pemerintahan di tingkat kecamatan.

### Permasalahan yang Diselesaikan
Sebelumnya, pengelolaan surat masuk dan surat keluar di instansi sering dilakukan secara manual menggunakan buku agenda fisik, sehingga menimbulkan beberapa kendala utama:
* **Sulit Melacak Status Surat:** Petugas meja depan (Resepsionis) sering kesulitan mengetahui apakah surat yang sudah diserahkan ke bagian tata usaha sudah diproses oleh Sekretaris atau belum.
* **Pencarian Arsip Lama yang Lambat:** Saat warga atau pimpinan menanyakan kembali arsip surat lama, petugas harus membuka buku catatan satu per satu.
* **Pencarian Gagal karena Salah Ketik (*Typo*):** Pada pencarian database konvensional (`LIKE %kata%`), kesalahan satu huruf saja (misalnya mengetik *"Undngan"* padahal di data tertulis *"Undangan"*) menyebabkan dokumen tidak ditemukan.

### Solusi yang Dihadirkan Website Ini
Website ini dibangun dengan **PHP Native + MySQL** (sesuai batasan lingkup penelitian Bab 1.5 pada environment XAMPP) untuk menjawab seluruh masalah di atas melalui:
1. **Digitalisasi Berkas & Metadata:** Setiap surat masuk maupun surat keluar dicatat secara digital lengkap dengan lampiran file PDF-nya sehingga arsip aman dan mudah diakses kapan saja.
2. **Alur Kerja Terintegrasi & Notifikasi Otomatis:** Menghubungkan langsung meja pelayanan (**Resepsionis**) dengan bagian tata usaha (**Sekretaris**) melalui perubahan status berjenjang (`Baru` ➔ `Diproses` ➔ `Selesai`) dan pemberitahuan otomatis.
3. **Mesin Pencari Cerdas (*Smart Search*):** Menerapkan algoritma **Levenshtein Distance** yang mampu menghitung jarak kemiripan karakter, sehingga sistem tetap dapat menemukan arsip surat yang relevan meskipun pengguna salah ketik (*typo*) saat mencari.

---

## 🎨 2. Konsep Desain & Tampilan Antarmuka

Antarmuka website dirancang dengan gaya **institusional pemerintahan** yang formal, berwibawa, dan mudah dipahami oleh staf administrasi:

![Primary Navy](https://img.shields.io/badge/Primary_Navy-%230F172A-0F172A?style=flat-square)
![Royal Blue](https://img.shields.io/badge/Accent_Blue-%231E3A8A-1E3A8A?style=flat-square)
![Institutional Gold](https://img.shields.io/badge/Institutional_Gold-%23D97706-D97706?style=flat-square)
![Soft Surface](https://img.shields.io/badge/Background_Light-%23F8FAFC-F8FAFC?style=flat-square)

* **Dominasi Warna Navy & Emas:** Memberikan kesan resmi, rapi, dan nyaman dilihat dalam waktu lama di layar monitor kantor.
* **Tata Letak Responsif (*Multi-Device*):** Dapat dibuka dengan nyaman baik melalui komputer desktop kantor maupun smartphone/tablet. Pada layar ponsel, menu navigasi samping otomatis berubah menjadi *drawer menu* (menu geser) dan tabel data mendukung *horizontal scroll* agar tidak berantakan.

---

## 👥 3. Penjelasan Lengkap Halaman & Fitur Pengguna

Website ini memiliki sistem **Login Berbasis Peran (*Role-Based Access Control*)**. Saat pengguna masuk melalui `login.php`, file router `dashboard.php` akan otomatis mendeteksi peran akun dan mengarahkan pengguna ke ruang kerja masing-masing.

### A. Ruang Kerja Resepsionis (`resepsionis/`)
Resepsionis berperan sebagai garda depan penerima surat dari masyarakat/instansi luar sekaligus pihak yang menyerahkan hasil balasan surat kepada pemohon.

| Menu / Halaman | File | Fungsi & Penjelasan Detail di Website |
| :--- | :--- | :--- |
| **Dashboard Resepsionis** | `dashboard.php` | Menampilkan ringkasan jumlah surat dan tabel pemantauan khusus surat-surat masuk yang pernah diajukan oleh resepsionis tersebut beserta status terkininya. |
| **Input Surat Masuk** | `tambah_surat.php` | Formulir digital untuk mencatat surat fisik yang baru diterima (nomor surat, asal pengirim, tanggal, perihal) sekaligus mengunggah hasil pindaian (*scan*) berkas PDF ke server. |
| **Daftar Surat Masuk** | `surat_masuk.php` | Menampilkan seluruh riwayat surat masuk lengkap dengan fitur filter berdasarkan status (`Baru`, `Diproses`, `Selesai`) dan paginasi halaman. |
| **Detail & Unduh Hasil** | `surat_masuk_detail.php` | Menampilkan informasi lengkap surat. Jika Sekretaris sudah menyelesaikan surat tersebut, tombol **Lihat File** untuk melihat/mengunduh berkas balasan resmi akan muncul di halaman ini. |
| **Arsip Surat Keluar** | `surat_keluar.php` & `surat_keluar_detail.php` | Memungkinkan resepsionis melihat daftar serta detail berkas surat keluar yang diterbitkan oleh kecamatan sebagai referensi pelayanan informasi. |
| **Pencarian Cerdas** | `pencarian.php` | Fitur pencarian cepat lintas arsip surat masuk & keluar menggunakan algoritma Levenshtein Distance lengkap dengan tombol langsung **Lihat** dan **Unduh**. |
| **Notifikasi** | `notifikasi.php` | Menampilkan riwayat pemberitahuan otomatis setiap kali surat yang diajukan berubah statusnya oleh Sekretaris. |

---

### B. Ruang Kerja Sekretaris (`sekretaris/`)
Sekretaris berperan sebagai pengelola administrasi tata usaha yang menindaklanjuti surat masuk, mengunggah dokumen balasan, serta menerbitkan surat keluar.

| Menu / Halaman | File | Fungsi & Penjelasan Detail di Website |
| :--- | :--- | :--- |
| **Dashboard Sekretaris** | `dashboard.php` | Menampilkan antrean surat masuk yang perlu segera ditindaklanjuti (**diurutkan otomatis dari yang paling lama menunggu** agar tidak ada surat terlewat) serta ringkasan draf surat keluar. |
| **Manajemen Surat Masuk** | `surat_masuk.php` | Menampilkan seluruh daftar surat masuk dari resepsionis dengan filter status dan navigasi paginasi. |
| **Tindak Lanjut Surat** | `surat_masuk_detail.php` | Halaman kerja utama sekretaris untuk memproses surat: mengubah status dari `Baru` ➔ `Diproses`, lalu mengubah ke `Selesai` sekaligus mengunggah berkas PDF hasil balasan/surat jawaban. |
| **Kelola Surat Keluar** | `surat_keluar.php` & `surat_keluar_detail.php` | Halaman untuk mencatat draf maupun arsip surat keluar baru, melampirkan file PDF surat keluar, dan melihat rincian berkasnya. |
| **Pencarian Cerdas** | `pencarian.php` | Mesin pencari toleran *typo* lintas seluruh arsip surat masuk & keluar dengan indikator skor kecocokan. |
| **Notifikasi** | `notifikasi.php` | Pusat pemberitahuan otomatis setiap kali resepsionis mendaftarkan surat masuk baru ke dalam sistem. |

---

## 🔄 4. Alur Kerja Sistem dari Awal Sampai Selesai

Berikut adalah gambaran bagaimana kedua peran saling berinteraksi di dalam website:

```text
1. [Surat Masuk Diterima di Meja Pelayanan]
        │
        ▼
2. 👤 RESEPSIONIS membuka menu "Tambah Surat"
   • Mengisi data surat & mengunggah file PDF surat masuk.
   • Status surat otomatis tercatat sebagai: [ BARU ]
   • Sistem mengirim 🔔 Notifikasi otomatis ke akun Sekretaris.
        │
        ▼
3. 👔 SEKRETARIS membuka "Dashboard / Surat Masuk"
   • Melihat surat baru di urutan prioritas dan memeriksa file PDF-nya.
   • Mengubah status surat menjadi: [ DIPROSES ]
   • Sistem mengirim 🔔 Notifikasi ke Resepsionis bahwa surat sedang ditangani.
        │
        ▼
4. 👔 SEKRETARIS menyelesaikan surat & mengunggah berkas jawaban
   • Mengunggah file PDF hasil balasan pada halaman detail surat.
   • Status surat berubah menjadi: [ SELESAI ]
   • Sistem mengirim 🔔 Notifikasi ke Resepsionis bahwa surat telah selesai.
        │
        ▼
5. 👤 RESEPSIONIS membuka "Detail Surat Masuk"
   • Mengklik tombol "Lihat File" pada bagian berkas hasil untuk mengunduh/mencetak
     surat balasan dan menyerahkannya kepada pemohon.
```

---

## 🧮 5. Cara Kerja Pencarian Cerdas (Algoritma Levenshtein Distance)

Fitur pencarian pada website ini tidak hanya mengandalkan pencocokan kata persis (*exact match*), melainkan mengimplementasikan algoritma **Levenshtein Distance** secara *native* di PHP (`includes/functions.php` ➔ `levenshtein_matrix()`).

1. **Perhitungan Jarak Edit ($D$):**  
   Saat pengguna mengetik kata kunci di kolom pencarian, algoritma membentuk matriks perbandingan karakter antara kata kunci ($i$) dengan data perihal/nomor surat di database ($j$), lalu menghitung jumlah minimum perubahan karakter (penyisipan, penghapusan, atau penggantian huruf).
2. **Perhitungan Persentase Kemiripan (*Similarity Ratio*):**  
   Nilai jarak $D$ kemudian dikonversi menjadi skor kemiripan dalam persen menggunakan rumus:
   $$S(i, j) = \left(1 - \frac{D}{\max(\vert{}i\vert{}, \vert{}j\vert{})}\right) \times 100\%$$
3. **Penyaringan Ambang Batas (*Threshold* 60%):**  
   Sesuai hasil wawancara pada **Bab 3.2.2**, website hanya menampilkan arsip surat yang memiliki tingkat kemiripan **$\ge 60\%$** sehingga hasil tetap relevan namun tetap toleran terhadap salah ketik.
4. **Tampilan Hasil Praktis:**  
   Di layar pengguna, skor perhitungan ditampilkan sebagai indikator kecocokan visual yang bersih tanpa istilah rumus yang membingungkan, serta langsung dilengkapi tombol **Lihat** (ke halaman detail) dan **Unduh** (mengunduh file PDF terkait).

---

## 🗄️ 6. Arsitektur Database & Performa Sistem

Website menggunakan database MySQL bernama **`db_surat_kecamatan`** dengan 5 tabel utama yang saling berelasi:
* **`pengguna`**: Menyimpan data akun login dan hak akses peran (`resepsionis` / `sekretaris`).
* **`surat_masuk`**: Menyimpan metadata surat masuk, path file PDF asli, dan status alur surat.
* **`hasil_surat`**: Menyimpan file PDF hasil tindak lanjut/balasan dari sekretaris yang terhubung ke surat masuk.
* **`surat_keluar`**: Menyimpan data arsip/draf surat keluar kecamatan beserta file lampirannya.
* **`notifikasi`**: Menyimpan riwayat notifikasi otomatis antar peran.

**Optimasi & Keamanan:**
* **PDO Prepared Statements:** Mencegah celah keamanan *SQL Injection* pada seluruh proses input dan pencarian data.
* **Paginasi & Batas Pindai:** Daftar tabel dibatasi 15–20 baris per halaman (`LIMIT`/`OFFSET`), sedangkan pencarian cerdas membatasi pemindaian pada arsip terbaru (`SEARCH_SCAN_LIMIT`) dan menampilkan 30 hasil terbaik agar website tetap ringan walau arsip sudah mencapai ribuan data.

---

## 📂 7. Struktur Direktori Proyek

```text
surat-php/
├── 📁 assets/css/style.css           # Desain antarmuka institusional (Navy & Emas)
├── 📁 config/database.php            # Konfigurasi koneksi PDO ke MySQL
├── 📁 database/
│   ├── schema.sql                    # Struktur tabel utama (DDL) sesuai ERD Bab 3
│   ├── seed.php                      # Skrip pengisi akun demo & data simulasi awal
│   ├── reset_password.php            # Utilitas reset password akun demo ke 123456
│   ├── upgrade_v2.sql                # Migrasi skema lampiran berkas surat keluar
│   └── upgrade_v3_index.sql          # Penambahan index untuk optimasi query
├── 📁 includes/
│   ├── auth.php                      # Middleware require_login() & require_role()
│   ├── functions.php                 # Algoritma Levenshtein matrix, similarity & helper
│   └── header.php / sidebar.php / footer.php
├── 📁 resepsionis/                   # Halaman & fitur khusus peran Resepsionis
├── 📁 sekretaris/                    # Halaman & fitur khusus peran Sekretaris
├── 📁 uploads/                       # Penyimpanan berkas PDF (surat_masuk, surat_keluar, hasil_surat)
└── 📄 index.php / login.php / logout.php / dashboard.php
```

---

## 🚀 8. Cara Menjalankan Website di Komputer Lokal (XAMPP)

### 1. Persiapan Server & Folder
1. Salin folder proyek ini ke direktori `htdocs` XAMPP:
   * **Windows:** `C:\xampp\htdocs\surat-php`
   * **macOS:** `/Applications/XAMPP/htdocs/surat-php`
2. Buka **XAMPP Control Panel**, lalu jalankan **Apache** dan **MySQL**.

### 2. Import Database
1. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), buat/import file `database/schema.sql` untuk membentuk database `db_surat_kecamatan` beserta seluruh tabelnya.
2. *(Opsional)* Jalankan `database/upgrade_v2.sql` (jika memperbarui database versi lama) dan `database/upgrade_v3_index.sql` (untuk mengaktifkan index kecepatan query) lewat tab **SQL** di phpMyAdmin.
3. Sesuaikan pengaturan koneksi di `config/database.php` jika MySQL kamu memakai username/password selain default XAMPP (`root` tanpa password).

### 3. Buat Akun Demo & Login
1. Buka **`http://localhost/surat-php/database/seed.php`** satu kali di browser untuk mengisi akun dan data contoh. *(Jika sebelumnya pernah menjalankan versi lama, buka `database/reset_password.php` untuk mereset password ke `123456`).*
2. Buka **`http://localhost/surat-php/login.php`** dan masuk dengan salah satu akun berikut:

| Peran | Username | Password |
| :--- | :--- | :--- |
| 👔 **Sekretaris** | `sekretaris01` | `123456` |
| 🗂️ **Resepsionis** | `resepsionis01` | `123456` |

<!-- FOOTER BANNER -->
<img src="https://capsule-render.vercel.app/api?type=waving&color=0:d97706,50:1e3a8a,100:0f172a&height=110&section=footer" width="100%" alt="Footer Banner"/>
