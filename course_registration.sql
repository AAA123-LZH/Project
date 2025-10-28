-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 28, 2025 at 05:08 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `course_registration`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `full_name`, `email`) VALUES
(1, 'admin', '123', 'Admin', 'admin@setu.com'),
(2, 'user', '123', 'User', 'user@setu.com');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `credits` int(11) DEFAULT NULL,
  `lecturer` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_code`, `course_name`, `description`, `credits`, `lecturer`) VALUES
(123, 'CS101', 'Computer Basics', 'Introduction to basic computer concepts.', 3, 'Mike'),
(234, 'MAT201', 'Math', 'Covers basic calculus topics.', 4, 'Tom'),
(345, 'ENG150', 'English Writing', 'Focuses on writing and reading skills.', 2, 'Leon'),
(777, 'CHI123', 'Chinese', 'this is a beautiful language', 3, 'Wang'),
(778, 'tu202', 'math', 'this is analysis class', 3, 'tom'),
(779, 'ts111', 'sport', 'help students  healthy', 3, 'Aim'),
(780, 'ts112', 'sports', 'help students  healthy', 3, 'Aim'),
(781, 'ts113', 'sportss', 'help students  healthy', 3, 'Aim'),
(782, 'cw111', 'maths', 'zxjhlkds', 3, 'Danny'),
(783, 'C757', 'Python', 'Code', 3, 'roc');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `status` enum('enrolled','approved','pending') DEFAULT 'pending',
  `date_registered` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `student_id`, `course_id`, `status`, `date_registered`) VALUES
(52, 1, 777, 'pending', '2025-10-17 21:50:15'),
(53, 1, 345, 'pending', '2025-10-18 16:04:47'),
(59, 1, 123, 'approved', '2025-10-20 13:07:15'),
(60, 1, 234, 'approved', '2025-10-20 13:07:15'),
(67, 2, 123, 'approved', '2025-10-20 13:13:40'),
(68, 2, 345, 'approved', '2025-10-20 13:13:40'),
(69, 3, 234, 'approved', '2025-10-20 13:13:40'),
(70, 3, 345, 'approved', '2025-10-20 13:13:40'),
(71, 4, 777, 'approved', '2025-10-20 13:13:40'),
(72, 1, 778, 'approved', '2025-10-20 13:19:14'),
(74, 7, 123, 'pending', '2025-10-28 15:53:16');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `student_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `program` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`student_id`, `username`, `password`, `full_name`, `email`, `program`) VALUES
(1, '123456', '123456', '[linzhaohua]', '[123456.@qq.com]', '[math]'),
(2, 'alice', '123456', 'Alice Brown', 'alice@setu.edu', 'Computer Science'),
(3, 'bob', '123456', 'Bob Johnson', 'bob@setu.edu', 'Business Administration'),
(4, 'tom', '123456', 'Tom Davis', 'tom@setu.edu', 'Electrical Engineering'),
(6, '6', '111111', 'rddddddd', 'wwwwww@gmail.com', NULL),
(7, 'username', 'password', 'zhangsan', 'zhangsan@setu.com', 'big data');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `course_code` (`course_code`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`student_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=784;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `student_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`student_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`course_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
