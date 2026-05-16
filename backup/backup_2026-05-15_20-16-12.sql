-- ======================================================
-- BACKUP DATABASE: ujian_system
-- Tanggal: 2026-05-15 20:16:12
-- ======================================================

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `enrollment_mk`;
CREATE TABLE `enrollment_mk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `mk_id` int(11) NOT NULL,
  `mk_induk_id` int(11) DEFAULT NULL,
  `status` enum('active','completed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `enrollment_id` (`enrollment_id`),
  KEY `mk_id` (`mk_id`),
  KEY `fk_enrollment_mk_induk` (`mk_induk_id`),
  CONSTRAINT `enrollment_mk_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollment_mk_ibfk_2` FOREIGN KEY (`mk_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_enrollment_mk_induk` FOREIGN KEY (`mk_induk_id`) REFERENCES `mata_kuliah_induk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `enrollment_mk` VALUES ('1','1','1','1','active','2026-05-15 18:49:17'),
('2','1','2','2','active','2026-05-15 18:49:17'),
('3','2','1','1','active','2026-05-15 18:49:17'),
('4','3','6','4','active','2026-05-15 18:49:17');

DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mahasiswa_id` int(11) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `status` enum('pending','active') DEFAULT 'active',
  `tanggal_daftar` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mahasiswa_id` (`mahasiswa_id`),
  KEY `kelas_id` (`kelas_id`),
  CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`mahasiswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `enrollments` VALUES ('1','4','1','active','2026-05-15 18:48:47'),
('2','5','1','active','2026-05-15 18:48:47'),
('3','6','2','active','2026-05-15 18:48:47');

DROP TABLE IF EXISTS `jawaban`;
CREATE TABLE `jawaban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ujian_id` int(11) NOT NULL,
  `soal_id` int(11) NOT NULL,
  `jawaban_mahasiswa` text DEFAULT NULL,
  `skor` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ujian_id` (`ujian_id`),
  KEY `soal_id` (`soal_id`),
  CONSTRAINT `jawaban_ibfk_1` FOREIGN KEY (`ujian_id`) REFERENCES `ujian` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jawaban_ibfk_2` FOREIGN KEY (`soal_id`) REFERENCES `soal` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel jawaban kosong

DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(100) NOT NULL,
  `dosen_id` int(11) DEFAULT NULL,
  `tahun_ajaran` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `dosen_id` (`dosen_id`),
  CONSTRAINT `kelas_ibfk_1` FOREIGN KEY (`dosen_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `kelas` VALUES ('1','TI-2024-A','2','2024/2025','2026-05-15 18:47:49'),
('2','TI-2024-B','2','2024/2025','2026-05-15 18:47:49'),
('3','SI-2024-A','3','2024/2025','2026-05-15 18:47:49'),
('4','MI-2024-A','3','2024/2025','2026-05-15 18:47:49');

DROP TABLE IF EXISTS `mata_kuliah`;
CREATE TABLE `mata_kuliah` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mk_induk_id` int(11) DEFAULT NULL,
  `kode_mk` varchar(20) NOT NULL,
  `nama_mk` varchar(100) NOT NULL,
  `kelas_id` int(11) NOT NULL,
  `is_latihan` tinyint(1) DEFAULT 0,
  `durasi_ujian` int(11) DEFAULT 60,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `kelas_id` (`kelas_id`),
  KEY `fk_mk_induk` (`mk_induk_id`),
  CONSTRAINT `fk_mk_induk` FOREIGN KEY (`mk_induk_id`) REFERENCES `mata_kuliah_induk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mata_kuliah_ibfk_1` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mata_kuliah` VALUES ('1','1','IF101','Algoritma dan Pemrograman','1','0','60','2026-05-15 18:48:16'),
('2','2','IF102','Basis Data','1','0','60','2026-05-15 18:48:16'),
('3','3','IF103','Pemrograman Web','1','0','60','2026-05-15 18:48:16'),
('4','1','IF101','Algoritma dan Pemrograman','2','0','60','2026-05-15 18:48:16'),
('5','2','IF102','Basis Data','2','0','60','2026-05-15 18:48:16'),
('6','4','IF104','Jaringan Komputer','2','0','60','2026-05-15 18:48:16'),
('7','7','SI201','Sistem Informasi Manajemen','3','0','60','2026-05-15 18:48:16'),
('8','8','SI202','Analisis dan Perancangan SI','3','0','60','2026-05-15 18:48:16'),
('9','5','MI301','Multimedia','4','0','60','2026-05-15 18:48:18'),
('10','6','MI302','Desain Grafis','4','0','60','2026-05-15 18:48:18');

DROP TABLE IF EXISTS `mata_kuliah_induk`;
CREATE TABLE `mata_kuliah_induk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_mk` varchar(20) NOT NULL,
  `nama_mk` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_mk` (`kode_mk`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `mata_kuliah_induk` VALUES ('1','IF101','Algoritma dan Pemrograman','2026-05-15 20:10:05'),
('2','IF102','Basis Data','2026-05-15 20:10:05'),
('3','IF103','Pemrograman Web','2026-05-15 20:10:05'),
('4','IF104','Jaringan Komputer','2026-05-15 20:10:05'),
('5','MI301','Multimedia','2026-05-15 20:10:05'),
('6','MI302','Desain Grafis','2026-05-15 20:10:05'),
('7','SI201','Sistem Informasi Manajemen','2026-05-15 20:10:05'),
('8','SI202','Analisis dan Perancangan SI','2026-05-15 20:10:05');

DROP TABLE IF EXISTS `soal`;
CREATE TABLE `soal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mk_id` int(11) NOT NULL,
  `mk_induk_id` int(11) DEFAULT NULL,
  `tipe_soal` enum('pg','essay_mutlak','essay_argumen') NOT NULL,
  `teks_soal` text NOT NULL,
  `pilihan_A` text DEFAULT NULL,
  `pilihan_B` text DEFAULT NULL,
  `pilihan_C` text DEFAULT NULL,
  `pilihan_D` text DEFAULT NULL,
  `pilihan_E` text DEFAULT NULL,
  `kunci_jawaban` text NOT NULL,
  `bobot` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `mk_id` (`mk_id`),
  KEY `fk_soal_mk_induk` (`mk_induk_id`),
  CONSTRAINT `fk_soal_mk_induk` FOREIGN KEY (`mk_induk_id`) REFERENCES `mata_kuliah_induk` (`id`) ON DELETE CASCADE,
  CONSTRAINT `soal_ibfk_1` FOREIGN KEY (`mk_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `soal` VALUES ('26','1','1','essay_mutlak','Apa kepanjangan dari PHP?',NULL,NULL,NULL,NULL,NULL,'PHP Hypertext Preprocessor','10','2026-05-15 18:51:13'),
('27','1','1','essay_mutlak','Apa kepanjangan dari HTML?',NULL,NULL,NULL,NULL,NULL,'Hypertext Markup Language','10','2026-05-15 18:51:13'),
('28','1','1','essay_mutlak','Apa kepanjangan dari CSS?',NULL,NULL,NULL,NULL,NULL,'Cascading Style Sheets','10','2026-05-15 18:51:13'),
('29','1','1','essay_mutlak','Apa kepanjangan dari SQL?',NULL,NULL,NULL,NULL,NULL,'Structured Query Language','10','2026-05-15 18:51:13'),
('30','1','1','essay_mutlak','Apa kepanjangan dari API?',NULL,NULL,NULL,NULL,NULL,'Application Programming Interface','10','2026-05-15 18:51:13'),
('31','1','1','essay_mutlak','Apa kepanjangan dari IDE?',NULL,NULL,NULL,NULL,NULL,'Integrated Development Environment','10','2026-05-15 18:51:13'),
('32','1','1','essay_mutlak','Apa kepanjangan dari SDK?',NULL,NULL,NULL,NULL,NULL,'Software Development Kit','10','2026-05-15 18:51:13'),
('33','1','1','essay_mutlak','Apa kepanjangan dari MVC?',NULL,NULL,NULL,NULL,NULL,'Model View Controller','10','2026-05-15 18:51:13'),
('34','1','1','essay_mutlak','Apa kepanjangan dari REST?',NULL,NULL,NULL,NULL,NULL,'Representational State Transfer','10','2026-05-15 18:51:13'),
('35','1','1','essay_mutlak','Apa kepanjangan dari AJAX?',NULL,NULL,NULL,NULL,NULL,'Asynchronous JavaScript and XML','10','2026-05-15 18:51:13'),
('36','1','1','essay_argumen','Jelaskan pentingnya algoritma dalam pemrograman!',NULL,NULL,NULL,NULL,NULL,'Algoritma adalah langkah-langkah sistematis untuk menyelesaikan masalah. Tanpa algoritma, program akan tidak terstruktur dan sulit dikembangkan.','10','2026-05-15 18:51:16'),
('37','1','1','essay_argumen','Apa perbedaan while dan do-while?',NULL,NULL,NULL,NULL,NULL,'While mengecek kondisi terlebih dahulu sebelum mengeksekusi, sedangkan do-while mengeksekusi terlebih dahulu lalu mengecek kondisi.','10','2026-05-15 18:51:16'),
('38','1','1','essay_argumen','Jelaskan konsep inheritance dalam OOP!',NULL,NULL,NULL,NULL,NULL,'Inheritance adalah pewarisan sifat dari class parent ke class child, memungkinkan penggunaan ulang kode.','10','2026-05-15 18:51:16'),
('39','1','1','essay_argumen','Apa yang dimaksud dengan database normalization?',NULL,NULL,NULL,NULL,NULL,'Normalisasi adalah proses pengorganisasian data untuk mengurangi redundansi dan meningkatkan integritas data.','10','2026-05-15 18:51:16'),
('40','1','1','essay_argumen','Jelaskan perbedaan HTTP dan HTTPS!',NULL,NULL,NULL,NULL,NULL,'HTTPS adalah versi aman dari HTTP dengan enkripsi SSL/TLS untuk keamanan data.','10','2026-05-15 18:51:16'),
('41','1','1','essay_argumen','Apa itu deadlock dalam sistem operasi?',NULL,NULL,NULL,NULL,NULL,'Deadlock adalah kondisi dimana dua atau lebih proses saling menunggu sumber daya yang satu sama lain pegang.','10','2026-05-15 18:51:16'),
('42','1','1','essay_argumen','Jelaskan konsep waterfall dalam SDLC!',NULL,NULL,NULL,NULL,NULL,'Waterfall adalah model pengembangan perangkat lunak yang berurutan dari analisis, desain, implementasi, testing, hingga maintenance.','10','2026-05-15 18:51:16'),
('43','1','1','essay_argumen','Apa perbedaan array dan linked list?',NULL,NULL,NULL,NULL,NULL,'Array memiliki ukuran tetap dan akses acak cepat, linked list dinamis tapi aksesnya berurutan.','10','2026-05-15 18:51:16'),
('44','1','1','essay_argumen','Jelaskan apa itu recursion!',NULL,NULL,NULL,NULL,NULL,'Recursion adalah fungsi yang memanggil dirinya sendiri untuk menyelesaikan masalah yang lebih kecil.','10','2026-05-15 18:51:16'),
('45','1','1','essay_argumen','Apa yang dimaksud dengan polymorphism?',NULL,NULL,NULL,NULL,NULL,'Polymorphism adalah kemampuan objek untuk memiliki banyak bentuk dan merespon method yang sama dengan cara berbeda.','10','2026-05-15 18:51:16'),
('46','1','1','essay_argumen','Jelaskan perbedaan TCP dan UDP!',NULL,NULL,NULL,NULL,NULL,'TCP adalah connection-oriented dan reliable, UDP adalah connectionless dan faster tapi tidak reliable.','10','2026-05-15 18:51:16'),
('47','1','1','essay_argumen','Apa itu enkapsulasi dalam OOP?',NULL,NULL,NULL,NULL,NULL,'Enkapsulasi adalah menyembunyikan detail implementasi dan hanya mengekspos interface publik.','10','2026-05-15 18:51:16'),
('48','1','1','essay_argumen','Jelaskan konsep cloud computing!',NULL,NULL,NULL,NULL,NULL,'Cloud computing adalah model penyediaan sumber daya komputasi melalui internet, pay-per-use.','10','2026-05-15 18:51:16'),
('49','1','1','essay_argumen','Apa perbedaan git dan GitHub?',NULL,NULL,NULL,NULL,NULL,'Git adalah version control system, GitHub adalah platform hosting untuk repositori Git.','10','2026-05-15 18:51:16'),
('50','1','1','essay_argumen','Jelaskan apa itu responsive web design!',NULL,NULL,NULL,NULL,NULL,'Responsive web design adalah pendekatan desain website yang dapat menyesuaikan tampilan di berbagai ukuran layar.','10','2026-05-15 18:51:16'),
('51','1','1','pg','Apa kepanjangan dari IDE?','Integrated Development Environment','Internet Development Environment','Integrated Design Environment','Internal Development Environment','Interface Design Environment','B','10','2026-05-15 19:24:32'),
('52','1','1','pg','Manakah yang termasuk bahasa pemrograman tingkat tinggi?','Assembly','C','Python','Machine Code','Binary','C','10','2026-05-15 19:24:32'),
('53','1','1','pg','Apa fungsi dari compiler?','Menerjemahkan kode sumber ke kode mesin','Menjalankan program','Debugging program','Mengelola memori','Menghubungkan library','A','10','2026-05-15 19:24:32'),
('54','1','1','pg','Manakah yang termasuk tipe data primitif?','Array','String','Object','Integer','Class','D','10','2026-05-15 19:24:32'),
('55','1','1','pg','Apa output dari console.log(\"Hello\") di JavaScript?','\"Hello\"','Hello','undefined','null','Error','B','10','2026-05-15 19:24:32'),
('56','1','1','pg','Manakah yang merupakan operator logika AND?','||','!','&&','==','===','C','10','2026-05-15 19:24:32'),
('57','1','1','pg','Apa fungsi array dalam pemrograman?','Menyimpan banyak data dalam satu variabel','Menjalankan perulangan','Membuat fungsi','Menampilkan output','Membaca input','A','10','2026-05-15 19:24:32'),
('58','1','1','pg','Manakah yang termasuk perulangan (loop)?','if-else','for','switch-case','break','continue','B','10','2026-05-15 19:24:32'),
('59','1','1','pg','Apa kepanjangan dari OOP?','Object Oriented Programming','Object Oriented Protocol','Object Organization Programming','Operational Object Program','Open Object Protocol','A','10','2026-05-15 19:24:32'),
('60','1','1','pg','Manakah yang termasuk bahasa markup?','Java','Python','C++','HTML','PHP','D','10','2026-05-15 19:24:32'),
('61','1','1','pg','Apa fungsi dari debugger?','Mencari dan memperbaiki error','Menulis kode','Menjalankan program','Mengcompile program','Menghubungkan database','A','10','2026-05-15 19:24:32'),
('62','1','1','pg','Manakah yang termasuk operator perbandingan?','=','==','+','-','*','B','10','2026-05-15 19:24:32'),
('63','1','1','pg','Apa hasil dari 5 + \"5\" di JavaScript?','10','55','Error','undefined','null','B','10','2026-05-15 19:24:32'),
('64','1','1','pg','Manakah yang termasuk tipe data kompleks?','int','float','boolean','Array','char','D','10','2026-05-15 19:24:32'),
('65','1','1','pg','Apa fungsi dari Git?','Version Control System','Database Management','Web Server','Text Editor','Compiler','A','10','2026-05-15 19:24:32'),
('66','1','1','pg','Manakah yang termasuk framework PHP?','React','Angular','Laravel','Vue.js','Django','C','10','2026-05-15 19:24:32'),
('67','1','1','pg','Apa fungsi dari CSS?','Mengatur tampilan website','Membuat database','Mengatur logika website','Membuat animasi','Mengatur server','A','10','2026-05-15 19:24:32'),
('68','1','1','pg','Manakah yang termasuk database relational?','MongoDB','Redis','MySQL','Cassandra','Neo4j','C','10','2026-05-15 19:24:32'),
('69','1','1','pg','Apa kepanjangan dari SQL?','Structured Query Language','Simple Query Language','Standard Query Language','Structured Question Language','System Query Language','A','10','2026-05-15 19:24:32'),
('70','1','1','pg','Manakah yang termasuk perintah DML?','CREATE','ALTER','INSERT','DROP','TRUNCATE','C','10','2026-05-15 19:24:32'),
('71','1','1','pg','Apa fungsi dari primary key?','Identifikasi unik setiap record','Mengurutkan data','Mencari data','Menghapus data','Mempercepat query','A','10','2026-05-15 19:24:32'),
('72','1','1','pg','Manakah yang termasuk metode sorting?','Binary Search','Linear Search','Bubble Sort','Hash Table','Queue','C','10','2026-05-15 19:24:32'),
('73','1','1','pg','Apa kepanjangan dari API?','Application Programming Interface','Application Program Interface','Application Programming Integration','Application Protocol Interface','Application Program Integration','A','10','2026-05-15 19:24:32'),
('74','1','1','pg','Manakah yang termasuk protokol HTTP method?','SELECT','INSERT','GET','UPDATE','DELETE','C','10','2026-05-15 19:24:32'),
('75','1','1','pg','Apa fungsi dari JSON?','Format pertukaran data','Database','Web server','Framework','Library','A','10','2026-05-15 19:24:32');

DROP TABLE IF EXISTS `ujian`;
CREATE TABLE `ujian` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `enrollment_id` int(11) NOT NULL,
  `mk_id` int(11) NOT NULL,
  `mulai_ujian` datetime DEFAULT NULL,
  `selesai_ujian` datetime DEFAULT NULL,
  `status` enum('sedang','selesai','terputus') DEFAULT 'sedang',
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `jumlah_pindah_tab` int(11) DEFAULT 0,
  `soal_yang_dikeluarkan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `enrollment_id` (`enrollment_id`),
  KEY `mk_id` (`mk_id`),
  CONSTRAINT `ujian_ibfk_1` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ujian_ibfk_2` FOREIGN KEY (`mk_id`) REFERENCES `mata_kuliah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel ujian kosong

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dosen','mahasiswa') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `nim_nip` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `users` VALUES ('1','admin','0192023a7bbd73250516f069df18b500','admin','Administrator','admin@ujian.com',NULL,'2026-05-15 18:46:04'),
('2','dosen1','d5bbfb47ac3160c31fa8c247827115aa','dosen','Dr. Ahmad Budiman','ahmad@univ.ac.id','19800101201001','2026-05-15 18:46:38'),
('3','dosen2','d5bbfb47ac3160c31fa8c247827115aa','dosen','Dr. Siti Nurhaliza','siti@univ.ac.id','19850201201002','2026-05-15 18:46:38'),
('4','mahasiswa1','b398b8a18ef4f69811a32cf169946bac','mahasiswa','Andi Susanto','andi@email.com','2023001001','2026-05-15 18:47:16'),
('5','mahasiswa2','b398b8a18ef4f69811a32cf169946bac','mahasiswa','Budi Prasetyo','budi@email.com','2023001002','2026-05-15 18:47:16'),
('6','mahasiswa3','b398b8a18ef4f69811a32cf169946bac','mahasiswa','Citra Dewi','citra@email.com','2023001003','2026-05-15 18:47:16');

DROP TABLE IF EXISTS `v_nilai_mahasiswa`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_nilai_mahasiswa` AS select `u`.`id` AS `ujian_id`,`mhs`.`nama_lengkap` AS `nama_mahasiswa`,`mhs`.`nim_nip` AS `nim`,`k`.`nama_kelas` AS `nama_kelas`,`mk`.`nama_mk` AS `nama_mk`,`u`.`nilai_akhir` AS `nilai_akhir`,`u`.`mulai_ujian` AS `mulai_ujian`,`u`.`selesai_ujian` AS `selesai_ujian`,`u`.`jumlah_pindah_tab` AS `jumlah_pindah_tab` from ((((`ujian` `u` join `enrollments` `e` on(`u`.`enrollment_id` = `e`.`id`)) join `users` `mhs` on(`e`.`mahasiswa_id` = `mhs`.`id`)) join `mata_kuliah` `mk` on(`u`.`mk_id` = `mk`.`id`)) join `kelas` `k` on(`mk`.`kelas_id` = `k`.`id`)) where `u`.`status` = 'selesai' order by `u`.`id` desc;

-- Tabel v_nilai_mahasiswa kosong

SET FOREIGN_KEY_CHECKS=1;
