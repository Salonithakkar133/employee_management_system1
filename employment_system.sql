-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2025 at 08:25 AM
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
-- Database: `employment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `description`, `status`, `start_date`, `end_date`, `assigned_to`, `created_by`, `created_at`, `is_deleted`) VALUES
(11, 'project', 'project', 'in_progress', '2025-06-26', '2025-07-25', 72, 7, '2025-06-24 12:48:12', 0),
(12, 'project', 'project', 'pending', NULL, NULL, NULL, 7, '2025-06-24 14:20:26', 1),
(13, 'project', 'project', 'in_progress', NULL, NULL, 27, 7, '2025-06-25 05:48:35', 1),
(14, 'p', 'p', 'pending', '2025-06-25', '2025-06-24', NULL, 7, '2025-06-25 13:22:12', 1),
(15, 'p', 'p', 'pending', '2025-06-25', '2025-06-26', NULL, 7, '2025-06-25 13:43:21', 0),
(20, 'ppp', 'ppp', 'pending', '2025-06-30', '2025-07-02', 72, NULL, '2025-06-27 11:28:30', 0),
(21, '', NULL, 'pending', NULL, NULL, NULL, NULL, '2025-06-27 11:29:02', 1),
(23, 'parth', 'parth', 'pending', '2025-06-26', '2025-06-26', 27, 7, '2025-06-27 12:12:50', 1),
(37, '', '', '', '2025-06-30', '2025-06-30', NULL, 0, '2025-06-30 06:56:29', 1),
(38, '', '', '', '2025-06-30', '2025-06-30', NULL, 0, '2025-06-30 07:06:27', 1),
(39, 'ppp', '123', 'in_progress', '2025-06-30', '2025-06-30', 72, 0, '2025-06-30 07:07:56', 1),
(40, 'pppp', 'pppp', 'in_progress', '2025-06-30', '2025-07-09', 69, 7, '2025-06-30 07:36:04', 1),
(41, '111112', '1111', 'completed', '2025-06-30', '2025-07-11', 71, 7, '2025-06-30 09:41:51', 0),
(42, '555', '555', 'pending', '2025-06-30', '2025-06-30', NULL, 28, '2025-06-30 11:08:00', 1),
(43, 'project123', 'description in detail', 'pending', '2025-06-30', '2025-06-30', 27, 7, '2025-06-30 11:34:21', 0),
(44, 'saloni', 'to my teacher', 'in_progress', '2025-06-30', '2025-06-30', 27, 28, '2025-06-30 11:44:13', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pending','employee','team_leader','admin') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `profile_image`, `is_deleted`) VALUES
(7, 'admin', 'admin@gmail.com', '$2y$10$YJ/OOAJ65ULmYKzMypt/SejEAA8p./DpveqP2iksDT5BVKSzD.50G', 'admin', '2025-06-18 12:14:57', '1751305129_1750765849_WhatsApp Image 2025-06-12 at 9.48.39 PM.jpeg', 0),
(27, 'saloni', 'saloni@gmail.com', '$2y$10$NTQ89.EpLTYKlmRV9FUSIOeLypfCavgcOn3cWDKMMGD2aNoaFfk0a', 'employee', '2025-06-19 12:32:40', '\r\n', 0),
(28, 'parth', 'parth@gmail.com', '$2y$10$iWi7sNCeFWC.OGyMcyIpuO2elMmgxMD/gZDjG6iOSXRmuBXSy..M2', 'team_leader', '2025-06-20 05:41:20', '1751305238_1750743010_diet flyer.png', 0),
(53, 'pragati', 'p@gmail', '$2y$10$CStbCpcqf090DhGlsKk5uO647RHTe7yRvvNveEE.c9ZCExrZScZ46', 'employee', '2025-06-20 12:28:04', NULL, 0),
(63, 'divyamam', 'd@gmail.com', '$2y$10$ZcQ9H64HsfPtou.MCkrXPe9W0hKRwxdOxnQ/PpJHWdmIJ8lkO1pYy', 'pending', '2025-06-25 09:37:22', NULL, 0),
(66, 'saloni12', 'admin22@gmail.com', '$2y$10$79FDyCqQ9sehFrNM7tKpq.ilxhCNZ3QQtK.HgmcgsEzkTwlRkdKZu', 'employee', '2025-06-25 10:34:16', NULL, 0),
(67, 's', 's@gmail.com', '$2y$10$GoTbyww9IVx2C20sF3W3g.Q8ZuvrWO4ZXIMg2ekn.OwB8AGRWW44i', 'pending', '2025-06-25 10:35:06', NULL, 0),
(69, 'abc', 'a@gmail.com', '$2y$10$89F/v1sDFP09K03e5ZDpZuJGOhqnh18a53mwEWwITt1vH/KFFHjZy', 'pending', '2025-06-25 11:07:21', NULL, 0),
(70, 'saloni', 'sal@gmail.com', '$2y$10$bwYLFMODqtywRrwwX8EGGOyQFQoVtaGRcFW6UZyH.GG3EwM3D3zlS', 'pending', '2025-06-25 12:23:52', NULL, 0),
(71, 'pa', 'h@gmail.com', '$2y$10$IQegFQpMUiqVnvxkPjxMreS8B4JD4FpYAYnWPykBE6EACQ99PmkAW', 'pending', '2025-06-25 12:32:08', NULL, 0),
(72, 'meet', 'm@gmail.com', '$2y$10$9p19n/vv0AhQ0ZxIPmL5SObVc3wkRvnSEb7fRlHoS3ZKKWGmdhD82', 'pending', '2025-06-25 12:37:09', NULL, 0),
(73, '', '', '$2y$10$ii/R6XSVEm3iLk1ful6bjOjIktN14wR6GJgVAE/rKAbFzOw3Zmg9q', '', '2025-06-27 10:04:14', NULL, 0),
(104, 'khushi', 'k@gmail.com', '$2y$10$kwl5VhPmR9u6WWbAfuvUCO8mZ6WuwUBCuFyzYfeBMn7B5vHWI/hHG', 'pending', '2025-07-01 06:03:55', NULL, 0),
(106, 'khushi', 'khushi@gmail.com', '$2y$10$lsnavOSPX/nD6ZwAKCXu..HBE.WONxBGmsLgZGQ1PNCt6zgUzx5m6', 'pending', '2025-07-01 06:05:19', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_to` (`assigned_to`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
