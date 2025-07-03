-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 04:45 PM
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
(11, 'project', 'project12', '', '2025-06-26', '2025-07-25', 72, 7, '2025-06-24 12:48:12', 0),
(12, 'project33333333', 'project', '', '2025-07-02', '2025-07-02', NULL, 7, '2025-06-24 14:20:26', 0),
(13, 'project', 'project', 'in_progress', NULL, NULL, 27, 7, '2025-06-25 05:48:35', 0),
(14, 'p', 'p', 'pending', '2025-06-25', '2025-06-24', NULL, 7, '2025-06-25 13:22:12', 0),
(15, 'pwife', 'p', '', '2025-07-02', '2025-07-02', NULL, 7, '2025-06-25 13:43:21', 0),
(20, 'ppp', 'ppp', 'pending', '2025-06-30', '2025-07-02', 72, NULL, '2025-06-27 11:28:30', 1),
(21, '', NULL, 'pending', NULL, NULL, NULL, NULL, '2025-06-27 11:29:02', 1),
(23, 'parth', 'parth', 'pending', '2025-06-26', '2025-06-26', 27, 7, '2025-06-27 12:12:50', 1),
(37, '', '', '', '2025-06-30', '2025-06-30', NULL, 0, '2025-06-30 06:56:29', 1),
(38, '', '', '', '2025-06-30', '2025-06-30', NULL, 0, '2025-06-30 07:06:27', 1),
(39, 'ppp', '123', 'in_progress', '2025-06-30', '2025-06-30', 72, 0, '2025-06-30 07:07:56', 1),
(40, 'pppp', 'pppp', 'in_progress', '2025-06-30', '2025-07-09', NULL, 7, '2025-06-30 07:36:04', 1),
(41, '111112', '1111', 'completed', '2025-06-30', '2025-07-11', 71, 7, '2025-06-30 09:41:51', 0),
(42, '555', '555', 'pending', '2025-06-30', '2025-06-30', NULL, 28, '2025-06-30 11:08:00', 0),
(43, 'project123', 'description in detail', 'pending', '2025-06-30', '2025-06-30', 27, 7, '2025-06-30 11:34:21', 0),
(44, 'saloni', 'to my teacher', 'in_progress', '2025-06-30', '2025-06-30', 27, 28, '2025-06-30 11:44:13', 0),
(45, 'project khushi', 'khushi', 'pending', '2025-07-01', '2025-07-02', NULL, 28, '2025-07-01 10:28:48', 0),
(46, '123', 'pppppp', 'pending', '2025-07-02', '2025-07-03', NULL, 7, '2025-07-02 10:59:10', 0),
(47, '555', '555', 'pending', '2025-07-03', '2025-07-04', NULL, 7, '2025-07-02 11:14:32', 0),
(48, 'q', 'q', 'pending', '2025-07-02', '2025-07-02', NULL, 7, '2025-07-02 15:22:59', 0),
(49, 'w', 'w', 'pending', '2025-07-03', '2025-07-10', NULL, 7, '2025-07-02 15:23:18', 0),
(50, 'jjjjjjjjj', 'kkkkkkkk', 'pending', '2025-07-02', '2025-07-02', NULL, 7, '2025-07-02 16:14:30', 0),
(51, 'pppp', '11', 'pending', '2025-07-03', '2025-07-03', NULL, 7, '2025-07-03 10:14:55', 0),
(52, '11', '11', 'pending', '2025-07-03', '2025-07-03', NULL, 7, '2025-07-03 10:15:13', 0);

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
(7, 'admin12', 'admin@gmail.com', '$2y$10$YJ/OOAJ65ULmYKzMypt/SejEAA8p./DpveqP2iksDT5BVKSzD.50G', 'admin', '2025-06-18 12:14:57', '1751473704_crustopia.png', 0),
(27, 'saloni123', 'saloni@gmail.com', '$2y$10$NTQ89.EpLTYKlmRV9FUSIOeLypfCavgcOn3cWDKMMGD2aNoaFfk0a', 'employee', '2025-06-19 12:32:40', '\r\n', 0),
(28, 'parth', 'parth@gmail.com', '$2y$10$iWi7sNCeFWC.OGyMcyIpuO2elMmgxMD/gZDjG6iOSXRmuBXSy..M2', 'team_leader', '2025-06-20 05:41:20', '1751365643_sound.png', 1),
(53, 'pragati', 'p@gmail', '$2y$10$CStbCpcqf090DhGlsKk5uO647RHTe7yRvvNveEE.c9ZCExrZScZ46', 'pending', '2025-06-20 12:28:04', NULL, 1),
(70, 'saloni', 'sal@gmail.com', '$2y$10$bwYLFMODqtywRrwwX8EGGOyQFQoVtaGRcFW6UZyH.GG3EwM3D3zlS', 'pending', '2025-06-25 12:23:52', NULL, 0),
(71, 'pa', 'h@gmail.com', '$2y$10$IQegFQpMUiqVnvxkPjxMreS8B4JD4FpYAYnWPykBE6EACQ99PmkAW', 'pending', '2025-06-25 12:32:08', NULL, 0),
(72, 'meet', 'm@gmail.com', '$2y$10$9p19n/vv0AhQ0ZxIPmL5SObVc3wkRvnSEb7fRlHoS3ZKKWGmdhD82', 'employee', '2025-06-25 12:37:09', NULL, 0),
(104, 'khushi', 'k2@gmail.com', '$2y$10$kwl5VhPmR9u6WWbAfuvUCO8mZ6WuwUBCuFyzYfeBMn7B5vHWI/hHG', 'employee', '2025-07-01 06:03:55', NULL, 0),
(155, 'jjjj', 'j@gmail.com', '$2y$10$Cm9LFfDGkXPyaX83yGtTXeX/5oXTklvupahRVm9i1lW3evrG3boRO', 'pending', '2025-07-03 14:44:45', NULL, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

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
