-- Jalankan file ini lewat phpMyAdmin (tab SQL) HANYA JIKA kamu sudah pernah
-- import schema.sql versi sebelumnya (tanpa kolom file_path di surat_keluar).
-- Kalau baru mulai dari nol, cukup import schema.sql saja, tidak perlu ini.

ALTER TABLE surat_keluar
    ADD COLUMN file_path VARCHAR(255) NULL AFTER perihal;
