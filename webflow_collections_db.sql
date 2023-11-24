-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 24, 2023 at 06:12 PM
-- Server version: 8.0.35-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `webflow_collections_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_users`
--

CREATE TABLE `app_users` (
  `id` int NOT NULL,
  `parent_user_id` int DEFAULT NULL,
  `first_name` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `access_token` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cached_results`
--

CREATE TABLE `cached_results` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `site_id` text COLLATE utf8mb4_general_ci,
  `collection_id` text COLLATE utf8mb4_general_ci,
  `cache_token` text COLLATE utf8mb4_general_ci,
  `response` longtext COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `collection_aspect_ratio`
--

CREATE TABLE `collection_aspect_ratio` (
  `id` int NOT NULL,
  `site_id` text COLLATE utf8mb4_general_ci,
  `collection_id` text COLLATE utf8mb4_general_ci,
  `width` int DEFAULT NULL,
  `height` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_collections`
--

CREATE TABLE `user_collections` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `collection_id` text COLLATE utf8mb4_general_ci,
  `selected_collections_json` longtext COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_sites`
--

CREATE TABLE `user_sites` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `sites_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_users`
--
ALTER TABLE `app_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_parent_user_id` (`parent_user_id`);

--
-- Indexes for table `cached_results`
--
ALTER TABLE `cached_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `collection_aspect_ratio`
--
ALTER TABLE `collection_aspect_ratio`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_collections`
--
ALTER TABLE `user_collections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_sites`
--
ALTER TABLE `user_sites`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_users`
--
ALTER TABLE `app_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cached_results`
--
ALTER TABLE `cached_results`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `collection_aspect_ratio`
--
ALTER TABLE `collection_aspect_ratio`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_collections`
--
ALTER TABLE `user_collections`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_sites`
--
ALTER TABLE `user_sites`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `app_users`
--
ALTER TABLE `app_users`
  ADD CONSTRAINT `fk_users_parent_user_id` FOREIGN KEY (`parent_user_id`) REFERENCES `app_users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
