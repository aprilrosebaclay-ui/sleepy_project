-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 07, 2026 at 10:52 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restiq_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int NOT NULL,
  `workout` float DEFAULT NULL,
  `reading` float DEFAULT NULL,
  `phone` float DEFAULT NULL,
  `work_hours` float DEFAULT NULL,
  `caffeine` float DEFAULT NULL,
  `relaxation` float DEFAULT NULL,
  `predicted_sleep` float DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `workout`, `reading`, `phone`, `work_hours`, `caffeine`, `relaxation`, `predicted_sleep`, `created_at`) VALUES
(1, 4, 2, 1, 2, 2, 2, 0, '2026-04-30 08:19:33'),
(2, 3, 4, 1, 4, 1, 1, 0, '2026-04-30 08:19:45'),
(3, 6, 6, 8, 6, 7, 8, 0, '2026-04-30 08:20:10'),
(4, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 0, '2026-04-30 08:21:30'),
(5, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 0, '2026-04-30 08:21:40'),
(6, 5, 2, 2, 3, 1, 5, 0, '2026-04-30 08:22:50'),
(7, 2.85, 0.49, 4.22, 5.03, 206.18, 0.67, 0, '2026-04-30 08:31:32'),
(8, 2, 1, 4, 5, 2, 1, 5.0316, '2026-05-03 21:20:26'),
(9, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.5395, '2026-05-03 21:21:34'),
(10, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.54, '2026-05-03 21:22:24'),
(11, 2.85, 0.49, 4.22, 5.03, 206.18, 0.67, 4.88, '2026-05-03 21:23:23'),
(12, 2.85, 0.49, 4.22, 5.03, 206.18, 0.67, 4.87, '2026-05-03 21:26:39'),
(13, 2.85, 0.49, 4.22, 5.03, 206.18, 0.67, 4.87, '2026-05-03 21:26:45'),
(14, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.72, '2026-05-03 21:27:38'),
(15, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.38, '2026-05-03 21:32:05'),
(16, 2.85, 0.49, 4.22, 5.03, 206.18, 0.67, 4.91, '2026-05-03 21:32:49'),
(17, 2.2, 1.81, 4.04, 9.23, 28.73, 0.35, 3.84, '2026-05-03 21:33:43'),
(18, 2.2, 1.81, 4.04, 9.23, 28.73, 0.35, 3.87, '2026-05-03 21:35:56'),
(19, 2.2, 1.81, 4.04, 9.23, 28.73, 0.35, 3.87, '2026-05-03 21:36:20'),
(20, 2.2, 1.81, 4.04, 9.23, 28.73, 0.35, 3.87431, '2026-05-03 21:36:26'),
(21, 2.2, 1.81, 4.04, 9.23, 28.73, 0.35, 3.874, '2026-05-03 21:36:39'),
(22, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.578, '2026-05-03 21:37:12'),
(23, 1.1, 0.1, 4.3, 4.6, 123.15, 0.02, 4.02, '2026-05-03 21:38:48'),
(24, 1.1, 0.1, 4.3, 4.6, 123.15, 0.02, 4.02, '2026-05-03 21:39:24'),
(25, 1, 2, 3, 31, 3, 3, 4.86, '2026-05-03 21:41:27'),
(26, 1, 2, 3, 31, 3, 3, 4.86, '2026-05-03 21:41:33'),
(27, 1, 2, 3, 31, 3, 3, 4.86, '2026-05-03 21:42:31'),
(28, 4, 5, 3, 2, 1, 3, 8.88, '2026-05-03 21:42:56'),
(29, 4, 5, 3, 2, 1, 3, 8.88, '2026-05-03 21:43:17'),
(30, 3, 4, 2, 1, 1, 4, 8.28, '2026-05-03 21:43:29'),
(31, 1, 2, 3, 31, 3, 3, 4.86, '2026-05-03 21:44:26'),
(32, 1, 2, 3, 31, 3, 3, 4.86, '2026-05-03 21:47:41'),
(33, 2, 3, 1, 4, 2, 1, 8.17, '2026-05-03 22:01:55'),
(34, 3, 4, 2, 1, 3, 1, 7.6, '2026-05-03 22:13:56'),
(35, 3, 4, 1, 2, 1, 3, 8.88, '2026-05-03 22:39:15'),
(36, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.24, '2026-05-03 23:00:04'),
(37, 2.85, 0.49, 4.22, 5.03, 206.18, 0.75, 3.45, '2026-05-03 23:00:50'),
(38, 3, 2, 5, 2, 2, 4, 7.42, '2026-05-03 23:34:44'),
(39, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.51, '2026-05-03 23:35:25'),
(40, 2.85, 0.49, 4.22, 5.03, 206.18, 0.67, 5.05, '2026-05-03 23:37:02'),
(41, 2.85, 0.49, 4.22, 5.03, 206.18, 0.67, 4.88, '2026-05-03 23:58:39'),
(42, 1.12, 0.52, 3.29, 7.89, 216.08, 0.75, 3.45, '2026-05-03 23:59:20'),
(43, 3, 4, 2, 4, 3, 4, 7.56, '2026-05-04 00:02:20'),
(44, 3, 4, 2, 4, 3, 4, 7.56, '2026-05-04 00:02:25'),
(45, 0.17, 0.9, 2.44, 7.11, 226.96, 0.06, 3.62, '2026-05-04 00:03:20'),
(46, 415, 55, 5, 5, 5, 5, 6.91, '2026-05-07 09:23:20');

-- --------------------------------------------------------

--
-- Table structure for table `predictions`
--

CREATE TABLE `predictions` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `sleep_hours` float DEFAULT NULL,
  `predicted_category` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `prediction_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `b_day` date NOT NULL,
  `gender` varchar(1) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `user_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `role` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `middle_name`, `b_day`, `gender`, `email`, `user_name`, `password`, `timestamp`, `role`) VALUES
(1, 'April rose', 'Baclay', 'Jabalde', '2001-04-10', 'F', 'aprilrose.baclay@bisu.edu.ph', 'Rose', 'Rose_793289', '2026-03-19 16:45:25', 'user'),
(2, 'jocelito', 'jaganas', 'lara', '2004-10-14', 'M', 'jocelito.jaganas@bisu.edu.ph', 'jr', '$2y$10$fZoXWlqCxFpmx09jyRk6huzGoA1fgJAtsPz/hu6/67Hb2cki6tZKW', '2026-03-31 15:39:06', 'user'),
(3, 'apple', 'banana', 'oranage', '2002-12-10', 'F', 'abcd@gmail.com', 'apple', '$2y$10$AfhuWCeflYcZ9AqN9Vo15.7g/f4c90OQVYuXVZA/vbcwhDk90Ze4K', '2026-03-31 15:46:26', 'user'),
(4, 'banana', 'banananana', 'bananananananana', '2002-12-14', 'M', 'abcdefg@gmail.com', 'banana', '$2y$10$RBCjBnMwkBFeYqC1FsmG4uDNilnHNZd.5/orULqHBa5sJCvjgODA2', '2026-03-31 15:58:00', 'user'),
(5, 'grace', 'anabieza', 'anuber', '2001-04-10', 'F', 'graceanabieza@bisu.edu.ph', 'grace', '$2y$10$rUT.w.t9U17OKepjB8nTeOt8Yh1Qd3jvR96PcAsfEouvzRBqfEEi6', '2026-04-07 11:05:14', 'user'),
(6, 'anamarie', 'suhayon', 'labao', '2020-03-24', 'F', 'anamariesuhayon@gmail.com', 'anamarie', '$2y$10$H/UQKB1gKGAcpML5ebiNleJySrDpNX4M/QHjyYPb.yUri1gzdTsn6', '2026-04-24 15:24:30', 'user'),
(7, 'hendrian', 'generalao', 'jaja', '2006-07-28', 'M', 'hendriangeneralao@gmail.com', 'hendrian', '$2y$10$XxKYNMNi4Uc7odg2Buf5X.tKjlercfUmcMI.2rm82NsUebjfiTgBy', '2026-04-28 11:38:38', 'user'),
(8, 'Admin', 'System', '', '2000-01-01', 'M', 'admin@gmail.com', 'admin', '$2y$10$0lOyw0z41yR6Cl5QGiurx./ZoZBRtPbF2P5/aWASKIzFoQEzvOT16', '2026-04-28 11:47:29', 'admin'),
(9, 'crist daniel', 'cotacte', 'jabalde', '2004-03-16', 'M', 'cristdanielcotacte@gmail.com', 'daniel', '$2y$10$fl2owK2yUNidkh50zQ5jOuExMWIQ189ShZYittkZLGil6gUS1Imp6', '2026-04-28 18:26:11', 'user'),
(10, 'atasha', 'jubane', 'j', '2010-04-09', 'F', 'atasha@gmail.com', 'atasha', '$2y$10$1f2dBNrBcA/Rjq8wMe3wHePtFF8WZEOSFvHMDbnMTLhDUf8a4xcdC', '2026-04-29 00:01:11', 'user'),
(11, 'jhon', 'Doe', '', '2005-12-12', 'M', 'jhon@gmail.com', 'jhon', '$2y$10$Aa0zRlfWaf7FBYAuoUv.ru5iAkCWm7OyAuKSEtNwNY24GzO09.8qa', '2026-05-01 12:48:56', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `predictions`
--
ALTER TABLE `predictions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `predictions`
--
ALTER TABLE `predictions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `predictions`
--
ALTER TABLE `predictions`
  ADD CONSTRAINT `predictions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
