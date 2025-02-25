-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3309
-- Generation Time: Feb 20, 2025 at 08:09 AM
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
-- Database: `library-mgmt`
--

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `B_id` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Author` varchar(255) NOT NULL,
  `ISBN` varchar(20) DEFAULT NULL,
  `Category` varchar(255) DEFAULT NULL,
  `Status` enum('Available','Reserved','Issued','Unavailable') DEFAULT 'Available',
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`B_id`, `Title`, `Author`, `ISBN`, `Category`, `Status`, `Quantity`) VALUES
(1001, 'DSA', 'Bhupendra Singh', 'B1356', 'Programming', 'Available', 5),
(1002, 'The Puppet Masters', 'Robert Heinlein', '12uy32', 'Science Fiction', 'Available', 1),
(1003, 'Operating system', 'xyz', '45237', 'tydsw', 'Available', 4),
(1004, 'DBMS', 'tywej', '87234', 'tywed', 'Available', 6),
(1005, 'Abraham Inchon', 'YGHS', '6282O', 'Biography', 'Available', 4);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `purchase_order_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `purchase_order_id`, `email`, `name`, `amount`, `status`, `created_at`) VALUES
(1, 'LIB679598fe4cce1', 'xyz@gmail.com', '', '66.00', 'Completed', '2025-01-26 02:07:58'),
(2, 'LIB6795b2752b9a3', 'xyz@gmail.com', '', '51.00', 'Completed', '2025-01-26 03:56:37'),
(3, 'LIB67b6c346b7df4', 'jescie@gmail.com', '', '15.00', 'Completed', '2025-02-20 05:53:10');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `T_id` int(11) NOT NULL,
  `S_email` varchar(255) NOT NULL,
  `B_id` int(11) NOT NULL,
  `Issue_date` date DEFAULT NULL,
  `Due_date` date DEFAULT NULL,
  `Return_date` date DEFAULT current_timestamp(),
  `Fine` decimal(10,2) DEFAULT 0.00,
  `Status` enum('Requested','Issued','Rejected','Returned') DEFAULT 'Requested',
  `payment_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`T_id`, `S_email`, `B_id`, `Issue_date`, `Due_date`, `Return_date`, `Fine`, `Status`, `payment_id`) VALUES
(1, 'jescie@gmail.com', 1001, '2024-12-21', '2025-01-04', '2024-12-24', '0.00', 'Returned', NULL),
(2, 'xyz@gmail.com', 1001, '2024-12-21', '2025-01-04', '0000-00-00', '0.00', 'Returned', 'LIB679598fe4cce1'),
(3, 'jescie@gmail.com', 1001, '2024-12-24', '2024-12-25', '2024-12-25', '0.00', 'Returned', NULL),
(4, 'xyz@gmail.com', 1003, '2024-12-26', '2025-01-09', '0000-00-00', '0.00', 'Returned', 'LIB6795b2752b9a3'),
(5, 'xyz@gmail.com', 1005, '2025-02-15', '2025-02-16', '0000-00-00', '12.00', 'Issued', NULL),
(6, 'jescie@gmail.com', 1004, '2025-02-12', '2025-02-15', '0000-00-00', '0.00', 'Returned', 'LIB67b6c346b7df4');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `role` enum('Admin','Librarian','Student') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `password`, `email`, `phone`, `role`) VALUES
(101, 'Yamuna Acharya', 'admin123', 'admin@gmail.com', '9876543210', 'Admin'),
(102, 'Jescie Donovan', '123', 'jescie@gmail.com', '9876509865', 'Student'),
(103, 'pamela rai', '1234', 'pamela@gmail.com', '983466718', 'Librarian'),
(104, 'xyz', '12', 'xyz@gmail.com', '986453727', 'Student'),
(105, 'Shanti Rai', 'shanti123', 'shanti@gmail.com', '9872352632', 'Librarian');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`B_id`),
  ADD UNIQUE KEY `ISBN` (`ISBN`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`T_id`),
  ADD KEY `S_email` (`S_email`),
  ADD KEY `B_id` (`B_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `Email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `B_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1006;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `T_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`S_email`) REFERENCES `users` (`email`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`B_id`) REFERENCES `books` (`B_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
