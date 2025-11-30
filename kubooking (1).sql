-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 21 Nov 2025 pada 13.53
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `kubooking`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `account`
--

CREATE TABLE `account` (
  `id_account` int NOT NULL,
  `id_registrasi` int DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','dosen','tendik','admin','super_admin') NOT NULL DEFAULT 'mahasiswa',
  `nim_nip` varchar(30) NOT NULL,
  `unit_jurusan` varchar(100) DEFAULT NULL,
  `angkatan` int DEFAULT NULL,
  `durasi_studi` int DEFAULT NULL,
  `aktif_sampai` date DEFAULT NULL,
  `status_aktif` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `account`
--

INSERT INTO `account` (`id_account`, `id_registrasi`, `nama`, `jurusan`, `prodi`, `email`, `password`, `role`, `nim_nip`, `unit_jurusan`, `angkatan`, `durasi_studi`, `aktif_sampai`, `status_aktif`, `created_at`) VALUES
(1, NULL, 'admin', NULL, NULL, 'admin@gmail.com', '$2y$10$JnxRlc2PPk5q615Ze1cc.OgQKBhtt2rPLigZMTdkZxkxSqXTtYzmC', 'admin', '123', NULL, NULL, NULL, NULL, 'aktif', '2025-11-19 13:30:19'),
(2, 1, 'Diandra Bagustri', 'TIK', 'TI', 'dbagustri@gmail.com', '$2y$10$JnxRlc2PPk5q615Ze1cc.OgQKBhtt2rPLigZMTdkZxkxSqXTtYzmC', 'mahasiswa', '2407411043', NULL, 2024, 3, '2027-12-31', 'aktif', '2025-11-19 13:31:36'),
(4, 3, 'andra', 'TIK', 'TI', 'andra@gmail.com', '$2y$10$MWCMsR0E0vttJP3gaTG2EO11mrug.SqjhrYr31in8uT42IS4xeS76', 'mahasiswa', '12345', NULL, 2012, 4, '2016-12-31', 'aktif', '2025-11-20 09:34:04'),
(5, 4, 'Mafud', 'TIK', 'TI', 'mafudmmd@gmail.com', '$2y$10$dSmNxiU6pQc0ztuwchbeoelaIILWYT63OmQLMsMHj1vOmBpRXD7KO', 'mahasiswa', '111', NULL, 2025, 4, '2029-12-31', 'aktif', '2025-11-21 18:14:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `account_suspend`
--

CREATE TABLE `account_suspend` (
  `id_suspend` int NOT NULL,
  `id_user` int NOT NULL,
  `suspend_count` int NOT NULL DEFAULT '0',
  `suspended` enum('yes','no') NOT NULL DEFAULT 'no',
  `tanggal_suspend` datetime DEFAULT NULL,
  `tanggal_berakhir` datetime DEFAULT NULL,
  `alasan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `bookings`
--

CREATE TABLE `bookings` (
  `id_bookings` int NOT NULL,
  `id_pj` int DEFAULT NULL,
  `id_ruangan` int NOT NULL,
  `booking_code` char(8) NOT NULL,
  `guest_name` varchar(100) DEFAULT NULL,
  `guest_email` varchar(100) DEFAULT NULL,
  `guest_phone` varchar(20) DEFAULT NULL,
  `kode_kelompok` varchar(6) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `jumlah_anggota` int NOT NULL DEFAULT '1',
  `is_external` tinyint(1) NOT NULL DEFAULT '0',
  `surat_izin` varchar(255) DEFAULT NULL,
  `reschedule_request` tinyint(1) NOT NULL DEFAULT '0',
  `asal_instansi` varchar(100) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `keperluan` text,
  `group_expire_at` datetime DEFAULT NULL,
  `submitted` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `bookings`
--

INSERT INTO `bookings` (`id_bookings`, `id_pj`, `id_ruangan`, `booking_code`, `guest_name`, `guest_email`, `guest_phone`, `kode_kelompok`, `start_time`, `end_time`, `jumlah_anggota`, `is_external`, `surat_izin`, `reschedule_request`, `asal_instansi`, `tanggal`, `keperluan`, `group_expire_at`, `submitted`) VALUES
(1, 2, 3, 'XQ8ETWHS', NULL, NULL, NULL, 'zv6ko5', '2025-11-19 08:00:00', '2025-11-19 09:00:00', 2, 0, NULL, 0, NULL, '2025-11-19', '                            ', '2025-11-19 06:42:12', 0),
(2, NULL, 3, 'M8URTW32', NULL, NULL, NULL, '54wf3m', '2025-11-20 08:00:00', '2025-11-20 09:00:00', 2, 0, NULL, 0, NULL, '2025-11-20', '                            ', '2025-11-20 02:14:03', 0),
(3, 4, 1, 'T6SH2EYI', NULL, NULL, NULL, 'x8f5dz', '2025-11-20 08:00:00', '2025-11-20 10:00:00', 2, 0, NULL, 0, NULL, '2025-11-20', '                            ', '2025-11-20 02:44:47', 0),
(4, 4, 2, '0M327QFX', NULL, NULL, NULL, '2ua3oi', '2025-11-20 08:00:00', '2025-11-20 09:00:00', 2, 0, NULL, 0, NULL, '2025-11-20', '                            ', '2025-11-20 02:46:28', 0),
(6, 2, 1, 'ZOQ1M0IA', NULL, NULL, NULL, 'aczbv4', '2025-11-21 08:00:00', '2025-11-21 09:00:00', 2, 0, NULL, 0, NULL, '2025-11-21', NULL, '2025-11-21 06:08:33', 0),
(7, 4, 1, 'QY9ZGPLX', NULL, NULL, NULL, 'd63znp', '2025-11-21 08:00:00', '2025-11-21 09:00:00', 4, 0, NULL, 0, NULL, '2025-11-21', NULL, '2025-11-21 11:21:18', 1),
(8, 4, 1, 'PB41AMGE', NULL, NULL, NULL, 'mfbsv1', '2025-11-21 09:00:00', '2025-11-21 10:00:00', 4, 0, NULL, 0, NULL, '2025-11-21', NULL, '2025-11-21 11:38:48', 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_member`
--

CREATE TABLE `booking_member` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_bookings` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `booking_member`
--

INSERT INTO `booking_member` (`id`, `id_user`, `id_bookings`) VALUES
(1, 2, 1),
(3, 4, 3),
(4, 4, 4),
(7, 2, 6),
(9, 2, 7),
(8, 4, 7),
(10, 5, 7),
(13, 2, 8),
(11, 4, 8),
(12, 5, 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_reschedule`
--

CREATE TABLE `booking_reschedule` (
  `id_reschedule` int NOT NULL,
  `id_bookings` int NOT NULL,
  `id_ruangan` int NOT NULL,
  `id_user` int NOT NULL,
  `new_start_time` datetime NOT NULL,
  `new_end_time` datetime NOT NULL,
  `new_tanggal` date NOT NULL,
  `alasan` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking_status`
--

CREATE TABLE `booking_status` (
  `id_status` int NOT NULL,
  `id_bookings` int NOT NULL,
  `id_reschedule` int DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled','selesai','reschedule_pending','reschedule_approved','reschedule_rejected') NOT NULL,
  `alasan_reject` text,
  `alasan_reschedule` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `booking_status`
--

INSERT INTO `booking_status` (`id_status`, `id_bookings`, `id_reschedule`, `status`, `alasan_reject`, `alasan_reschedule`, `created_at`) VALUES
(1, 7, NULL, 'pending', NULL, NULL, '2025-11-21 18:15:27'),
(2, 7, NULL, 'approved', NULL, NULL, '2025-11-21 18:17:34'),
(3, 8, NULL, 'pending', NULL, NULL, '2025-11-21 18:33:19'),
(4, 8, NULL, 'approved', NULL, NULL, '2025-11-21 18:34:08');

-- --------------------------------------------------------

--
-- Struktur dari tabel `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id_fasilitas` int NOT NULL,
  `nama_fasilitas` varchar(100) NOT NULL,
  `icon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `fasilitas_ruangan`
--

CREATE TABLE `fasilitas_ruangan` (
  `id_fasilitas_ruangan` int NOT NULL,
  `id_ruangan` int NOT NULL,
  `id_facility` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int NOT NULL,
  `id_bookings` int NOT NULL,
  `id_user` int NOT NULL,
  `rating` tinyint NOT NULL,
  `komentar` text,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal_ruangan`
--

CREATE TABLE `jadwal_ruangan` (
  `id` int NOT NULL,
  `id_ruangan` int NOT NULL,
  `hari` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `open_time` time NOT NULL,
  `close_time` time NOT NULL,
  `break_start` time DEFAULT NULL,
  `break_end` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `registrasi`
--

CREATE TABLE `registrasi` (
  `id_registrasi` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nim_nip` varchar(30) NOT NULL,
  `unit_jurusan` varchar(100) DEFAULT NULL,
  `screenshot_kubaca` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `role_registrasi` enum('mahasiswa','dosen','tendik') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `registrasi`
--

INSERT INTO `registrasi` (`id_registrasi`, `nama`, `jurusan`, `prodi`, `email`, `password`, `nim_nip`, `unit_jurusan`, `screenshot_kubaca`, `status`, `role_registrasi`, `created_at`) VALUES
(1, 'Diandra Bagustri', 'TIK', 'TI', 'dbagustri@gmail.com', '$2y$10$JnxRlc2PPk5q615Ze1cc.OgQKBhtt2rPLigZMTdkZxkxSqXTtYzmC', '2407411043', NULL, 'uploads/1763532590_pnj.jpeg', 'approved', 'mahasiswa', '2025-11-19 13:09:50'),
(2, 'Mafud', 'TIK', 'TI', 'mafudmd@gmail.com', '$2y$10$3dtZLpY2RnQ.5jfLdZSMPOiQoDjHZpces110Iq3prBBlPKC573wk.', '2407411045', NULL, 'uploads/1763602928_pnj.jpeg', 'approved', 'mahasiswa', '2025-11-20 08:42:09'),
(3, 'andra', 'TIK', 'TI', 'andra@gmail.com', '$2y$10$MWCMsR0E0vttJP3gaTG2EO11mrug.SqjhrYr31in8uT42IS4xeS76', '12345', NULL, 'uploads/1763605984_pnj.jpeg', 'approved', 'mahasiswa', '2025-11-20 09:33:04'),
(4, 'Mafud', 'TIK', 'TI', 'mafudmmd@gmail.com', '$2y$10$dSmNxiU6pQc0ztuwchbeoelaIILWYT63OmQLMsMHj1vOmBpRXD7KO', '111', NULL, 'uploads/1763723641_WhatsAppImage2025-11-21at13.38.47_50c85711.jpg', 'approved', 'mahasiswa', '2025-11-21 18:14:01');

-- --------------------------------------------------------

--
-- Struktur dari tabel `ruangan`
--

CREATE TABLE `ruangan` (
  `id_ruangan` int NOT NULL,
  `nama_ruangan` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `kapasitas_min` int NOT NULL,
  `kapasitas_max` int NOT NULL,
  `status_operasional` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `lokasi` varchar(100) DEFAULT NULL,
  `foto_ruangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `ruangan`
--

INSERT INTO `ruangan` (`id_ruangan`, `nama_ruangan`, `kategori`, `kapasitas_min`, `kapasitas_max`, `status_operasional`, `lokasi`, `foto_ruangan`) VALUES
(1, 'Ruang Diskusi A', 'Diskusi', 3, 5, 'aktif', 'Gedung A Lantai 1', NULL),
(2, 'Ruang Diskusi B', 'Diskusi', 3, 5, 'aktif', 'Gedung A Lantai 2', NULL),
(3, 'Ruang Belajar A', 'Belajar', 5, 10, 'aktif', 'Gedung B Lantai 1', NULL),
(4, 'Ruang Belajar B', 'Belajar', 5, 10, 'aktif', 'Gedung B Lantai 2', NULL),
(5, 'Ruang Rapat A', 'Rapat', 10, 15, 'aktif', 'Gedung C Lantai 1', NULL),
(6, 'Ruang Rapat B', 'Rapat', 10, 15, 'aktif', 'Gedung C Lantai 2', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `template_notifikasi`
--

CREATE TABLE `template_notifikasi` (
  `id_template` int NOT NULL,
  `jenis` enum('verifikasi','booking','pengingat','suspend','reschedule') NOT NULL,
  `judul` varchar(150) DEFAULT NULL,
  `kanal` enum('email','whatsapp','inapp') NOT NULL DEFAULT 'inapp',
  `pesan` text NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id_account`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nim_nip` (`nim_nip`),
  ADD KEY `idx_acc_role` (`role`),
  ADD KEY `idx_acc_status` (`status_aktif`),
  ADD KEY `fk_acc_reg` (`id_registrasi`);

--
-- Indeks untuk tabel `account_suspend`
--
ALTER TABLE `account_suspend`
  ADD PRIMARY KEY (`id_suspend`),
  ADD UNIQUE KEY `uk_suspend_user` (`id_user`),
  ADD KEY `idx_suspend_status` (`suspended`);

--
-- Indeks untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id_bookings`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD UNIQUE KEY `uk_booking_code` (`booking_code`),
  ADD UNIQUE KEY `kode_kelompok` (`kode_kelompok`),
  ADD KEY `idx_booking_ruangan_waktu` (`id_ruangan`,`start_time`,`end_time`),
  ADD KEY `idx_booking_pj` (`id_pj`),
  ADD KEY `idx_booking_tanggal` (`tanggal`);

--
-- Indeks untuk tabel `booking_member`
--
ALTER TABLE `booking_member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_booking_member` (`id_bookings`,`id_user`),
  ADD KEY `idx_bm_booking` (`id_bookings`),
  ADD KEY `idx_bm_user` (`id_user`);

--
-- Indeks untuk tabel `booking_reschedule`
--
ALTER TABLE `booking_reschedule`
  ADD PRIMARY KEY (`id_reschedule`),
  ADD KEY `idx_res_booking` (`id_bookings`),
  ADD KEY `idx_res_ruangan` (`id_ruangan`),
  ADD KEY `idx_res_user` (`id_user`);

--
-- Indeks untuk tabel `booking_status`
--
ALTER TABLE `booking_status`
  ADD PRIMARY KEY (`id_status`),
  ADD KEY `idx_status_booking` (`id_bookings`),
  ADD KEY `idx_status_reschedule` (`id_reschedule`),
  ADD KEY `idx_status_type` (`status`);

--
-- Indeks untuk tabel `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id_fasilitas`);

--
-- Indeks untuk tabel `fasilitas_ruangan`
--
ALTER TABLE `fasilitas_ruangan`
  ADD PRIMARY KEY (`id_fasilitas_ruangan`),
  ADD UNIQUE KEY `uk_facility_ruangan` (`id_ruangan`,`id_facility`),
  ADD KEY `idx_fr_ruangan` (`id_ruangan`),
  ADD KEY `idx_fr_facility` (`id_facility`);

--
-- Indeks untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`),
  ADD UNIQUE KEY `uk_feedback` (`id_bookings`,`id_user`),
  ADD KEY `idx_fb_booking` (`id_bookings`),
  ADD KEY `idx_fb_user` (`id_user`);

--
-- Indeks untuk tabel `jadwal_ruangan`
--
ALTER TABLE `jadwal_ruangan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ruangan_hari` (`id_ruangan`,`hari`),
  ADD KEY `idx_jadwal_ruangan` (`id_ruangan`),
  ADD KEY `idx_jadwal_hari` (`hari`);

--
-- Indeks untuk tabel `registrasi`
--
ALTER TABLE `registrasi`
  ADD PRIMARY KEY (`id_registrasi`),
  ADD UNIQUE KEY `uk_reg_email` (`email`),
  ADD UNIQUE KEY `uk_reg_nim` (`nim_nip`),
  ADD KEY `idx_reg_status` (`status`);

--
-- Indeks untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  ADD PRIMARY KEY (`id_ruangan`),
  ADD UNIQUE KEY `uk_ruangan_nama` (`nama_ruangan`),
  ADD KEY `idx_ruangan_status` (`status_operasional`),
  ADD KEY `idx_ruangan_kategori` (`kategori`);

--
-- Indeks untuk tabel `template_notifikasi`
--
ALTER TABLE `template_notifikasi`
  ADD PRIMARY KEY (`id_template`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `account`
--
ALTER TABLE `account`
  MODIFY `id_account` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `account_suspend`
--
ALTER TABLE `account_suspend`
  MODIFY `id_suspend` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id_bookings` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `booking_member`
--
ALTER TABLE `booking_member`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `booking_reschedule`
--
ALTER TABLE `booking_reschedule`
  MODIFY `id_reschedule` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `booking_status`
--
ALTER TABLE `booking_status`
  MODIFY `id_status` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id_fasilitas` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `fasilitas_ruangan`
--
ALTER TABLE `fasilitas_ruangan`
  MODIFY `id_fasilitas_ruangan` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal_ruangan`
--
ALTER TABLE `jadwal_ruangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `registrasi`
--
ALTER TABLE `registrasi`
  MODIFY `id_registrasi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `ruangan`
--
ALTER TABLE `ruangan`
  MODIFY `id_ruangan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `template_notifikasi`
--
ALTER TABLE `template_notifikasi`
  MODIFY `id_template` int NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `account`
--
ALTER TABLE `account`
  ADD CONSTRAINT `fk_acc_reg` FOREIGN KEY (`id_registrasi`) REFERENCES `registrasi` (`id_registrasi`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `account_suspend`
--
ALTER TABLE `account_suspend`
  ADD CONSTRAINT `fk_suspend_user` FOREIGN KEY (`id_user`) REFERENCES `account` (`id_account`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_booking_pj` FOREIGN KEY (`id_pj`) REFERENCES `account` (`id_account`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_booking_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE RESTRICT;

--
-- Ketidakleluasaan untuk tabel `booking_member`
--
ALTER TABLE `booking_member`
  ADD CONSTRAINT `fk_bm_booking` FOREIGN KEY (`id_bookings`) REFERENCES `bookings` (`id_bookings`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_bm_user` FOREIGN KEY (`id_user`) REFERENCES `account` (`id_account`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `booking_reschedule`
--
ALTER TABLE `booking_reschedule`
  ADD CONSTRAINT `fk_res_booking` FOREIGN KEY (`id_bookings`) REFERENCES `bookings` (`id_bookings`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_res_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE RESTRICT,
  ADD CONSTRAINT `fk_res_user` FOREIGN KEY (`id_user`) REFERENCES `account` (`id_account`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `booking_status`
--
ALTER TABLE `booking_status`
  ADD CONSTRAINT `fk_status_booking` FOREIGN KEY (`id_bookings`) REFERENCES `bookings` (`id_bookings`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_status_reschedule` FOREIGN KEY (`id_reschedule`) REFERENCES `booking_reschedule` (`id_reschedule`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `fasilitas_ruangan`
--
ALTER TABLE `fasilitas_ruangan`
  ADD CONSTRAINT `fk_fr_facility` FOREIGN KEY (`id_facility`) REFERENCES `fasilitas` (`id_fasilitas`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fr_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_fb_booking` FOREIGN KEY (`id_bookings`) REFERENCES `bookings` (`id_bookings`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_fb_user` FOREIGN KEY (`id_user`) REFERENCES `account` (`id_account`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `jadwal_ruangan`
--
ALTER TABLE `jadwal_ruangan`
  ADD CONSTRAINT `fk_jadwal_ruangan` FOREIGN KEY (`id_ruangan`) REFERENCES `ruangan` (`id_ruangan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
