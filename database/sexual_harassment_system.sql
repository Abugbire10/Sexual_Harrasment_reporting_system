-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1: 3308
-- Generation Time: Sep 02, 2024 at 11:46 AM
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
-- Database: `sexual_harassment_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `administrator`
--

CREATE TABLE `administrator` (
  `username` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `administrator`
--

INSERT INTO `administrator` (`username`, `password_hash`, `staff_id`, `admin_id`, `email`, `created_at`) VALUES
('CostiB10', '$2y$10$EDBUkx/5d4TIVou059KOJu7OcfK8F6bSdBIIEfm2SyUU/R.e3E5Xu', 10000, 2, 'josephabugbire10@gmail.com', '2024-08-15 18:12:19'),
('Suraj', '$2y$10$Y955BxS5U3bNP52Z4E0RqukpKtYTtZyDn3j2IxHfbojMBVxI1aeGO', 10002, 5, 'mshub1010@gmail.com', '2024-08-16 14:57:34');

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `report_id` int(11) NOT NULL,
  `report_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `type` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `evidence1` varchar(255) DEFAULT NULL,
  `evidence2` varchar(255) DEFAULT NULL,
  `evidence3` varchar(255) DEFAULT NULL,
  `evidence4` varchar(255) DEFAULT NULL,
  `evidence5` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `department` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` enum('submitted','under review','resolved') DEFAULT NULL,
  `anonymous` tinyint(1) DEFAULT 0,
  `user_id` int(11) DEFAULT NULL,
  `admin_evidence` varchar(255) DEFAULT NULL,
  `Admin_Feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reports`
--

INSERT INTO `reports` (`report_id`, `report_date`, `type`, `description`, `evidence1`, `evidence2`, `evidence3`, `evidence4`, `evidence5`, `email`, `department`, `phone`, `name`, `status`, `anonymous`, `user_id`, `admin_evidence`, `Admin_Feedback`) VALUES
(67, '2024-08-16 06:22:12', 'harassment', 'Sexual Harrassment', NULL, NULL, NULL, NULL, NULL, '', '', '', '', 'under review', 1, 2, 'uploads/66bef03b1a98c_WhatsApp Image 2024-08-07 at 13.55.01_3178755b.jpg,', 'received'),
(68, '2024-08-16 15:09:26', 'violence', 'Rape', NULL, NULL, NULL, NULL, NULL, '', '', '', '', 'resolved', 1, 2, NULL, 'good the isssue is solved\r\n'),
(69, '2024-08-16 16:45:54', 'harassment', 'fhyjjti,kyuj hmgmutim hmf jmn', 'uploads/713dgE1NB6L.png', NULL, NULL, NULL, NULL, '', '', '', 'fggf', 'submitted', 0, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `created_at`, `username`) VALUES
(1, 'costinobruno@gmail.com', '$2y$10$.RlSiIeJX2RixPL7/65vZu4ce11zogtXU5CcoAd5UcrMjVoFF0rUy', '2024-08-09 13:23:12', 'Costino'),
(2, 'palatabletaste99@gmail.com', '$2y$10$swvrqrYJRUujLvgzJGY77.O9/.Iq/E7OwqrhfOPzXioxUQxIvJ4kC', '2024-08-09 16:28:38', 'Derrick'),
(4, 'mshub1010@gmail.com', '$2y$10$fXUYstLGQQMhjUJSq42t/eAh87xJtIgqogO3Gr4IB4OpzZkzhBB6O', '2024-08-12 11:03:27', 'cashes');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `administrator`
--
ALTER TABLE `administrator`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `staff_id` (`staff_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `administrator`
--
ALTER TABLE `administrator`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `reports`
--
ALTER TABLE `reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
