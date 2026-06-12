-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20260223.2354c6b4e9
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2026 at 10:22 AM
-- Server version: 8.0.40
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cinema_ticket`
--

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int NOT NULL,
  `title` varchar(150) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `duration` int NOT NULL,
  `synopsis` text,
  `poster` varchar(255) DEFAULT NULL,
  `release_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `genre`, `duration`, `synopsis`, `poster`, `release_date`) VALUES
(2, 'SMILE', 'Thriller', 130, 'After witnessing a bizarre, traumatic incident involving a patient, a psychiatrist becomes increasingly convinced she is being threatened by an uncanny entity.', '6a243179dc7aa.jpg', '2026-06-07'),
(3, '28 Years Later', 'Horror', 120, 'A group of survivors of the rage virus live on a small island. When one of the group leaves the island on a mission into the mainland, he discovers secrets, wonders, and horrors that have mutated not only the infected but other survivors.', '6a2431ba7a2b1.jpg', '2026-06-07'),
(4, 'FINAL DESTINATION (BLOODLINE)', 'Thriller', 120, 'Plagued by a recurring violent nightmare, a college student returns home to find the one person who can break the cycle and save her family from the horrific fate that inevitably awaits them.', '6a24320f0d23a.jpg', '2026-06-07'),
(5, '12 STRONG', 'Action', 120, '12 Strong tells the story of the first Special Forces team deployed to Afghanistan after 9/11; under the leadership of a new captain, the team must work with an Afghan warlord to take down the Taliban.', '6a254179ac10a.jpg', '2026-06-07'),
(6, 'WHIPLASH', 'Drama', 120, 'A promising young drummer enrolls at a cut-throat music conservatory where his dreams of greatness are mentored by an instructor who will stop at nothing to realize a student\'s potential.', '6a2541d140557.jpg', '2026-06-07'),
(7, 'SAW X', 'Thriller', 120, 'A sick and desperate John travels to Mexico for a risky and experimental medical procedure in hopes of a miracle cure for his cancer only to discover the entire operation is a scam to defraud the most vulnerable.', '6a2542108d537.jpg', '2026-06-07'),
(8, 'JAWS', 'Sci-Fi', 120, 'When a massive killer shark unleashes chaos on a beach community off Long Island, it\'s up to the local police chief, a marine biologist, and an old seafarer to hunt the beast down.', '6a25429475c76.jpg', '2026-06-07'),
(9, 'THE PURGE', 'Thriller', 120, 'A wealthy family is held hostage for harboring the target of a murderous syndicate during the Purge, a 12-hour period in which any and all crime is legal.', '6a25439dad9e8.jpg', '2026-06-07'),
(10, 'THE BATMAN', 'Action', 120, 'When a sadistic serial killer begins murdering key political figures in Gotham, the Batman is forced to investigate the city\'s hidden corruption and question his family\'s involvement.', '6a2543e295e63.jpg', '2026-06-07'),
(11, 'LOVE ROSIE', 'Romance', 120, 'Rosie and Alex have been best friends since they were 5, so they couldn\'t possibly be right for each other--or could they? When it comes to love, life and making the right choices, these two are their own worst enemies.', '6a25441c7dd3e.jpg', '2026-06-07');

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `seat_id` int NOT NULL,
  `hall_number` int NOT NULL,
  `seat_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`seat_id`, `hall_number`, `seat_code`) VALUES
(1, 1, 'A1'),
(2, 1, 'A2'),
(3, 1, 'A3'),
(4, 1, 'A4'),
(5, 1, 'B1'),
(6, 1, 'B2'),
(7, 1, 'B3'),
(8, 1, 'B4'),
(9, 1, 'C1'),
(10, 1, 'C2'),
(11, 1, 'C3'),
(12, 1, 'C4'),
(13, 2, 'A1'),
(14, 2, 'A2'),
(15, 2, 'A3'),
(16, 2, 'A4'),
(17, 2, 'B1'),
(18, 2, 'B2'),
(19, 2, 'B3'),
(20, 2, 'B4'),
(21, 2, 'C1'),
(22, 2, 'C2'),
(23, 2, 'C3'),
(24, 2, 'C4'),
(25, 1, 'A1'),
(26, 1, 'A2'),
(27, 1, 'A3'),
(28, 1, 'A4'),
(29, 1, 'A5'),
(30, 1, 'A6'),
(31, 1, 'A7'),
(32, 1, 'A8'),
(33, 1, 'A9'),
(34, 1, 'A10'),
(35, 1, 'B1'),
(36, 1, 'B2'),
(37, 1, 'B3'),
(38, 1, 'B4'),
(39, 1, 'B5'),
(40, 1, 'B6'),
(41, 1, 'B7'),
(42, 1, 'B8'),
(43, 1, 'B9'),
(44, 1, 'B10'),
(45, 1, 'C1'),
(46, 1, 'C2'),
(47, 1, 'C3'),
(48, 1, 'C4'),
(49, 1, 'C5'),
(50, 1, 'C6'),
(51, 1, 'C7'),
(52, 1, 'C8'),
(53, 1, 'C9'),
(54, 1, 'C10'),
(55, 1, 'D1'),
(56, 1, 'D2'),
(57, 1, 'D3'),
(58, 1, 'D4'),
(59, 1, 'D5'),
(60, 1, 'D6'),
(61, 1, 'D7'),
(62, 1, 'D8'),
(63, 1, 'D9'),
(64, 1, 'D10'),
(65, 1, 'E1'),
(66, 1, 'E2'),
(67, 1, 'E3'),
(68, 1, 'E4'),
(69, 1, 'E5'),
(70, 1, 'E6'),
(71, 1, 'E7'),
(72, 1, 'E8'),
(73, 1, 'E9'),
(74, 1, 'E10'),
(75, 1, 'F1'),
(76, 1, 'F2'),
(77, 1, 'F3'),
(78, 1, 'F4'),
(79, 1, 'F5'),
(80, 1, 'F6'),
(81, 1, 'F7'),
(82, 1, 'F8'),
(83, 1, 'F9'),
(84, 1, 'F10'),
(85, 1, 'G1'),
(86, 1, 'G2'),
(87, 1, 'G3'),
(88, 1, 'G4'),
(89, 1, 'G5'),
(90, 1, 'G6'),
(91, 1, 'G7'),
(92, 1, 'G8'),
(93, 1, 'G9'),
(94, 1, 'G10'),
(95, 1, 'H1'),
(96, 1, 'H2'),
(97, 1, 'H3'),
(98, 1, 'H4'),
(99, 1, 'H5'),
(100, 1, 'H6'),
(101, 1, 'H7'),
(102, 1, 'H8'),
(103, 1, 'H9'),
(104, 1, 'H10'),
(105, 1, 'I1'),
(106, 1, 'I2'),
(107, 1, 'I3'),
(108, 1, 'I4'),
(109, 1, 'I5'),
(110, 1, 'I6'),
(111, 1, 'I7'),
(112, 1, 'I8'),
(113, 1, 'I9'),
(114, 1, 'I10'),
(115, 1, 'J1'),
(116, 1, 'J2'),
(117, 1, 'J3'),
(118, 1, 'J4'),
(119, 1, 'J5'),
(120, 1, 'J6'),
(121, 1, 'J7'),
(122, 1, 'J8'),
(123, 1, 'J9'),
(124, 1, 'J10'),
(125, 2, 'A1'),
(126, 2, 'A2'),
(127, 2, 'A3'),
(128, 2, 'A4'),
(129, 2, 'A5'),
(130, 2, 'A6'),
(131, 2, 'A7'),
(132, 2, 'A8'),
(133, 2, 'A9'),
(134, 2, 'A10'),
(135, 2, 'B1'),
(136, 2, 'B2'),
(137, 2, 'B3'),
(138, 2, 'B4'),
(139, 2, 'B5'),
(140, 2, 'B6'),
(141, 2, 'B7'),
(142, 2, 'B8'),
(143, 2, 'B9'),
(144, 2, 'B10'),
(145, 2, 'C1'),
(146, 2, 'C2'),
(147, 2, 'C3'),
(148, 2, 'C4'),
(149, 2, 'C5'),
(150, 2, 'C6'),
(151, 2, 'C7'),
(152, 2, 'C8'),
(153, 2, 'C9'),
(154, 2, 'C10'),
(155, 2, 'D1'),
(156, 2, 'D2'),
(157, 2, 'D3'),
(158, 2, 'D4'),
(159, 2, 'D5'),
(160, 2, 'D6'),
(161, 2, 'D7'),
(162, 2, 'D8'),
(163, 2, 'D9'),
(164, 2, 'D10'),
(165, 2, 'E1'),
(166, 2, 'E2'),
(167, 2, 'E3'),
(168, 2, 'E4'),
(169, 2, 'E5'),
(170, 2, 'E6'),
(171, 2, 'E7'),
(172, 2, 'E8'),
(173, 2, 'E9'),
(174, 2, 'E10'),
(175, 2, 'F1'),
(176, 2, 'F2'),
(177, 2, 'F3'),
(178, 2, 'F4'),
(179, 2, 'F5'),
(180, 2, 'F6'),
(181, 2, 'F7'),
(182, 2, 'F8'),
(183, 2, 'F9'),
(184, 2, 'F10'),
(185, 2, 'G1'),
(186, 2, 'G2'),
(187, 2, 'G3'),
(188, 2, 'G4'),
(189, 2, 'G5'),
(190, 2, 'G6'),
(191, 2, 'G7'),
(192, 2, 'G8'),
(193, 2, 'G9'),
(194, 2, 'G10'),
(195, 2, 'H1'),
(196, 2, 'H2'),
(197, 2, 'H3'),
(198, 2, 'H4'),
(199, 2, 'H5'),
(200, 2, 'H6'),
(201, 2, 'H7'),
(202, 2, 'H8'),
(203, 2, 'H9'),
(204, 2, 'H10'),
(205, 2, 'I1'),
(206, 2, 'I2'),
(207, 2, 'I3'),
(208, 2, 'I4'),
(209, 2, 'I5'),
(210, 2, 'I6'),
(211, 2, 'I7'),
(212, 2, 'I8'),
(213, 2, 'I9'),
(214, 2, 'I10'),
(215, 2, 'J1'),
(216, 2, 'J2'),
(217, 2, 'J3'),
(218, 2, 'J4'),
(219, 2, 'J5'),
(220, 2, 'J6'),
(221, 2, 'J7'),
(222, 2, 'J8'),
(223, 2, 'J9'),
(224, 2, 'J10');

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `showtime_id` int NOT NULL,
  `movie_id` int NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `hall_number` int NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`showtime_id`, `movie_id`, `date`, `time`, `hall_number`, `price`) VALUES
(2, 5, '2026-07-07', '22:00:00', 1, 50000.00),
(3, 3, '2026-07-07', '17:00:00', 2, 50000.00),
(4, 4, '2026-07-07', '14:00:00', 1, 50000.00),
(5, 7, '2026-07-30', '20:00:00', 2, 50000.00),
(6, 2, '2026-07-07', '22:00:00', 2, 50000.00),
(7, 10, '2026-07-07', '19:00:00', 1, 50000.00),
(8, 6, '2026-07-07', '15:00:00', 1, 50000.00),
(9, 11, '2026-07-07', '18:00:00', 1, 50000.00),
(10, 9, '2026-07-07', '12:00:00', 1, 50000.00),
(11, 8, '2026-07-07', '23:00:00', 1, 50000.00);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` int NOT NULL,
  `transaction_id` int NOT NULL,
  `showtime_id` int NOT NULL,
  `seat_id` int NOT NULL,
  `status` enum('booked','canceled') DEFAULT 'booked'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticket_id`, `transaction_id`, `showtime_id`, `seat_id`, `status`) VALUES
(11, 4, 6, 176, 'canceled'),
(12, 4, 6, 177, 'canceled'),
(13, 4, 6, 178, 'canceled'),
(14, 4, 6, 179, 'canceled'),
(15, 5, 6, 22, 'canceled'),
(16, 5, 6, 23, 'canceled'),
(17, 5, 6, 24, 'canceled'),
(18, 5, 6, 149, 'canceled');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int NOT NULL,
  `user_id` int NOT NULL,
  `transaction_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` enum('cash','transfer','e-wallet') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `transaction_date`, `total_amount`, `payment_method`) VALUES
(1, 3, '2026-06-06 13:08:38', 250000.00, 'cash'),
(2, 3, '2026-06-06 13:26:46', 100000.00, 'transfer'),
(3, 4, '2026-06-06 13:51:19', 150000.00, 'e-wallet'),
(4, 4, '2026-06-07 10:24:13', 200000.00, 'e-wallet'),
(5, 4, '2026-06-10 02:08:45', 200000.00, 'e-wallet');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(3, 'Admin', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', 'admin', '2026-05-31 13:37:15'),
(4, 'sebian', 'sebian@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '2026-06-06 13:50:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`seat_id`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`showtime_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `seat_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=225;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `showtime_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`transaction_id`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`showtime_id`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`seat_id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
