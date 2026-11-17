-- Jalankan file ini lewat phpMyAdmin (tab SQL) untuk menambahkan index
-- pada kolom yang sering dipakai WHERE/ORDER BY, supaya query tetap cepat
-- walau jumlah data sudah ribuan/puluhan ribu baris.
-- Aman dijalankan kapan saja -- tidak menghapus/mengubah data yang ada.

ALTER TABLE surat_masuk
    ADD INDEX idx_surat_masuk_status (status),
    ADD INDEX idx_surat_masuk_created (created_at),
    ADD INDEX idx_surat_masuk_uploaded_by (uploaded_by);

ALTER TABLE surat_keluar
    ADD INDEX idx_surat_keluar_status (status),
    ADD INDEX idx_surat_keluar_created (created_at);

ALTER TABLE notifikasi
    ADD INDEX idx_notifikasi_penerima (penerima_role, sudah_dibaca),
    ADD INDEX idx_notifikasi_created (created_at);
