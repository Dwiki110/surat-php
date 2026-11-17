<div align="center">

# 🏛️ Sistem Administrasi Surat — Kecamatan Cibitung

**Implementasi Web Tugas Akhir:** *"Penerapan Algoritma Levenshtein Distance untuk Website Administrasi Surat"*  
**Muhammad Dwiki Wicaksono** • Universitas Esa Unggul

![PHP](https://img.shields.io/badge/PHP-Native-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-PDO-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![XAMPP](https://img.shields.io/badge/Environment-XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)
![Status](https://img.shields.io/badge/Status-Prototipe%20Skripsi-2ea44f?style=for-the-badge)

</div>

---

## 📖 Tentang Proyek

Aplikasi web berbasis **PHP Native + MySQL** yang dirancang untuk mendigitalisasi alur administrasi persuratan di lingkungan **Kecamatan Cibitung**, sesuai batasan lingkup penelitian (Bab 1.5) dan kebutuhan environment XAMPP[cite: 1].

Sistem ini memanfaatkan algoritma **Levenshtein Distance** untuk pencarian cerdas lintas arsip surat masuk maupun surat keluar dengan toleransi kesalahan pengetikan (*typo*), serta dilengkapi manajemen hak akses berbasis peran (*Role-Based Access Control*) untuk **Resepsionis** dan **Sekretaris**[cite: 1].

---

## ✨ Fitur Utama

- **🔐 Autentikasi & Hak Akses Berbasis Peran**  
  Pemisahan modul dan antarmuka khusus antara **Resepsionis** dan **Sekretaris** sesuai *Use Case Diagram*[cite: 1].
- **📊 Dashboard Terpisah per Peran**  
  - **Resepsionis:** Memantau status surat masuk yang telah diajukan beserta riwayat tindak lanjutnya[cite: 1].
  - **Sekretaris:** Menampilkan antrean surat masuk yang perlu ditindaklanjuti (diurutkan dari yang paling lama menunggu) serta ringkasan draf surat keluar[cite: 1].
- **📥 Manajemen Surat Masuk (`Baru` → `Diproses` → `Selesai`)**  
  Pengajuan surat baru, pembaruan status disposisi/proses, unggah berkas hasil balasan, notifikasi otomatis di setiap perubahan status, serta fitur pratinjau/unduh berkas langsung[cite: 1].
- **📤 Manajemen Surat Keluar**  
  Pembuatan draf dan arsip surat keluar lengkap dengan lampiran berkas PDF serta halaman detail terdedikasi[cite: 1].
- **🔍 Pencarian Cerdas (*Levenshtein Distance*)**  
  Implementasi matriks Levenshtein (`levenshtein_matrix()`) secara native di PHP dengan ambang batas (*threshold*) kemiripan **60%** sesuai hasil wawancara Bab 3.2.2[cite: 1]. Hasil pencarian menampilkan skor kecocokan visual serta tombol **Lihat** dan **Unduh** langsung[cite: 1].
- **⚡ Optimasi Performa & Responsivitas**  
  Mendukung paginasi (`LIMIT`/`OFFSET`), pembatasan pemindaian ar
