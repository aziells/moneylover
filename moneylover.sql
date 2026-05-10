-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 05, 2026 at 02:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `moneylover`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `laporan_bulanan` (IN `p_bulan` INT, IN `p_tahun` INT)   BEGIN
    -- Ringkasan Bulanan
    SELECT 
        'Ringkasan' AS kategori,
        COUNT(m.id_pemesanan) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        NULL AS detail
    FROM memesan m
    WHERE MONTH(m.tgl_pemesanan) = p_bulan 
      AND YEAR(m.tgl_pemesanan) = p_tahun;

    -- Per Menu
    SELECT 
        CONCAT('Menu: ', mn.nama_menu) AS kategori,
        SUM(m.jumlah) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        mn.nama_menu AS detail
    FROM memesan m
    JOIN menu mn ON m.id_menu = mn.id_menu
    WHERE MONTH(m.tgl_pemesanan) = p_bulan 
      AND YEAR(m.tgl_pemesanan) = p_tahun
    GROUP BY mn.nama_menu;

    -- Per Kasir
    SELECT 
        CONCAT('Kasir: ', u.nama) AS kategori,
        COUNT(m.id_pemesanan) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        u.nama AS detail
    FROM memesan m
    JOIN user u ON m.id_user = u.id_user
    WHERE MONTH(m.tgl_pemesanan) = p_bulan 
      AND YEAR(m.tgl_pemesanan) = p_tahun
    GROUP BY u.nama;

    -- Per Metode Pembayaran
    SELECT 
        CONCAT('Metode: ', m.metode_pembayaran) AS kategori,
        COUNT(m.id_pemesanan) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        m.metode_pembayaran AS detail
    FROM memesan m
    WHERE MONTH(m.tgl_pemesanan) = p_bulan 
      AND YEAR(m.tgl_pemesanan) = p_tahun
    GROUP BY m.metode_pembayaran;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `laporan_bulanan_union` (IN `p_bulan` INT, IN `p_tahun` INT)   BEGIN
    -- Ringkasan
    SELECT 
        'Ringkasan' AS kategori,
        COUNT(m.id_pemesanan) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        NULL AS detail
    FROM memesan m
    WHERE MONTH(m.tgl_pemesanan) = p_bulan AND YEAR(m.tgl_pemesanan) = p_tahun

    UNION ALL

    -- Per Menu
    SELECT 
        CONCAT('Menu: ', mn.nama_menu) AS kategori,
        SUM(m.jumlah) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        mn.nama_menu AS detail
    FROM memesan m
    JOIN menu mn ON m.id_menu = mn.id_menu
    WHERE MONTH(m.tgl_pemesanan) = p_bulan AND YEAR(m.tgl_pemesanan) = p_tahun
    GROUP BY mn.nama_menu

    UNION ALL

    -- Per Kasir
    SELECT 
        CONCAT('Kasir: ', u.nama) AS kategori,
        COUNT(m.id_pemesanan) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        u.nama AS detail
    FROM memesan m
    JOIN user u ON m.id_user = u.id_user
    WHERE MONTH(m.tgl_pemesanan) = p_bulan AND YEAR(m.tgl_pemesanan) = p_tahun
    GROUP BY u.nama

    UNION ALL

    -- Per Metode Pembayaran
    SELECT 
        CONCAT('Metode: ', m.metode_pembayaran) AS kategori,
        COUNT(m.id_pemesanan) AS jumlah_transaksi,
        SUM(m.harga_total) AS total_pendapatan,
        AVG(m.harga_total) AS rata_rata_transaksi,
        m.metode_pembayaran AS detail
    FROM memesan m
    WHERE MONTH(m.tgl_pemesanan) = p_bulan AND YEAR(m.tgl_pemesanan) = p_tahun
    GROUP BY m.metode_pembayaran;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `laporan_bulanan`
-- (See below for the actual view)
--
CREATE TABLE `laporan_bulanan` (
`kategori` varchar(107)
,`jumlah_transaksi` decimal(32,0)
,`total_pendapatan` decimal(32,2)
,`rata_rata_transaksi` decimal(14,6)
,`detail` varchar(100)
,`bulan` int(2)
,`tahun` int(4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `laporan_bulanan_jan2026`
-- (See below for the actual view)
--
CREATE TABLE `laporan_bulanan_jan2026` (
`kategori` varchar(107)
,`jumlah_transaksi` decimal(32,0)
,`total_pendapatan` decimal(32,2)
,`rata_rata_transaksi` decimal(14,6)
,`detail` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `memesan`
--

CREATE TABLE `memesan` (
  `id_pemesanan` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `nama_pelanggan` varchar(100) DEFAULT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_total` decimal(10,2) NOT NULL,
  `tgl_pemesanan` date NOT NULL,
  `jam_pemesanan` time NOT NULL,
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `status_pembayaran` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `memesan`
--

INSERT INTO `memesan` (`id_pemesanan`, `id_user`, `id_menu`, `nama_pelanggan`, `jumlah`, `harga_total`, `tgl_pemesanan`, `jam_pemesanan`, `metode_pembayaran`, `status_pembayaran`) VALUES
(9, 2, 4, 'Pelanggan MLC00283', 1, 25000.00, '2025-12-29', '04:15:10', 'cash', 'success'),
(10, 2, 4, 'Pelanggan MLC04758', 2, 50000.00, '2025-12-29', '04:29:27', 'qr', 'success'),
(11, 2, 2, 'Pelanggan MLC03832', 2, 20000.00, '2025-12-29', '04:31:31', 'qr', 'success'),
(12, 2, 1, 'Pelanggan MLC09064', 1, 15000.00, '2025-12-29', '04:38:14', 'qr', 'success'),
(13, 2, 4, 'Pelanggan MLC05706', 1, 25000.00, '2025-12-29', '04:39:11', 'cash', 'success'),
(14, 2, 4, 'Pelanggan MLC07531', 1, 25000.00, '2025-12-29', '04:42:10', 'qr', 'success'),
(15, 2, 3, 'Pelanggan MLC06795', 3, 24000.00, '2025-12-29', '04:43:51', 'cash', 'success'),
(16, 2, 3, 'Pelanggan MLC00561', 2, 16000.00, '2025-12-29', '04:44:18', 'qr', 'success'),
(17, 2, 1, 'Pelanggan MLC00878', 2, 30000.00, '2025-12-29', '04:44:40', 'qr', 'success'),
(18, 2, 24, 'Pelanggan MLC01050', 1, 15000.00, '2025-12-30', '05:39:56', 'qr', 'success'),
(19, 2, 3, 'Pelanggan MLC01050', 1, 8000.00, '2025-12-30', '05:39:56', 'qr', 'success'),
(20, 2, 2, 'Pelanggan MLC01050', 1, 10000.00, '2025-12-30', '05:39:56', 'qr', 'success'),
(21, 2, 24, 'Pelanggan MLC06134', 1, 15000.00, '2025-12-31', '06:30:59', 'cash', 'success'),
(22, 2, 1, 'Pelanggan MLC06134', 1, 15000.00, '2025-12-31', '06:30:59', 'cash', 'success'),
(23, 2, 2, 'Pelanggan MLC00399', 1, 10000.00, '2026-01-05', '11:37:12', 'cash', 'success'),
(24, 2, 3, 'Pelanggan MLC00399', 1, 8000.00, '2026-01-05', '11:37:12', 'cash', 'success'),
(25, 2, 24, 'Pelanggan MLC00465', 1, 15000.00, '2026-01-05', '12:00:15', 'cash', 'success'),
(26, 2, 2, 'Pelanggan MLC00465', 1, 10000.00, '2026-01-05', '12:00:15', 'cash', 'success'),
(27, 2, 3, 'Pelanggan MLC03217', 2, 16000.00, '2026-01-05', '12:00:33', 'qr', 'success'),
(28, 2, 1, 'Pelanggan MLC03217', 1, 15000.00, '2026-01-05', '12:00:33', 'qr', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) DEFAULT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `harga_jual` decimal(10,2) NOT NULL,
  `stok` int(11) DEFAULT 0,
  `status` varchar(50) DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_menu`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id_menu`, `kode_barang`, `nama_menu`, `harga_jual`, `stok`, `status`, `gambar`) VALUES
(1, NULL, 'Mathca Latte', 15000.00, 15, 'tersedia', '1767069559_6953577788e9c.jpg'),
(2, NULL, 'Mathca Ice Cream', 10000.00, 99, 'tersedia', '1767069550_6953576e406c3.jpg'),
(3, NULL, 'Cookies', 8000.00, 15, 'tersedia', '1767069536_695357608d748.jpeg'),
(4, NULL, 'spagheti ciken', 20000.00, 123, 'tersedia', '1767065148_6953463c207a6.jpg'),
(24, '24', 'cookies coklat', 15000.00, 45, 'tersedia', '1767069457_6953571164e49.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `no_telepon` varchar(15) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','kasir') NOT NULL,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `nama`, `no_telepon`, `email`, `password`, `role`) VALUES
(1, 'zahra', 'zahra', '1234567', 'zahrara@gmail.com', 'admin123', 'admin'),
(2, 'azahra', 'azahra', '1234567890', 'azahra@gmail.com', 'azahrakasir', 'kasir');

-- --------------------------------------------------------

--
-- Structure for view `laporan_bulanan`
--
DROP TABLE IF EXISTS `laporan_bulanan`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `laporan_bulanan`  AS SELECT 'Ringkasan' AS `kategori`, count(`m`.`id_pemesanan`) AS `jumlah_transaksi`, sum(`m`.`harga_total`) AS `total_pendapatan`, avg(`m`.`harga_total`) AS `rata_rata_transaksi`, NULL AS `detail`, month(`m`.`tgl_pemesanan`) AS `bulan`, year(`m`.`tgl_pemesanan`) AS `tahun` FROM `memesan` AS `m` GROUP BY month(`m`.`tgl_pemesanan`), year(`m`.`tgl_pemesanan`)union all select concat('Menu: ',`mn`.`nama_menu`) AS `kategori`,sum(`m`.`jumlah`) AS `jumlah_transaksi`,sum(`m`.`harga_total`) AS `total_pendapatan`,avg(`m`.`harga_total`) AS `rata_rata_transaksi`,`mn`.`nama_menu` AS `detail`,month(`m`.`tgl_pemesanan`) AS `bulan`,year(`m`.`tgl_pemesanan`) AS `tahun` from (`memesan` `m` join `menu` `mn` on(`m`.`id_menu` = `mn`.`id_menu`)) group by `mn`.`nama_menu`,month(`m`.`tgl_pemesanan`),year(`m`.`tgl_pemesanan`) union all select concat('Kasir: ',`u`.`nama`) AS `kategori`,count(`m`.`id_pemesanan`) AS `jumlah_transaksi`,sum(`m`.`harga_total`) AS `total_pendapatan`,avg(`m`.`harga_total`) AS `rata_rata_transaksi`,`u`.`nama` AS `detail`,month(`m`.`tgl_pemesanan`) AS `bulan`,year(`m`.`tgl_pemesanan`) AS `tahun` from (`memesan` `m` join `user` `u` on(`m`.`id_user` = `u`.`id_user`)) group by `u`.`nama`,month(`m`.`tgl_pemesanan`),year(`m`.`tgl_pemesanan`) union all select concat('Metode: ',`m`.`metode_pembayaran`) AS `kategori`,count(`m`.`id_pemesanan`) AS `jumlah_transaksi`,sum(`m`.`harga_total`) AS `total_pendapatan`,avg(`m`.`harga_total`) AS `rata_rata_transaksi`,`m`.`metode_pembayaran` AS `detail`,month(`m`.`tgl_pemesanan`) AS `bulan`,year(`m`.`tgl_pemesanan`) AS `tahun` from `memesan` `m` group by `m`.`metode_pembayaran`,month(`m`.`tgl_pemesanan`),year(`m`.`tgl_pemesanan`)  ;

-- --------------------------------------------------------

--
-- Structure for view `laporan_bulanan_jan2026`
--
DROP TABLE IF EXISTS `laporan_bulanan_jan2026`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `laporan_bulanan_jan2026`  AS SELECT 'Ringkasan' AS `kategori`, count(`m`.`id_pemesanan`) AS `jumlah_transaksi`, sum(`m`.`harga_total`) AS `total_pendapatan`, avg(`m`.`harga_total`) AS `rata_rata_transaksi`, NULL AS `detail` FROM `memesan` AS `m` WHERE month(`m`.`tgl_pemesanan`) = 1 AND year(`m`.`tgl_pemesanan`) = 2026union allselect concat('Menu: ',`mn`.`nama_menu`) AS `kategori`,sum(`m`.`jumlah`) AS `jumlah_transaksi`,sum(`m`.`harga_total`) AS `total_pendapatan`,avg(`m`.`harga_total`) AS `rata_rata_transaksi`,`mn`.`nama_menu` AS `detail` from (`memesan` `m` join `menu` `mn` on(`m`.`id_menu` = `mn`.`id_menu`)) where month(`m`.`tgl_pemesanan`) = 1 and year(`m`.`tgl_pemesanan`) = 2026 group by `mn`.`nama_menu` union all select concat('Kasir: ',`u`.`nama`) AS `kategori`,count(`m`.`id_pemesanan`) AS `jumlah_transaksi`,sum(`m`.`harga_total`) AS `total_pendapatan`,avg(`m`.`harga_total`) AS `rata_rata_transaksi`,`u`.`nama` AS `detail` from (`memesan` `m` join `user` `u` on(`m`.`id_user` = `u`.`id_user`)) where month(`m`.`tgl_pemesanan`) = 1 and year(`m`.`tgl_pemesanan`) = 2026 group by `u`.`nama` union all select concat('Metode: ',`m`.`metode_pembayaran`) AS `kategori`,count(`m`.`id_pemesanan`) AS `jumlah_transaksi`,sum(`m`.`harga_total`) AS `total_pendapatan`,avg(`m`.`harga_total`) AS `rata_rata_transaksi`,`m`.`metode_pembayaran` AS `detail` from `memesan` `m` where month(`m`.`tgl_pemesanan`) = 1 and year(`m`.`tgl_pemesanan`) = 2026 group by `m`.`metode_pembayaran`  ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `memesan`
--
ALTER TABLE `memesan`
  ADD PRIMARY KEY (`id_pemesanan`),
  ADD KEY `fk_user_memesan` (`id_user`),
  ADD KEY `fk_menu_memesan` (`id_menu`);

--
-- Indexes for table `menu`
--


--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `memesan`
--
ALTER TABLE `memesan`
  MODIFY `id_pemesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `menu`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `memesan`
--
ALTER TABLE `memesan`
  ADD CONSTRAINT `fk_menu_memesan` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_memesan` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
