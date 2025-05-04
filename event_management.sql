-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 12, 2025 at 08:08 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `description`, `created_at`) VALUES
(1, 'Hello', '2025-04-08 10:14:25'),
(2, 'This is test again.', '2025-04-08 10:15:32');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `start_date` varchar(255) DEFAULT NULL,
  `end_date` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT NULL,
  `assigned_users` varchar(255) DEFAULT NULL
,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_role` enum('admin','user') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_name` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','super_user','user') NOT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(255) DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
,
  PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `email_verified`, `created_at`) VALUES
(1, 'Pragati Kuinkel', 'pragatikuinkel59@gmail.com', '9828854305', '$2y$10$u8Qgo8B6lDD/uGzPDmaIw.tHEROv/LPSA2OL9kVb7/cO.mIL8xV7K', 'admin', 1, '2025-04-03 17:39:03'),
(5, 'Pratyush Kuinkel', 'pratyushkuinkel@gmail.com', '9898899898', '$2y$10$BsPyVydwBi/gQcXQGa/zCOzkoRaThEiIL4d5wnIlB.k52fA0kEE3K', 'super_user', 1, '2025-04-04 04:24:54'),
(6, 'Sunita Kuinkel', 'sunitakuinkel@gmail.com', '9841872755', '$2y$10$7PbH6Mggf5cKqW.48qHvd.S171gOHVEsjzIzqwwiLgcFREMn0S5NK', 'user', 1, '2025-04-04 11:42:59'),
(7, 'Bhuntey Kuinkel', 'bhuntey@gmail.com', '9800000000', '$2y$10$6Xcp6nQhQyaSbBti9Hmu6urWzVgkBneiZuTtNMQ4cYaQF9UiWgFOW', 'user', 1, '2025-04-08 01:16:14'),
(8, 'Rabi Kuinkel', 'rabikuinkel@gmail.com', '9841591111', '$2y$10$crNj8xqQh6S.66yJEXwnqe7fuqBx4rU6bwEdrF1.vSUrbOTlWQ3MG', 'super_user', 1, '2025-04-08 09:32:44'),
(9, 'Manisha Kuinkel', 'manishakuinkel@gmail.com', '9801000000', '$2y$10$aTEknw7iJxWN8PIjCzmyGeyD6qEcMWBzJAjWQPsV4be8YxPil.xCG', 'user', 1, '2025-04-08 10:12:21');

-- --------------------------------------------------------

--
-- Table structure for table `verification_tokens`
--

CREATE TABLE `verification_tokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_type` enum('email_verification','password_reset') NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `used_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `token` (`token`),
  KEY `expires_at` (`expires_at`),
  CONSTRAINT `verification_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deleted_users`
--

CREATE TABLE `deleted_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `role` enum('admin','super_user','user') NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
    `id` INT PRIMARY KEY AUTO_INCREMENT,
    `vendor_id` INT NOT NULL,
    `event_id` INT NOT NULL,
    `description` TEXT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `file_path` VARCHAR(255) NOT NULL,
    `status` ENUM('pending', 'accepted', 'rejected', 'paid') DEFAULT 'pending',
    `created_at` DATETIME NOT NULL,
    FOREIGN KEY (`vendor_id`) REFERENCES `users`(`id`),
    FOREIGN KEY (`event_id`) REFERENCES `events`(`id`)
);

--
-- Table structure for table `payments`
--

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    bill_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    reference_number VARCHAR(100) NOT NULL,
    payment_date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    FOREIGN KEY (bill_id) REFERENCES bills(id)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`);

--
-- Indexes for table `deleted_users`
--
ALTER TABLE `deleted_users`
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_deleted_at` (`deleted_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

