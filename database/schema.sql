-- =========================================================
-- Skema Database: Sistem Administrasi Surat Kecamatan Cibitung
-- Sesuai ERD pada Tugas Akhir (Gambar 3.2)
-- Import file ini dulu lewat phpMyAdmin / mysql CLI,
-- lalu jalankan database/seed.php sekali lewat browser.
-- =========================================================

CREATE DATABASE IF NOT EXISTS db_surat_kecamatan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_surat_kecamatan;

-- Tabel PENGGUNA
CREATE TABLE IF NOT EXISTS pengguna (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    nama_lengkap VARCHAR(100) NOT NULL,
    role ENUM('resepsionis', 'sekretaris') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel SURAT (surat masuk)
CREATE TABLE IF NOT EXISTS surat_masuk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(50) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    keterangan TEXT NULL,
    pengirim VARCHAR(100) NOT NULL,
    file_path VARCHAR(255) NULL,
    status ENUM('Baru', 'Diproses', 'Selesai') NOT NULL DEFAULT 'Baru',
    uploaded_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_surat_masuk_user FOREIGN KEY (uploaded_by) REFERENCES pengguna(id),
    INDEX idx_surat_masuk_status (status),
    INDEX idx_surat_masuk_created (created_at),
    INDEX idx_surat_masuk_uploaded_by (uploaded_by)
) ENGINE=InnoDB;

-- Tabel HASIL_SURAT (verifikasi Levenshtein saat surat selesai diproses)
CREATE TABLE IF NOT EXISTS hasil_surat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surat_id INT NOT NULL,
    judul_konfirmasi VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NULL,
    levenshtein_distance INT NOT NULL,
    similarity_pct FLOAT NOT NULL,
    uploaded_by INT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_hasil_surat_masuk FOREIGN KEY (surat_id) REFERENCES surat_masuk(id) ON DELETE CASCADE,
    CONSTRAINT fk_hasil_surat_user FOREIGN KEY (uploaded_by) REFERENCES pengguna(id)
) ENGINE=InnoDB;

-- Tabel surat keluar
CREATE TABLE IF NOT EXISTS surat_keluar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomor_surat VARCHAR(50) NOT NULL,
    judul VARCHAR(255) NOT NULL,
    tujuan VARCHAR(100) NOT NULL,
    perihal TEXT NULL,
    file_path VARCHAR(255) NULL,
    status ENUM('Draft', 'Terkirim') NOT NULL DEFAULT 'Draft',
    tgl_keluar DATE NULL,
    created_by INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_surat_keluar_user FOREIGN KEY (created_by) REFERENCES pengguna(id),
    INDEX idx_surat_keluar_status (status),
    INDEX idx_surat_keluar_created (created_at)
) ENGINE=InnoDB;

-- Tabel NOTIFIKASI
CREATE TABLE IF NOT EXISTS notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surat_id INT NULL,
    penerima_role ENUM('resepsionis', 'sekretaris') NOT NULL,
    tipe VARCHAR(50) NULL,
    pesan TEXT NOT NULL,
    sudah_dibaca TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_notifikasi_penerima (penerima_role, sudah_dibaca),
    INDEX idx_notifikasi_created (created_at)
) ENGINE=InnoDB;
