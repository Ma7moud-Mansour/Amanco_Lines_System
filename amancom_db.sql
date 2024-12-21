-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2024 at 04:41 PM
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
-- Database: `main_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `client_company`
--

CREATE TABLE `client_company` (
  `Name` varchar(100) NOT NULL,
  `Class` varchar(50) NOT NULL,
  `Server_Name` varchar(100) NOT NULL,
  `Code` int(11) NOT NULL,
  `Odoo_SO` int(11) NOT NULL,
  `SIM_Serial_no` int(11) NOT NULL,
  `Device_Serial_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_company`
--

INSERT INTO `client_company` (`Name`, `Class`, `Server_Name`, `Code`, `Odoo_SO`, `SIM_Serial_no`, `Device_Serial_no`) VALUES
('Company A', 'A', 'Server1', 1, 1001, 1, 1),
('Company B', 'B', 'Server2', 2, 1002, 2, 2),
('Company C', 'A', 'Server3', 3, 1003, 3, 3),
('Company D', 'C', 'Server4', 4, 1004, 4, 4),
('Company E', 'B', 'Server5', 5, 1005, 5, 5),
('Company F', 'A', 'Server6', 6, 1006, 6, 6),
('Company G', 'C', 'Server7', 7, 1007, 7, 7),
('Company H', 'B', 'Server8', 8, 1008, 8, 8),
('Company I', 'A', 'Server9', 9, 1009, 9, 9),
('Company J', 'C', 'Server10', 10, 1010, 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `client_number`
--

CREATE TABLE `client_number` (
  `Client_Number` int(11) NOT NULL,
  `Client_Code` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client_number`
--

INSERT INTO `client_number` (`Client_Number`, `Client_Code`) VALUES
(1111111, 1),
(2222222, 2),
(3333333, 3),
(4444444, 4),
(5555555, 5),
(6666666, 6),
(7777777, 7),
(8888888, 8),
(9999999, 9),
(1010101, 10);

-- --------------------------------------------------------

--
-- Table structure for table `device`
--

CREATE TABLE `device` (
  `Device_type` varchar(50) NOT NULL,
  `Serial_no` int(11) NOT NULL,
  `SIM_Serial_no` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `device`
--

INSERT INTO `device` (`Device_type`, `Serial_no`, `SIM_Serial_no`) VALUES
('gt06n', 1, 1),
('at4', 2, 2),
('tr06', 3, 3),
('gt06n', 4, 4),
('gt06n', 5, 5),
('obd', 6, 6),
('Qbit', 7, 7),
('tr06', 8, 8),
('AT4', 9, 9),
('obd', 10, 10);

-- --------------------------------------------------------

--
-- Table structure for table `odoo`
--

CREATE TABLE `odoo` (
  `Odoo_SO` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `odoo`
--

INSERT INTO `odoo` (`Odoo_SO`) VALUES
(1001),
(1002),
(1003),
(1004),
(1005),
(1006),
(1007),
(1008),
(1009),
(1010);

-- --------------------------------------------------------

--
-- Table structure for table `payment_method`
--

CREATE TABLE `payment_method` (
  `Payment_type` varchar(50) NOT NULL,
  `Photo_Of_Payment` varchar(50) NOT NULL,
  `VF_num` int(11) DEFAULT NULL,
  `ODOO_SO` int(11) NOT NULL,
  `Device_Serial` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_method`
--

INSERT INTO `payment_method` (`Payment_type`, `Photo_Of_Payment`, `VF_num`, `ODOO_SO`, `Device_Serial`) VALUES
('Credit Card', '', 123, 1001, 1),
('Bank Transfer', '', 124, 1002, 2),
('PayPal', '', 125, 1003, 3),
('Cash', '', 126, 1004, 4),
('Credit Card', '', 127, 1005, 5),
('Bank Transfer', '', 128, 1006, 6),
('PayPal', '', 129, 1007, 7),
('Cash', '', 130, 1008, 8),
('Credit Card', '', 131, 1009, 9),
('Bank Transfer', '', 132, 1010, 10);

-- --------------------------------------------------------

--
-- Table structure for table `server_subscription`
--

CREATE TABLE `server_subscription` (
  `Device_num` int(11) NOT NULL,
  `Subscription_Price` int(11) NOT NULL,
  `Program_Name` varchar(100) NOT NULL,
  `sub_Day` int(11) NOT NULL,
  `sub_Month` int(11) NOT NULL,
  `sub_Year` int(11) NOT NULL,
  `Subscription_Period` int(11) NOT NULL,
  `Odoo_SO` int(11) NOT NULL,
  `Device_no_Serial` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `server_subscription`
--

INSERT INTO `server_subscription` (`Device_num`, `Subscription_Price`, `Program_Name`, `sub_Day`, `sub_Month`, `sub_Year`, `Subscription_Period`, `Odoo_SO`, `Device_no_Serial`) VALUES
(1, 200, 'Program1', 1, 1, 2023, 12, 1001, 1),
(2, 300, 'Program2', 2, 2, 2023, 24, 1002, 2),
(3, 400, 'Program3', 3, 3, 2023, 36, 1003, 3),
(4, 500, 'Program4', 4, 4, 2023, 12, 1004, 4),
(5, 250, 'Program5', 5, 5, 2023, 24, 1005, 5),
(6, 350, 'Program6', 6, 6, 2023, 36, 1006, 6),
(7, 450, 'Program7', 7, 7, 2023, 12, 1007, 7),
(8, 550, 'Program8', 8, 8, 2023, 24, 1008, 8),
(9, 600, 'Program9', 9, 9, 2023, 36, 1009, 9),
(10, 700, 'Program10', 10, 10, 2023, 12, 1010, 10);

-- --------------------------------------------------------

--
-- Table structure for table `sim_card`
--

CREATE TABLE `sim_card` (
  `Serial_no` int(11) NOT NULL,
  `Service_Provider` varchar(50) NOT NULL,
  `Price` int(11) NOT NULL,
  `Type` int(11) NOT NULL,
  `Is_Sold` int(11) NOT NULL,
  `SIM_num` int(11) NOT NULL,
  `Day` int(11) NOT NULL,
  `Month` int(11) NOT NULL,
  `Year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sim_card`
--

INSERT INTO `sim_card` (`Serial_no`, `Service_Provider`, `Price`, `Type`, `Is_Sold`, `SIM_num`, `Day`, `Month`, `Year`) VALUES
(1, 'Vodafone', 50, 1, 0, 1234567890, 15, 12, 2022),
(2, 'Orange', 40, 1, 1, 1234567891, 16, 11, 2022),
(3, 'Etisalat', 60, 2, 0, 1234567892, 17, 10, 2022),
(4, 'WE', 30, 1, 1, 1234567893, 18, 9, 2022),
(5, 'Vodafone', 55, 2, 1, 1234567894, 19, 8, 2022),
(6, 'Orange', 35, 1, 0, 1234567895, 20, 7, 2022),
(7, 'Etisalat', 65, 2, 1, 1234567896, 21, 6, 2022),
(8, 'WE', 25, 1, 0, 1234567897, 22, 5, 2022),
(9, 'Vodafone', 45, 1, 0, 1234567898, 23, 4, 2022),
(10, 'Orange', 50, 2, 1, 1234567899, 24, 3, 2022);

-- --------------------------------------------------------

--
-- Table structure for table `sim_inventory`
--

CREATE TABLE `sim_inventory` (
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sim_inventory`
--

INSERT INTO `sim_inventory` (`Quantity`) VALUES
(100),
(95),
(90),
(85),
(80),
(75),
(70),
(65),
(60),
(55);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `client_company`
--
ALTER TABLE `client_company`
  ADD PRIMARY KEY (`Code`),
  ADD KEY `Odoo_SO` (`Odoo_SO`),
  ADD KEY `SIM_Serial_no` (`SIM_Serial_no`),
  ADD KEY `Device_Serial_no` (`Device_Serial_no`);

--
-- Indexes for table `client_number`
--
ALTER TABLE `client_number`
  ADD PRIMARY KEY (`Client_Code`);

--
-- Indexes for table `device`
--
ALTER TABLE `device`
  ADD PRIMARY KEY (`Serial_no`),
  ADD KEY `SIM_Serial_no` (`SIM_Serial_no`);

--
-- Indexes for table `odoo`
--
ALTER TABLE `odoo`
  ADD PRIMARY KEY (`Odoo_SO`);

--
-- Indexes for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD KEY `ODOO_SO` (`ODOO_SO`,`Device_Serial`);

--
-- Indexes for table `server_subscription`
--
ALTER TABLE `server_subscription`
  ADD PRIMARY KEY (`Odoo_SO`,`Device_no_Serial`),
  ADD KEY `Device_no_Serial` (`Device_no_Serial`);

--
-- Indexes for table `sim_card`
--
ALTER TABLE `sim_card`
  ADD PRIMARY KEY (`Serial_no`),
  ADD UNIQUE KEY `SIM_num` (`SIM_num`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `client_company`
--
ALTER TABLE `client_company`
  ADD CONSTRAINT `client_company_ibfk_1` FOREIGN KEY (`Odoo_SO`) REFERENCES `odoo` (`Odoo_SO`),
  ADD CONSTRAINT `client_company_ibfk_2` FOREIGN KEY (`SIM_Serial_no`) REFERENCES `sim_card` (`Serial_no`),
  ADD CONSTRAINT `client_company_ibfk_3` FOREIGN KEY (`Device_Serial_no`) REFERENCES `device` (`Serial_no`);

--
-- Constraints for table `client_number`
--
ALTER TABLE `client_number`
  ADD CONSTRAINT `client_number_ibfk_1` FOREIGN KEY (`Client_Code`) REFERENCES `client_company` (`Code`);

--
-- Constraints for table `device`
--
ALTER TABLE `device`
  ADD CONSTRAINT `device_ibfk_1` FOREIGN KEY (`SIM_Serial_no`) REFERENCES `sim_card` (`Serial_no`);

--
-- Constraints for table `payment_method`
--
ALTER TABLE `payment_method`
  ADD CONSTRAINT `payment_method_ibfk_1` FOREIGN KEY (`ODOO_SO`,`Device_Serial`) REFERENCES `server_subscription` (`Odoo_SO`, `Device_no_Serial`);

--
-- Constraints for table `server_subscription`
--
ALTER TABLE `server_subscription`
  ADD CONSTRAINT `server_subscription_ibfk_1` FOREIGN KEY (`Odoo_SO`) REFERENCES `odoo` (`Odoo_SO`),
  ADD CONSTRAINT `server_subscription_ibfk_2` FOREIGN KEY (`Device_no_Serial`) REFERENCES `device` (`Serial_no`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
