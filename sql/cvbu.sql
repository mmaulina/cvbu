-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 31, 2026 at 11:56 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cvbu`
--

-- --------------------------------------------------------

--
-- Table structure for table `lampiran`
--

CREATE TABLE `lampiran` (
  `id` int NOT NULL,
  `judul` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lampiran_diameter`
--

CREATE TABLE `lampiran_diameter` (
  `id` int NOT NULL,
  `keterangan_id` int DEFAULT NULL,
  `diameter` enum('15-20','21-30','31-50','>50') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lampiran_foto`
--

CREATE TABLE `lampiran_foto` (
  `id` int NOT NULL,
  `diameter_id` int DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lampiran_keterangan`
--

CREATE TABLE `lampiran_keterangan` (
  `id` int NOT NULL,
  `lampiran_id` int DEFAULT NULL,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lampiran`
--
ALTER TABLE `lampiran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `lampiran_diameter`
--
ALTER TABLE `lampiran_diameter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `keterangan_id` (`keterangan_id`);

--
-- Indexes for table `lampiran_foto`
--
ALTER TABLE `lampiran_foto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diameter_id` (`diameter_id`);

--
-- Indexes for table `lampiran_keterangan`
--
ALTER TABLE `lampiran_keterangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lampiran_id` (`lampiran_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lampiran`
--
ALTER TABLE `lampiran`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `lampiran_diameter`
--
ALTER TABLE `lampiran_diameter`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `lampiran_foto`
--
ALTER TABLE `lampiran_foto`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1026;

--
-- AUTO_INCREMENT for table `lampiran_keterangan`
--
ALTER TABLE `lampiran_keterangan`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lampiran_diameter`
--
ALTER TABLE `lampiran_diameter`
  ADD CONSTRAINT `lampiran_diameter_ibfk_1` FOREIGN KEY (`keterangan_id`) REFERENCES `lampiran_keterangan` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lampiran_foto`
--
ALTER TABLE `lampiran_foto`
  ADD CONSTRAINT `lampiran_foto_ibfk_1` FOREIGN KEY (`diameter_id`) REFERENCES `lampiran_diameter` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lampiran_keterangan`
--
ALTER TABLE `lampiran_keterangan`
  ADD CONSTRAINT `lampiran_keterangan_ibfk_1` FOREIGN KEY (`lampiran_id`) REFERENCES `lampiran` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
