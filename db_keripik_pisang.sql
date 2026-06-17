-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2026 at 04:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_keripik_pisang`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `qty`, `created_at`, `updated_at`) VALUES
(1, 4, 3, 2, '2026-06-09 14:04:09', '2026-06-09 14:04:09'),
(2, 4, 4, 1, '2026-06-09 14:04:09', '2026-06-09 14:04:09'),
(3, 1, 5, 1, '2026-06-09 17:53:46', '2026-06-09 17:53:46'),
(4, 1, 1, 5, '2026-06-09 17:55:31', '2026-06-09 17:55:31');

-- --------------------------------------------------------

--
-- Table structure for table `foto`
--

CREATE TABLE `foto` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_foto` varchar(255) NOT NULL,
  `is_utama` tinyint(1) DEFAULT 0 COMMENT '1 jika foto profil utama, 0 jika foto tambahan',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(30) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(14,2) NOT NULL DEFAULT 0.00,
  `shipping_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `grand_total` decimal(14,2) NOT NULL DEFAULT 0.00,
  `shipping_name` varchar(100) DEFAULT NULL,
  `shipping_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `shipping_city` varchar(80) DEFAULT NULL,
  `shipping_province` varchar(80) DEFAULT NULL,
  `shipping_postal` varchar(10) DEFAULT NULL,
  `courier` varchar(50) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `status` enum('pending','paid','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `shipped_at` datetime DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `user_id`, `total_price`, `shipping_cost`, `grand_total`, `shipping_name`, `shipping_phone`, `shipping_address`, `shipping_city`, `shipping_province`, `shipping_postal`, `courier`, `payment_method`, `payment_proof`, `status`, `notes`, `admin_notes`, `paid_at`, `shipped_at`, `delivered_at`, `created_at`, `updated_at`) VALUES
(1, 'ORD-20250601-001', 2, 33000.00, 9000.00, 42000.00, 'Budi Santoso', '081298765432', 'Jl. Melati No. 5, Purwokerto Utara', 'Purwokerto', 'Jawa Tengah', '53124', 'JNE REG', 'Transfer Bank BRI', NULL, 'delivered', NULL, NULL, '2025-06-01 10:30:00', '2025-06-02 08:00:00', '2025-06-04 14:20:00', '2026-06-09 14:04:09', '2026-06-09 14:04:09'),
(2, 'ORD-20250605-002', 3, 80000.00, 12000.00, 92000.00, 'Siti Rahayu', '085711223344', 'Jl. Mawar No. 12, Sokaraja', 'Banyumas', 'Jawa Tengah', '53181', 'J&T Express', 'Transfer Bank BCA', NULL, 'shipped', NULL, NULL, '2025-06-05 09:15:00', '2025-06-06 07:30:00', NULL, '2026-06-09 14:04:09', '2026-06-09 14:04:09'),
(19, 'ORD-1781048985', 5, 25000.00, 0.00, 25000.00, 'andhika dwi', '082135887896', 'asdsdas', NULL, NULL, NULL, NULL, 'COD', NULL, 'shipped', NULL, NULL, NULL, '2026-06-10 06:58:32', NULL, '2026-06-09 23:49:45', '2026-06-09 23:58:32'),
(20, 'ORD-1781049758', 5, 400000.00, 0.00, 400000.00, 'ismail', '082135883532', 'purwokerto, banyumas', NULL, NULL, NULL, NULL, 'Transfer Bank', NULL, 'shipped', NULL, NULL, NULL, '2026-06-10 07:08:28', NULL, '2026-06-10 00:02:38', '2026-06-10 00:08:28'),
(21, 'ORD-1781050014', 6, 100000.00, 0.00, 100000.00, 'ismail', '081234567890', 'purwokerto, banyumas', NULL, NULL, NULL, NULL, 'E-Wallet', NULL, 'processing', NULL, NULL, NULL, '2026-06-10 07:08:48', '2026-06-10 07:08:43', '2026-06-10 00:06:54', '2026-06-10 00:08:58'),
(22, 'ORD-1781050532', 5, 40000.00, 0.00, 40000.00, 'andhika dwi', '082135887896', 'asdasdad', NULL, NULL, NULL, NULL, 'COD', NULL, 'processing', NULL, NULL, NULL, NULL, NULL, '2026-06-10 00:15:32', '2026-06-10 00:16:17'),
(23, 'ORD-1781700799', 5, 32000.00, 0.00, 32000.00, 'ANDHIKA DWI CAHYA PURNAMA', '082135887896', 'Jl.	Sokajati	,	Pasirmuncang	Rt	06/	Rw	04,	Purwokerto	Barat,	Banyumas, Jawa	Tengah,	Desa/Kelurahan	Pasirmuncang,	Kec.	Purwokerto	Barat, Kab.	Banyumas,	Provinsi	Jawa	Tengah', NULL, NULL, NULL, NULL, 'COD', NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-06-17 12:53:19', '2026-06-17 12:53:19');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(150) NOT NULL,
  `product_price` decimal(12,2) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `subtotal` decimal(14,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `qty`, `subtotal`, `created_at`) VALUES
(1, 1, 1, 'Keripik Pisang Original', 15000.00, 1, 15000.00, '2026-06-09 14:04:09'),
(2, 1, 2, 'Keripik Pisang Pedas Level 3', 18000.00, 1, 18000.00, '2026-06-09 14:04:09'),
(3, 2, 5, 'Keripik Pisang Paket Hemat (5 pcs)', 80000.00, 1, 80000.00, '2026-06-09 14:04:09'),
(4, 19, 6, 'Kripik pisang keju', 25000.00, 1, 25000.00, '2026-06-09 23:49:45'),
(5, 20, 5, 'sale pisang', 80000.00, 5, 400000.00, '2026-06-10 00:02:38'),
(6, 21, 6, 'Kripik pisang keju', 25000.00, 4, 100000.00, '2026-06-10 00:06:54'),
(7, 22, 4, 'keripik sanjai', 20000.00, 2, 40000.00, '2026-06-10 00:15:32'),
(8, 23, 8, 'pisang coklat lumer', 14000.00, 1, 14000.00, '2026-06-17 12:53:19'),
(9, 23, 2, 'Keripik Pisang matcha', 18000.00, 1, 18000.00, '2026-06-17 12:53:19');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(160) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `weight` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT 'no-image.png',
  `category` varchar(80) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `slug`, `description`, `price`, `stock`, `weight`, `image`, `category`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Keripik Pisang Original', 'keripik-pisang-original', 'Keripik pisang klasik dengan cita rasa gurih dan renyah. Dibuat dari pisang kepok pilihan yang dipanen langsung dari kebun lokal Banyumas. Cocok untuk camilan sehari-hari bersama keluarga.', 15000.00, 100, 250, '1781026044_Keripik Pisang Asin Gurih Original _ Kripik Pisang Renyah Tipis Super isi 500 gr.jpg', 'Original', 1, 1, '2026-06-09 14:04:09', '2026-06-09 17:27:24'),
(2, 'Keripik Pisang matcha', 'keripik-pisang-pedas-level-3', 'Sensasi manis yang enak', 18000.00, 79, 250, '1781026035_Keripik Pisang Coklat Lumer Beli 1 Gratis 1 _ Paket Hemat 2 Box Thinwil 500ml.jpg', 'Manis', 1, 1, '2026-06-09 14:04:09', '2026-06-17 12:53:19'),
(3, 'Keripik Pisang Cokelat Susu', 'keripik-pisang-cokelat-susu', 'Perpaduan sempurna keripik pisang renyah dengan lelehan cokelat susu premium. Manis, gurih, dan lumer di mulut. Pilihan favorit anak-anak dan dewasa.', 22000.00, 60, 200, '1781026008_Keripik Pisang Berlapis Cokelat, Cokelat, Tertutupi, Keripik Pisang PNG Transparan Clipart dan File PSD untuk Unduh Gratis.jpg', 'Manis', 1, 1, '2026-06-09 14:04:09', '2026-06-09 17:26:48'),
(4, 'keripik sanjai', 'keripik-pisang-keju-asin', 'Nikmati gurihnya keripik pisang dengan taburan keju cheddar asin yang melimpah. Tekstur renyah berpadu rasa keju yang kaya menjadikannya camilan tak terlupakan.', 20000.00, 73, 200, '1781025998_Keripik Sanjai_ Sumatra snack of fried, sliced cassava with chilli and sugar_.jpg', 'Pedas', 1, 1, '2026-06-09 14:04:09', '2026-06-10 00:15:32'),
(5, 'sale pisang', 'keripik-pisang-paket-hemat-5pcs', 'sale pisang terenak', 80000.00, 35, 1250, '1781025955_Sale Pisang Kering 250gr.jpg', 'Manis', 1, 1, '2026-06-09 14:04:09', '2026-06-10 00:02:38'),
(6, 'Kripik pisang keju', 'kripik-pisang-keju', 'enak bet wok', 25000.00, 95, 250, '1781026402_Plantain Chips Recipe – Baked Plantain Chips Recipe.jpg', 'Manis', 1, 1, '2026-06-09 17:33:22', '2026-06-10 00:06:54'),
(7, 'Pisang matcha lumer', 'pisang-matcha-lumer', 'enak', 15000.00, 100, 250, '1781050795_unduhan (1).jpg', 'Manis', 1, 1, '2026-06-10 00:19:55', '2026-06-10 00:19:55'),
(8, 'pisang coklat lumer', 'pisang-coklat-lumer', 'enak', 14000.00, 99, 250, '1781052080_unduhan.jpg', 'Manis', 1, 1, '2026-06-10 00:41:20', '2026-06-17 12:53:19');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5,
  `comment` text DEFAULT NULL,
  `is_visible` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `user_id`, `product_id`, `order_id`, `rating`, `comment`, `is_visible`, `created_at`, `updated_at`) VALUES
(1, 2, 1, 1, 5, 'Renyahnya mantap banget! Pisangnya terasa banget, tidak terlalu asin. Packaging juga rapih dan aman sampai tujuan. Pasti beli lagi!', 1, '2026-06-09 14:04:09', '2026-06-09 14:04:09'),
(2, 2, 2, 1, 4, 'Pedasnya pas di level 3, tidak terlalu ekstrem tapi sudah bikin nagih. Cocok buat yang suka pedas sedang. Recommended!', 1, '2026-06-09 14:04:09', '2026-06-09 14:04:09');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `avatar` varchar(255) DEFAULT 'default.png',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`, `avatar`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin Keripik Pisang', 'admin@gmail.com', '$2y$10$1ZkX9OeAFx.bt9Np58uOTe3Iqw4cRFiSutJNz0rInAXsQtyrtQ5.a', '081234567890', 'Jl. Raya Purwokerto No. 1, Purwokerto, Jawa Tengah', 'admin', 'default.png', 1, '2026-06-09 14:04:09', '2026-06-09 16:56:49'),
(2, 'Budi Santoso', 'budi@example.com', '$2y$10$WqB40fGz5sE5j/7i0OQy0.Z62M1n/d5nQ4uK/0lG5yR7M.H9M0xL6', '081298765432', 'Jl. Melati No. 5, Purwokerto Utara, Banyumas', 'user', 'default.png', 1, '2026-06-09 14:04:09', '2026-06-09 16:14:49'),
(3, 'Siti Rahayu', 'siti@example.com', '$2y$10$WqB40fGz5sE5j/7i0OQy0.Z62M1n/d5nQ4uK/0lG5yR7M.H9M0xL6', '085711223344', 'Jl. Mawar No. 12, Sokaraja, Banyumas', 'user', 'default.png', 1, '2026-06-09 14:04:09', '2026-06-09 16:14:49'),
(4, 'Ahmad Fauzi', 'ahmad@example.com', '$2y$10$WqB40fGz5sE5j/7i0OQy0.Z62M1n/d5nQ4uK/0lG5yR7M.H9M0xL6', '082233445566', 'Jl. Kenanga No. 8, Purwokerto Selatan, Banyumas', 'user', 'default.png', 1, '2026-06-09 14:04:09', '2026-06-09 16:14:49'),
(5, 'andhika', 'andhika@gmail.com', '$2y$10$8ZrHoEuFshvi94fBHUCT3.zQfuuDWX4fBKiAiND6iQKk/C1EHLFb6', '082135887896', NULL, 'user', 'default.png', 1, '2026-06-09 17:04:44', '2026-06-09 17:04:44'),
(6, 'ismail', 'ismail@gmail.com', '$2y$10$TvCs2IRHBj0Nj5N2ZlJDSOj7UnKXUSzQNNvJKHNIlHg4yhWu7OE.G', '081234567890', NULL, 'user', 'default.png', 1, '2026-06-10 00:05:50', '2026-06-10 00:05:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_user_product` (`user_id`,`product_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Indexes for table `foto`
--
ALTER TABLE `foto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_foto_user` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_orders_code` (`order_code`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_od_order` (`order_id`),
  ADD KEY `fk_od_product` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_products_slug` (`slug`),
  ADD KEY `fk_products_created_by` (`created_by`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_reviews_user_product_order` (`user_id`,`product_id`,`order_id`),
  ADD KEY `fk_reviews_product` (`product_id`),
  ADD KEY `fk_reviews_order` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `foto`
--
ALTER TABLE `foto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `foto`
--
ALTER TABLE `foto`
  ADD CONSTRAINT `fk_foto_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `fk_od_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_od_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
