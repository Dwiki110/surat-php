<!-- HEADER BANNER GRADASI NAVY & EMAS -->
<div align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:0f172a,50:1e3a8a,100:d97706&height=210&section=header&text=Sistem%20Administrasi%20Surat&fontSize=36&fontColor=ffffff&fontAlignY=38&desc=Kecamatan%20Cibitung%20%E2%80%A2%20Implementasi%20Algoritma%20Levenshtein%20Distance&descSize=15&descColor=fde68a&descAlignY=60" width="100%" alt="Banner Sistem Administrasi Surat"/>

  <p>
    <img src="https://img.shields.io/badge/PHP-Native-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
    <img src="https://img.shields.io/badge/MySQL-PDO-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
    <img src="https://img.shields.io/badge/XAMPP-Environment-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP"/>
    <img src="https://img.shields.io/badge/Algoritma-Levenshtein_Distance-D97706?style=for-the-badge" alt="Levenshtein"/>
    <img src="https://img.shields.io/badge/Status-Prototipe_Skripsi-16A34A?style=for-the-badge" alt="Status"/>
  </p>

  **Implementasi Web Tugas Akhir:** *"Penerapan Algoritma Levenshtein Distance untuk Website Administrasi Surat"*  
  Disusun oleh **Muhammad Dwiki Wicaksono** — Program Studi Teknik Informatika, Universitas Esa Unggul
</div>

---

## 📖 1. Latar Belakang & Gambaran Umum

Pengelolaan arsip persuratan secara konvensional sering menghadapi kendala dalam pelacakan status tindak lanjut surat serta lambatnya pencarian arsip lama—terutama ketika pengguna mengalami kesalahan pengetikan (*typo*) pada kata kunci nomor surat, pengirim, maupun perihal.

Aplikasi **Sistem Administrasi Surat Kecamatan Cibitung** dibangun menggunakan **PHP Native** dan **MySQL** (sesuai batasan lingkup penelitian Bab 1.5 pada environment XAMPP) untuk menyediakan solusi terintegrasi yang mencakup:
1. **Digitalisasi Arsip:** Penyimpanan metadata surat beserta dokumen fisiknya (format PDF) secara terpusat.
2. **Pemisahan Tugas Berbasis Peran (*Role-Based Access Control*):** Memisahkan ruang kerja dan hak akses antara bagian penerimaan depan (**Resepsionis**) dan bagian tata usaha/pimpinan (**Sekretaris**) sesuai *Use Case Diagram*.
3. **Pencarian Toleran Kesalahan (*Fuzzy String Matching*):** Memanfaatkan algoritma **Levenshtein Distance** agar pencarian dokumen tetap akurat meskipun kata kunci yang dimasukkan tidak 100% identik dengan data di database.

---

## 🎨 2. Identitas Desain Antarmuka (UI Theme)

Antarmuka dirancang dengan pendekatan *institusional* menggunakan palet warna **Navy & Emas** yang formal, bersih, serta mendukung tampilan responsif (*mobile-friendly* melalui *drawer sidebar* dan tabel *horizontal-scroll*):

![Primary Navy](https://img.shields.io/badge/Primary_Navy-%230F172A-0F172A?style=flat-square)
![Royal Blue](https://img.shields.io/badge/Accent_Blue-%231E3A8A-1E3A8A?style=flat-square)
![Institutional Gold](https://img.shields.io/badge/Institutional_Gold-%23D97706-D97706?style=flat-square)
![Soft Surface](https://img.shields.io/badge/Background_Light-%23F8FAFC-F8FAFC?style=flat-square)

---

## 🔄 3. Alur Kerja Sistem (End-to-End Workflow)

Sistem membagi alur operasional ke dalam dua aktor utama dengan siklus status yang saling terhubung melalui notifikasi otomatis:

```text
[Surat Fisik Diterima] 
       │
       ▼
👤 RESEPSIONIS (Input Data & Upload PDF) ──► Status: [BARU] ──► 🔔 Notifikasi ke Sekretaris
                                                                       │
       ┌───────────────────────────────────────────────────────────────┘
       ▼
👔 SEKRETARIS (Tinjau & Tindak Lanjut) ───► Status: [DIPROSES] ──► 🔔 Notifikasi ke Resepsionis
       │
       ▼
👔 SEKRETARIS (Upload Hasil Balasan) ─────► Status: [SELESAI] ───► 🔔 Notifikasi ke Resepsionis
