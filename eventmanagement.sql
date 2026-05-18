-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 25, 2025 at 10:34 AM
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
-- Database: `eventmanagement`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(47, 'aru226', '$2y$10$tlvEYuw14K1XPyz7EJVTuO05PBxp/mIUJoxUN4WdlUSZaxL7dguoS');

-- --------------------------------------------------------

--
-- Table structure for table `clientlogin`
--

CREATE TABLE `clientlogin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clientlogin`
--

INSERT INTO `clientlogin` (`id`, `username`, `password`, `status`, `created_at`) VALUES
(1, 'johnsmith', '$2y$10$EwN7nXc4.sQ.TIUY8tf/Z.VMI8SrfXUMp7xFTyRXLo4F0HFhEMCq2', 'approved', '2025-05-20 07:19:35'),
(2, 'janedoe', '$2y$10$d3hN7Xc5.uN8TIUY7xf/Z.VMI8SrfXUMp7xFTyRXLu5F1HXpEMCq3', 'pending', '2025-05-20 07:19:35');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `business_type` varchar(100) NOT NULL,
  `website` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `aadhar_file` varchar(255) NOT NULL,
  `pan_file` varchar(255) NOT NULL,
  `photo_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `full_name`, `username`, `email`, `mobile`, `password`, `company_name`, `business_type`, `website`, `city`, `state`, `address`, `aadhar_file`, `pan_file`, `photo_file`, `created_at`) VALUES
(1, 'John Smith', 'johnsmith', 'john@example.com', '1234567890', '$2y$10$EwN7nXc4.sQ.TIUY8tf/Z.VMI8SrfXUMp7xFTyRXLo4F0HFhEMCq2', 'Tech Innovators', 'Software', 'https://techinnovators.com', 'San Francisco', 'CA', '123 Market St', 'uploads/aadhar.jpg', 'uploads/pan.jpg', 'uploads/photo.jpg', '2025-05-20 07:18:24'),
(2, 'MIRIYAMPALLI ARAVIND', 'aru123', 'aru@gmail.com', '9951489478', '$2y$10$e1zqQqPIaPdmTRjSGKG/ruJEmPV6Xnq0yYzvKvw9iMeVV4TYMXuUK', 'gvkbhnjmk', 'ghujk', 'https://in.linkedin.com/in/aravind-miriyampalli', 'Pullalacheruvu', 'Andhra Pradesh', 'Near Forest Bangla\r\n8-95', 'uploads/CASE A (FIG 1).png', 'uploads/CASE A (FIG 2).png', 'uploads/CASE A VALUES.png', '2025-05-20 07:24:40'),
(3, 'SHAFI', 'shafi16', 'shaikshafi6288@gmail.com', '7207030522', '$2y$10$Lt604PMnfTn4QhhyhQov6.IiTNZgFt558xIZ/4xAFhUKHoV44zcTK', 'SHAFI PVT', 'Food Supplier', 'https://in.linkedin.com/in/aravind-miriyampalli', 'RAJKOT', 'Andhra pradesh', 'Near Forest Bangla,pullalacheruvu\r\nPullalacheruvu Mandal,prakasam district', 'uploads/apple.jpg', 'uploads/apple.jpg', 'uploads/aru.jpg', '2025-05-23 09:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `venue` varchar(255) NOT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `speaker` varchar(255) DEFAULT NULL,
  `event_description` text NOT NULL,
  `event_category` varchar(100) NOT NULL,
  `other_category` varchar(100) DEFAULT NULL,
  `organizer_phone` varchar(15) NOT NULL,
  `event_poster` varchar(255) NOT NULL,
  `total_seats` int(11) NOT NULL,
  `max_seats` int(11) NOT NULL,
  `waitlist` tinyint(1) DEFAULT 0,
  `terms` tinyint(1) DEFAULT 0,
  `date_signed` date NOT NULL,
  `payment_status` varchar(10) NOT NULL DEFAULT 'unpaid',
  `event_price` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `event_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_name`, `venue`, `event_date`, `start_time`, `end_time`, `speaker`, `event_description`, `event_category`, `other_category`, `organizer_phone`, `event_poster`, `total_seats`, `max_seats`, `waitlist`, `terms`, `date_signed`, `payment_status`, `event_price`, `created_at`, `event_status`) VALUES
(9, 'COLLAGE FEST', 'MU', '2025-05-23', '03:35:00', '08:55:00', 'SHAFI', 'The last day of the collage just chill', 'Music', '', '9951489478', 'uploads/683047957fc0d_aru.jpg', 100, 1, 1, 1, '2025-05-23', 'unpaid', 0.00, '2025-05-23 10:01:57', 'approved'),
(10, 'sports event', 'marwadi', '2025-05-23', '12:00:00', '03:00:00', 'parmar sir', 'cdfghn', 'Education', '', '9951489478', 'uploads/683052baf38cd_aru.jpg', 200, 1, 1, 1, '2025-05-23', 'paid', 20.00, '2025-05-23 10:49:30', 'approved');

-- --------------------------------------------------------

--
-- Table structure for table `event_bookings`
--

CREATE TABLE `event_bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `event_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mobile` varchar(15) NOT NULL,
  `notes` text DEFAULT NULL,
  `tickets` int(11) NOT NULL DEFAULT 1,
  `total_amount` decimal(10,2) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_bookings`
--

INSERT INTO `event_bookings` (`id`, `event_id`, `full_name`, `email`, `mobile`, `notes`, `tickets`, `total_amount`, `transaction_id`, `payment_proof`, `booking_date`) VALUES
(100004, 9, 'Shaik Shafi', 'shafi@gmail.com', '9951489478', 'pnfdljmk', 10, 0.00, '429187714244', 'uploads/proof_6830488e8966b_aru.jpg', '2025-05-23 10:06:06');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`id`, `username`, `login_time`) VALUES
(3, 'mani226', '2025-05-22 20:23:50'),
(4, 'mani226', '2025-05-22 20:24:56'),
(5, 'mani226', '2025-05-22 20:28:38'),
(6, 'mani226', '2025-05-22 20:30:01'),
(7, 'mani226', '2025-05-23 04:09:34'),
(8, 'mani226', '2025-05-23 09:32:49'),
(9, 'mani226', '2025-05-23 10:04:19'),
(10, 'mani226', '2025-05-23 10:51:10'),
(11, 'mani226', '2025-05-23 10:55:15'),
(12, 'mani226', '2025-05-23 10:55:35');

-- --------------------------------------------------------

--
-- Table structure for table `signup`
--

CREATE TABLE `signup` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `mobile_number` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `signup`
--

INSERT INTO `signup` (`id`, `full_name`, `username`, `email`, `password`, `mobile_number`, `created_at`) VALUES
(1, 'John Doe', 'johndoe', 'johndoe@example.com', '$2y$10$EwN7nXc4.sQ.TIUY8tf/Z.VMI8SrfXUMp7xFTyRXLo4F0HFhEMCq2', '1234567890', '2025-05-20 07:12:37'),
(3, 'Miriyampalli Aravind', 'mani226', 'qwer@gmail.com', '$2y$10$I.YmuRt0BQczgw51AcIZ3.XETU4firb1h8dCCa1bb5IzyRqgHUFCK', '9951489478', '2025-05-22 20:23:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `clientlogin`
--
ALTER TABLE `clientlogin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `signup`
--
ALTER TABLE `signup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `clientlogin`
--
ALTER TABLE `clientlogin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `event_bookings`
--
ALTER TABLE `event_bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100005;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `signup`
--
ALTER TABLE `signup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_bookings`
--
ALTER TABLE `event_bookings`
  ADD CONSTRAINT `event_bookings_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
