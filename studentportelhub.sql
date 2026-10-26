-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2026 at 11:19 AM
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
-- Database: `studentportelhub`
--

-- --------------------------------------------------------

--
-- Table structure for table `api_students`
--

CREATE TABLE `api_students` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `course` varchar(100) NOT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `api_students`
--

INSERT INTO `api_students` (`id`, `name`, `email`, `course`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Parth Kaklotar', 'parthkaklotar544@gmail.com', 'Computer Science', 'Active', '2026-10-22 07:10:23', '2026-10-22 07:10:23'),
(2, 'vraj patel', 'vraj@example.com', 'Information Technology', 'Inactive', '2026-10-22 07:10:59', '2026-10-22 07:14:01'),
(4, 'vraj italiya', 'vrajp@example.com', 'Computer Science', 'Active', '2026-10-22 07:23:06', '2026-10-22 07:23:06'),
(5, 'manas gamdha', 'manas@example.com', 'Information Technology', 'Active', '2026-10-22 07:25:09', '2026-10-22 07:25:09');

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(150) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'Event created', 'Event ID: 3; Name: Run for Unity', '::1', '2026-10-22 08:01:10'),
(2, 1, 'Event archived', 'Event ID: 3; Name: Run for Unity', '::1', '2026-10-26 07:09:02'),
(3, 1, 'Event archived', 'Event ID: 1; Name: Innovation & Technology Workshop', '::1', '2026-10-26 07:09:17'),
(4, 1, 'Event created', 'Event ID: 4; Name: Innovation & Technology Workshop', '::1', '2026-10-26 07:14:24'),
(5, 1, 'Notice created', 'Notice ID: 1; Title: report', '::1', '2026-10-26 09:33:41');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `message`, `created_at`) VALUES
(1, 'Parth Kaklotar', 'parthkaklotar544@gmail.com', 'good study', '2026-10-26 07:46:45');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(100) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `venue` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `poster_path` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_date`, `venue`, `description`, `category`, `poster_path`, `status`) VALUES
(1, 'Innovation & Technology Workshop', '2026-10-20', 'College Auditorium', 'A student workshop on innovation and technology at CHARUSAT.', 'Workshop', 'event_uploads/da6742f4174f7be1667c767366132890.png', 'Inactive'),
(3, 'Run for Unity', '2026-10-22', 'College Auditorium', 'full enjoy', 'Sports', 'event_uploads/f49325d4a0e530b35b8efdf24725e73f.png', 'Inactive'),
(4, 'Innovation & Technology Workshop', '2026-10-30', 'College Auditorium', 'this event is the funny and knowlagebal', 'Workshop', 'event_uploads/e2f291fe60bc95043a2cadf2369e5972.png', 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `portal_notices`
--

CREATE TABLE `portal_notices` (
  `notice_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `expires_at` date DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portal_notices`
--

INSERT INTO `portal_notices` (`notice_id`, `title`, `message`, `expires_at`, `status`, `created_at`) VALUES
(1, 'report', 'your student is good study working', '2026-10-30', 'Active', '2026-10-26 09:33:41');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `event_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `student_id`, `event_id`) VALUES
(1, 1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `name`, `email`, `course`, `status`) VALUES
(1, 'PARTH KAKLOTAR', 'parthkaklotar544@gmail.com', 'Computer Science', 'Active'),
(2, 'nilkandh valani', 'nilkandh@example.com', 'Information Technology', 'Active'),
(3, 'vraj italiya', 'vraj@example.com', 'Computer Engineering', 'Active'),
(4, 'manas patel', 'manas@example.com', 'Computer Science', 'Inactive');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `enrollment` varchar(20) NOT NULL,
  `email` varchar(254) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year` varchar(30) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `role` enum('Admin','Student') NOT NULL DEFAULT 'Student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `enrollment`, `email`, `mobile`, `password`, `course`, `year`, `gender`, `role`, `created_at`) VALUES
(1, 'Parth Kaklotar', '25CS028', 'parthkaklotar544@gmail.com', '1234567893', '$2y$10$o7QYmk48/V0lSljwFUepV.qMP.jg7hE/BbyPnThMreKg90DsQL3ry', 'Computer Science', 'Second Year', 'Male', 'Admin', '2026-10-14 07:37:30'),
(2, 'nilkandh valani', '25EC121', 'nilkandh@example.com', '1335255768', '$2y$10$zxdneuE3KqslWcloE0uaTOpdIvZoNRrS7ZUVbbdwQYIdbG8VSes3i', 'Computer Science', 'Second Year', 'Male', 'Student', '2026-10-14 11:47:20'),
(3, 'dharmik kaklotar', '25CS034', 'dharmik@example.com', '1435366346', '$2y$10$GCQqgaMCn/jXsabQd4lE.ulia5h.cK9bQ1sdsJbQTQEG41lWKTTo.', 'Computer Engineering', 'Second Year', 'Male', 'Student', '2026-10-26 09:42:58'),
(4, 'krutin koradiya', '25CS036', 'krutin@example.com', '3546474643', '$2y$10$qeZunLdtTLKMDbuZNrO1keMHW9jt3dKL90qGaJLlYa6T8NPoF8ir2', 'Computer Engineering', 'Second Year', 'Male', 'Student', '2026-10-26 09:47:11'),
(5, 'krutin', '25Cs038', 'student@example.com', '2543252353', '$2y$10$xrlDAJrrbk/43nJ7xia.P.J01Lz5LJAmKaXTtTwm3kImrZ6218MIq', 'Information Technology', 'Second Year', 'Male', 'Student', '2026-10-26 09:48:59');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `api_students`
--
ALTER TABLE `api_students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `portal_notices`
--
ALTER TABLE `portal_notices`
  ADD PRIMARY KEY (`notice_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollment` (`enrollment`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `api_students`
--
ALTER TABLE `api_students`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `portal_notices`
--
ALTER TABLE `portal_notices`
  MODIFY `notice_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`),
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
