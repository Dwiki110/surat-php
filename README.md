<!-- HEADER BANNER GRADASI NAVY & EMAS -->
<div align="center">
  <img src="https://capsule-render.vercel.app/api?type=waving&color=0:0f172a,50:1e3a8a,100:d97706&height=210&section=header&text=Sistem%20Administrasi%20Surat&fontSize=36&fontColor=ffffff&fontAlignY=38&desc=Kecamatan%20Cibitung%20%E2%80%A2%20Implementasi%20Algoritma%20Levenshtein%20Distance&descSize=15&descColor=fde68a&descAlignY=60" width="100%" alt="Banner Sistem Administrasi Surat"/>

  <p>
    <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
    <img src="https://img.shields.io/badge/MySQL_PDO-005C84?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
    <img src="https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP"/>
    <img src="https://img.shields.io/badge/Algoritma-Levenshtein_Distance-D97706?style=for-the-badge" alt="Levenshtein"/>
    <img src="https://img.shields.io/badge/Status-Prototipe_Skripsi-16A34A?style=for-the-badge" alt="Status"/>
  </p>

  **Implementasi Web Tugas Akhir:** *"Penerapan Algoritma Levenshtein Distance untuk Website Administrasi Surat"*  
  Disusun oleh **Muhammad Dwiki Wicaksono** — Universitas Esa Unggul
</div>

---

## 🎨 Identitas Desain Antarmuka (UI Theme)

Antarmuka aplikasi dirancang menggunakan palet warna institusional yang formal, bersih, dan responsif:

![Primary Navy](https://img.shields.io/badge/Primary_Navy-%230F172A-0F172A?style=flat-square)
![Royal Blue](https://img.shields.io/badge/Accent_Blue-%231E3A8A-1E3A8A?style=flat-square)
![Institutional Gold](https://img.shields.io/badge/Institutional_Gold-%23D97706-D97706?style=flat-square)
![Soft Surface](https://img.shields.io/badge/Background_Light-%23F8FAFC-F8FAFC?style=flat-square)

> [!NOTE]
> Aplikasi ini dibangun menggunakan **PHP Native + MySQL** sesuai batasan lingkup penelitian (Bab 1.5) dan dioptimalkan untuk lingkungan pengembangan **XAMPP**.

---

## ✨ Fitur Utama Sistem

<table>
  <tr>
    <td width="50%" valign="top">
      <h3>🔐 Autentikasi & Peran Ganda</h3>
      <ul>
        <li>Login berbasis peran (<b>Resepsionis</b> & <b>Sekretaris</b>) sesuai <i>Use Case Diagram</i>.</li>
        <li>Router otomatis pada <code>dashboard.php</code> yang mengarahkan pengguna ke ruang kerja masing-masing.</li>
        <li>Proteksi sesi melalui <code>require_login()</code> dan <code>require_role()</code>.</li>
      </ul>
    </td>
    <td width="50%" valign="top">
      <h3>🔍 Pencarian Cerdas (Levenshtein)</h3>
      <ul>
        <li>Implementasi <code>levenshtein_matrix()</code> native di PHP untuk toleransi kesalahan ketik (<i>typo</i>).</li>
        <li>Ambang batas (<i>threshold</i>) kemiripan <b>60%</b> sesuai hasil wawancara Bab 3.2.2.</li>
        <li>Hasil lintas arsip dilengkapi skor visual serta tombol cepat <b>Lihat</b> dan <b>Unduh</b>.</li>
      </ul>
    </td>
  </tr>
  <tr>
    <td width="50%" valign="top">
      <h3>📥 Alur Surat Masuk</h3>
      <ul>
        <li>Siklus status terstruktur: <code>Baru</code> ➔ <code>Diproses</code> ➔ <code>Selesai</code>.</li>
        <li>Notifikasi otomatis ke pihak terkait di setiap perubahan status surat.</li>
        <li>Unggah dan pratinjau langsung berkas surat maupun hasil balasan.</li>
      </ul>
    </td>
    <td width="50%" valign="top">
      <h3>📤 Manajemen Surat Keluar & Performa</h3>
      <ul>
        <li>Pembuatan draf surat keluar dengan lampiran berkas PDF dan halaman detail khusus.</li>
        <li>Paginasi query (<code>LIMIT</code>/<code>OFFSET</code>) & batas pindai (<code>SEARCH_SCAN_LIMIT</code>) untuk ribuan arsip.</li>
        <li>Desain responsif dengan menu geser (<i>drawer sidebar</i>) pada layar mobile.</li>
      </ul>
    </td>
  </tr>
</table>

---

## 📂 Struktur Direktori

```text
surat-php/
├── 📁 assets/css/style.css           # Desain antarmuka institusional (Navy & Emas)
├── 📁 config/database.php            # Konfigurasi koneksi PDO ke MySQL
├── 📁 database/
│   ├── schema.sql                    # DDL tabel utama sesuai ERD Bab 3
│   ├── seed.php                      # Skrip pengisi akun & data simulasi awal
│   ├── reset_password.php            # Utilitas reset password akun demo
│   ├── upgrade_v2.sql                # Migrasi skema lampiran surat keluar
│   └── upgrade_v3_index.sql          # Penambahan index performa query
├── 📁 includes/
│   ├── auth.php                      # Middleware require_login() & require_role()
│   ├── functions.php                 # Algoritma Levenshtein, similarity ratio & helper
│   └── header.php / sidebar.php / footer.php
├── 📁 resepsionis/                   # Modul khusus peran Resepsionis
├── 📁 sekretaris/                    # Modul khusus peran Sekretaris
├── 📁 uploads/                       # Penyimpanan berkas PDF (masuk, keluar, hasil)
└── 📄 index.php / login.php / logout.php / dashboard.php
