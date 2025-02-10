-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 08, 2025 at 08:10 AM
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
-- Database: `coldstorage`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `search_chamber_master` ()   BEGIN
    SELECT 
        chamber_id, 
        chamber, 
        created_date, 
        created_by, 
        modified_date, 
        modified_by 
    FROM tbl_chamber_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_city_master` ()   BEGIN
    SELECT c.city_id, c.city_name, c.state_id, c.country_id, c.created_date, c.created_by, c.modified_date, c.modified_by,
           s.state_name,
           co.country_name
    FROM tbl_city_master c
    LEFT JOIN tbl_state_master s ON c.state_id = s.state_id
    LEFT JOIN tbl_country_master co ON s.country_id = co.country_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_country_master` ()   BEGIN
    SELECT * FROM tbl_country_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_customer_contact_detail` (IN `WhereField` INT)   BEGIN
SELECT * FROM tbl_customer_contact_detail where 
customer_id= whereField;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_customer_master` ()   BEGIN
SELECT * FROM tbl_customer_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_district_master` ()   BEGIN
    SELECT 
        d.district_id,
        d.district_name,
        d.city_id,
        d.state_id,
        d.country_id,
        d.created_date,
        d.created_by,
        d.modified_date,
        d.modified_by,
        c.city_name,
        s.state_name,
        co.country_name,
        u_created.username AS created_by_username,
        u_modified.username AS modified_by_username
    FROM tbl_district_master d
    LEFT JOIN tbl_city_master c ON d.city_id = c.city_id
    LEFT JOIN tbl_state_master s ON d.state_id = s.state_id
    LEFT JOIN tbl_country_master co ON d.country_id = co.country_id
    LEFT JOIN tbl_user_master u_created ON d.created_by = u_created.id
    LEFT JOIN tbl_user_master u_modified ON d.modified_by = u_modified.id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_floor_master` ()   BEGIN
    SELECT 
        floor_id, 
        floor, 
        created_date, 
        created_by, 
        modified_date, 
        modified_by 
    FROM 
        tbl_floor_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_itemunit_master` ()   BEGIN
    SELECT 
        itemunit_id, 
        item_id, 
        unit_id, 
        created_date, 
        created_by, 
        modified_date, 
        modified_by 
    FROM 
        tbl_itemunit_mapping_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_item_master` ()   BEGIN
    SELECT 
        item_id, 
        item_name, 
        gst, 
        market_rate, 
        status, 
        created_date, 
        created_by, 
        modified_date, 
        modified_by
    FROM 
        tbl_item_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_item_unit_master` ()   BEGIN
    -- Query to fetch data from the table and group by item_id, created_date, and modified_date
    SELECT 
        `item_unit_id`, 
        `item_id`, 
        `created_date`, 
        `modified_date`
    FROM 
        `tbl_item_unit_master`
    GROUP BY 
        `item_id`, `created_date`, `modified_date`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_location_master` ()   BEGIN
    SELECT 
        location_id, 
        customer_id, 
        chamber_id, 
        floor_id, 
        rack_id, 
        location, 
        created_date, 
        created_by, 
        modified_date, 
        modified_by
    FROM tbl_location_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_rack_master` ()   BEGIN
    SELECT rack_id, rack, created_date, created_by, modified_date, modified_by 
    FROM tbl_rack_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_state_master` ()   BEGIN
    SELECT 
        s.state_id,
        s.state_name,
        s.country_id,
        s.created_date,
        s.created_by,
        s.modified_date,
        s.modified_by,
        c.country_name
    FROM tbl_state_master s
    LEFT JOIN tbl_country_master c ON s.country_id = c.country_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_unit_master` ()   BEGIN
    SELECT 
        unit_id, 
        unit, 
        conversion_factor, 
        created_date, 
        created_by, 
        modified_date, 
        modified_by 
    FROM tbl_unit_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `search_user_master` ()   BEGIN
    SELECT * FROM tbl_user_master;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_customer_contact_detail` (IN `p_customer_contact_detail_id` INT, IN `p_customer_id` INT, IN `p_person_name` VARCHAR(255), IN `p_contact_no` INT, IN `p_email_id` VARCHAR(255), IN `p_is_send_sms` BOOLEAN, IN `p_is_send_email` BOOLEAN, IN `TransactionMode` CHAR(1))   BEGIN
    IF TransactionMode = 'I' THEN
        -- Insert a new customer record
       INSERT INTO `tbl_customer_contact_detail` (`customer_contact_detail_id`,`customer_id`, `person_name`, `contact_no`, `email_id`, `is_send_sms`, `is_send_email`) VALUES (p_customer_contact_detail_id, p_customer_id, p_person_name, p_contact_no, p_email_id, p_is_send_sms, TransactionMode);

    ELSEIF TransactionMode = 'U' THEN
        -- Update an existing customer record by customer_detail_id
        UPDATE `tbl_customer_contact_details`
        SET `customer_id` = p_customer_id,
            `person_name` = p_person_name,
            `contact_no` = p_contact_no,
            `email` = p_email_id,
            `send_sms` = p_is_send_sms,
            `send_email` = p_is_send_email
        WHERE `customer_contact_detail_id` = p_customer_contact_detail_id;

    ELSEIF TransactionMode = 'DELETE' THEN
        -- Delete a customer record by customer_detail_id
        DELETE FROM `tbl_customer_contact_details`
        WHERE `customer_contact_detail_id` = p_customer_contcat_detail_id;

    ELSE
        -- Handle case if action type is not recognized
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Invalid action type specified.';
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_customer_master` (INOUT `p_customer_id` INT, IN `p_customer_name` VARCHAR(255), IN `p_customer_type` VARCHAR(255), IN `p_address` VARCHAR(255), IN `p_district_id` INT, IN `p_city_id` INT, IN `p_state_id` INT, IN `p_country_id` INT, IN `p_pincode` INT, IN `p_contact_no` INT, IN `p_send_sms` BOOLEAN, IN `p_send_whatsapp` BOOLEAN, IN `p_weburl` VARCHAR(255), IN `p_email_id` VARCHAR(255), IN `p_send_email` BOOLEAN, IN `p_status` VARCHAR(255), IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR(1))   BEGIN
IF TransactionMode = 'I' THEN
    
    SET p_customer_id = (SELECT COALESCE(MAX(customer_id),0) + 1 FROM tbl_customer_master);
    INSERT INTO tbl_customer_master
    (   
        customer_id,
        customer_name,
        customer_type,
        address,
        district_id,
        city_id,
        state_id,
        country_id,
        pincode,
        contact_no,
        send_sms,
        send_whatsapp,
        weburl,
        email_id,
        send_email,
        status,
        created_date,
        created_by,
        modified_date,
        modified_by
    )
    VALUES
    (
        p_customer_id,
        p_customer_name,
        p_customer_type,
        p_address,
        p_district_id,
        p_city_id,
        p_state_id,
        p_country_id,
        p_pincode,
        p_contact_no,
        p_send_sms,
        p_send_whatsapp,
        p_weburl,
        p_email_id,
        p_send_email,
        p_status,
        NOW(),
        p_created_by,
        NOW(),
        p_modified_by
    );
    
ELSEIF TransactionMode = 'U' THEN
    
    UPDATE tbl_customer_master
    SET 
        customer_name = p_customer_name,
        customer_type = p_customer_type,
        address = p_address,
        district_id = p_district_id,
        city_id = p_city_id,
        state_id = p_state_id,
        country_id = p_country_id,
        pincode = p_pincode,
        contact_no = p_contact_no,
        send_sms = p_send_sms,
        send_whatsapp = p_send_whatsapp,
        weburl = p_weburl,
        email_id = p_email_id,
        send_email = p_send_email,
        status = p_status,
        created_date = NOW(),
        created_by = p_created_by,
        modified_date = NOW(),
        modified_by = p_modified_by
    WHERE customer_id = p_customer_id;
    
ELSEIF TransactionMode = 'D' THEN

    DELETE FROM tbl_customer_master WHERE customer_id = p_customer_id;
    
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_district_master` (IN `p_district_id` INT, IN `p_district_name` VARCHAR(255), IN `p_city_id` INT, IN `p_state_id` INT, IN `p_country_id` INT, IN `p_created_date` DATETIME, IN `p_created_by` INT, IN `p_modified_date` DATETIME, IN `p_modified_by` INT, IN `TransactionMode` CHAR)   BEGIN
IF TransactionMode = 'I' THEN
    
    SET p_district_id = (SELECT COALESCE(MAX(district_id),0) + 1 FROM tbl_district_master);
    INSERT INTO tbl_district_master
    (   
        district_id,
        district_name,
        city_id,
        state_id,
        country_id,
        created_date,
        created_by,
        modified_date,
        modified_by
    )
    VALUES
    (
        p_district_id,
        p_district_name,
        p_city_id,
        p_state_id,
        p_country_id,
        p_created_date,
        p_created_by,
        p_modified_date,
        p_modified_by
    );
    
ELSEIF TransactionMode = 'U' THEN
    
    UPDATE tbl_district_master
    SET 
        district_name = p_district_name,
        city_id = p_city_id,
        state_id = p_state_id,
        country_id = p_country_id,
        created_date = p_created_date,
        created_by = p_created_by,
        modified_date = p_modified_date,
        modified_by = p_modified_by
    WHERE district_id = p_district_id;
    
ELSEIF TransactionMode = 'D' THEN

    DELETE FROM tbl_district_master WHERE district_id = p_district_id;
    
END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `transaction_user_master` (IN `p_id` INT, IN `p_username` VARCHAR(255), IN `p_name` VARCHAR(255), IN `p_password` VARCHAR(255), IN `p_status` BOOLEAN, IN `TransactionMode` CHAR(1))   BEGIN
IF TransactionMode = 'I' THEN
	
    SET p_id = (SELECT COALESCE(MAX(id),0) + 1 FROM tbl_user_master);
    INSERT INTO tbl_user_master
    (	
        id,
        username,
        name,
        password,
        status
    )
    VALUES
    (
        p_id,
        p_username,
        p_name,
        p_password,
        p_status
    );
    
ELSEIF TransactionMode = 'U' THEN
	
    UPDATE tbl_user_master
    SET 
        username = p_username,
        name = p_name,
        password = p_password,
        status = p_status
    WHERE id = p_id;
    
ELSEIF TransactionMode = 'D' THEN

    DELETE FROM tbl_user_master WHERE id = p_id;
    
END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(50) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `name`, `status`) VALUES
(1, 'hetu', '12345', '', 0),
(12, '', '12434', 'jsbxjhbd', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_chamber_master`
--

CREATE TABLE `tbl_chamber_master` (
  `chamber_id` int(11) NOT NULL,
  `chamber` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` int(11) NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_chamber_master`
--

INSERT INTO `tbl_chamber_master` (`chamber_id`, `chamber`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(7, 'fandom', '2025-01-01 12:58:29', 16, '2025-01-28 08:37:17', 6),
(8, 'chamber connect', '2025-01-01 12:58:41', 16, '2025-01-28 08:38:05', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_city_master`
--

CREATE TABLE `tbl_city_master` (
  `city_id` int(11) NOT NULL,
  `city_name` varchar(100) NOT NULL,
  `state_id` int(10) NOT NULL,
  `country_id` int(10) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_city_master`
--

INSERT INTO `tbl_city_master` (`city_id`, `city_name`, `state_id`, `country_id`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(17, 'perth', 9, 59, '2025-01-01 12:00:45', 16, '2025-01-28 15:08:36', 6),
(18, 'chandigadh', 9, 59, '2025-01-01 12:00:57', 16, '2025-01-01 12:00:57', 16),
(19, 'junagadhh', 10, 0, '2025-01-01 12:07:18', 16, '2025-01-01 12:27:13', 6),
(20, 'Rajkot', 8, 58, '2025-01-28 15:08:59', 6, '2025-01-28 15:08:59', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_country_master`
--

CREATE TABLE `tbl_country_master` (
  `country_id` bigint(20) NOT NULL,
  `state_id` int(11) NOT NULL,
  `country_name` varchar(100) NOT NULL,
  `created_date` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `created_by` varchar(11) NOT NULL,
  `modified_date` datetime(6) NOT NULL DEFAULT current_timestamp(6),
  `modified_by` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_country_master`
--

INSERT INTO `tbl_country_master` (`country_id`, `state_id`, `country_name`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(56, 0, 'south Africa', '2025-01-01 11:55:57.000000', '6', '2025-01-28 12:13:51.000000', '6'),
(58, 0, 'australia', '2025-01-01 11:57:34.000000', '16', '2025-01-01 11:57:34.000000', '16'),
(59, 0, 'UP', '2025-01-01 12:00:04.000000', '6', '2025-01-01 12:26:47.000000', '6'),
(60, 0, 'bali', '2025-01-02 14:42:08.000000', '16', '2025-01-02 14:42:08.000000', '16');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_contact_detail`
--

CREATE TABLE `tbl_customer_contact_detail` (
  `customer_contact_detail_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL DEFAULT 0,
  `person_name` varchar(100) DEFAULT NULL,
  `contact_no` varchar(50) DEFAULT NULL,
  `email_id` varchar(50) DEFAULT NULL,
  `is_send_sms` tinyint(1) DEFAULT NULL,
  `is_send_email` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_contact_detail`
--

INSERT INTO `tbl_customer_contact_detail` (`customer_contact_detail_id`, `customer_id`, `person_name`, `contact_no`, `email_id`, `is_send_sms`, `is_send_email`) VALUES
(1, 2, 'gjgjh', '5657576', 'rutupatel717@gmail.com', 0, 0),
(2, 3, 'hetanshree', '367676828', 'hetu46@gmail.com', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customer_master`
--

CREATE TABLE `tbl_customer_master` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_type` varchar(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `district_id` int(10) NOT NULL,
  `city_id` int(50) NOT NULL,
  `state_id` int(10) NOT NULL,
  `country_id` int(50) NOT NULL,
  `pincode` int(10) NOT NULL,
  `contact_no` int(10) NOT NULL,
  `send_sms` tinyint(1) NOT NULL,
  `send_whatsapp` tinyint(1) NOT NULL,
  `weburl` varchar(100) NOT NULL,
  `email_id` varchar(100) NOT NULL,
  `send_email` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customer_master`
--

INSERT INTO `tbl_customer_master` (`customer_id`, `customer_name`, `customer_type`, `address`, `district_id`, `city_id`, `state_id`, `country_id`, `pincode`, `contact_no`, `send_sms`, `send_whatsapp`, `weburl`, `email_id`, `send_email`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(1, 'devanshi', 'global', 'jhgjh', 15, 17, 8, 58, 360002, 98710909, 0, 1, 't@gmail.com', 'rutupatel717@gmail.com', 0, 1, '2025-02-07 15:07:24', 1, '2025-02-07 15:07:24', 1),
(2, 'devanshi', 'global', 'jhgjh', 19, 18, 9, 59, 360002, 98710909, 0, 0, 't@gmail.com', 'rutupatel717@gmail.com', 0, 1, '2025-02-08 11:17:04', 0, '2025-02-08 11:17:04', 1),
(3, 'devanshi', 'global', 'surat', 15, 17, 8, 58, 360002, 98710909, 0, 1, 't@gmail.com', 'rutupatel717@gmail.com', 0, 1, '2025-02-07 15:15:38', 1, '2025-02-07 15:15:38', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_district_master`
--

CREATE TABLE `tbl_district_master` (
  `district_id` int(11) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `city_id` int(10) NOT NULL,
  `state_id` int(10) NOT NULL,
  `country_id` int(50) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(50) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_district_master`
--

INSERT INTO `tbl_district_master` (`district_id`, `district_name`, `city_id`, `state_id`, `country_id`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(15, 'northern', 17, 8, 58, '2025-01-01 12:04:45', 16, '2025-01-01 12:04:45', 16),
(16, 'golden templee', 18, 9, 59, '2025-01-01 12:05:44', 16, '2025-01-01 12:27:30', 6),
(19, 'biharr', 18, 9, 59, '2025-01-28 12:06:48', 6, '2025-01-28 12:06:48', 6),
(21, 'rajkot', 17, 9, 59, '2025-01-29 10:16:49', 6, '2025-01-29 10:16:49', 6),
(22, 'rajkot', 18, 9, 59, '2025-01-30 16:59:29', 6, '2025-01-30 16:59:29', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_floor_master`
--

CREATE TABLE `tbl_floor_master` (
  `floor_id` int(11) NOT NULL,
  `floor` varchar(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_floor_master`
--

INSERT INTO `tbl_floor_master` (`floor_id`, `floor`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(2, '4', '2025-01-01 14:29:50', 16, '2025-01-01 14:29:50', 16),
(3, '12', '2025-01-01 14:30:49', 16, '2025-01-28 12:32:26', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_itemunit_mapping_master`
--

CREATE TABLE `tbl_itemunit_mapping_master` (
  `itemunit_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_itemunit_mapping_master`
--

INSERT INTO `tbl_itemunit_mapping_master` (`itemunit_id`, `item_id`, `unit_id`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(8, 13, 7, '2025-01-01 16:28:40', 16, '2025-01-01 16:33:27', 16),
(11, 14, 8, '2025-01-01 16:33:51', 16, '2025-01-01 16:33:51', 16),
(12, 10, 7, '2025-01-01 16:36:31', 16, '2025-01-28 12:45:14', 6),
(15, 7, 8, '2025-01-01 16:39:18', 16, '2025-01-02 10:40:02', 16),
(17, 13, 7, '2025-01-02 11:23:24', 16, '2025-01-02 11:23:24', 16),
(18, 7, 8, '2025-01-02 11:23:54', 16, '2025-01-02 11:23:54', 16);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_master`
--

CREATE TABLE `tbl_item_master` (
  `item_id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `gst` varchar(10) NOT NULL,
  `market_rate` int(10) NOT NULL,
  `life_item` int(50) NOT NULL,
  `status` varchar(10) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_master`
--

INSERT INTO `tbl_item_master` (`item_id`, `item_name`, `gst`, `market_rate`, `life_item`, `status`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(7, 'iphone', 'gst_applic', 101, 10, '1', '2025-01-01 12:40:40', 6, '2025-01-01 12:40:40', 6),
(8, 'laptop', 'gst_exempt', 12, 7, '1', '2025-01-01 12:41:29', 6, '2025-01-01 12:41:29', 6),
(13, 'bike', 'gst_exempt', 150, 15, '1', '2025-01-01 12:47:01', 6, '2025-01-28 12:26:07', 6),
(14, 'watch', 'gst_applic', 12, 7, '1', '2025-01-01 12:51:03', 16, '2025-01-01 12:51:03', 16),
(15, 'ipad', 'gst_applic', 12, 12, '1', '2025-01-01 12:51:30', 16, '2025-01-01 12:51:30', 16),
(17, 'mobile', 'gst_applic', 10, 11, '0', '2025-01-28 12:24:41', 6, '2025-01-28 12:24:41', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_item_unit_master`
--

CREATE TABLE `tbl_item_unit_master` (
  `item_unit_id` int(11) NOT NULL,
  `item_id` int(10) NOT NULL,
  `unit_id` int(10) NOT NULL,
  `item` varchar(100) NOT NULL,
  `unit` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(50) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_item_unit_master`
--

INSERT INTO `tbl_item_unit_master` (`item_unit_id`, `item_id`, `unit_id`, `item`, `unit`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(85, 7, 8, '', '', '2025-01-01 16:23:17', 16, '2025-01-01 16:23:17', 16),
(125, 10, 7, '', '', '2025-01-02 11:12:40', 16, '2025-01-02 11:12:40', 16),
(138, 9, 7, '', '', '2025-01-28 12:45:43', 6, '2025-01-28 12:45:43', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_location_master`
--

CREATE TABLE `tbl_location_master` (
  `location_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `chamber_id` int(100) NOT NULL,
  `floor_id` int(100) NOT NULL,
  `rack_id` int(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_location_master`
--

INSERT INTO `tbl_location_master` (`location_id`, `customer_id`, `chamber_id`, `floor_id`, `rack_id`, `location`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(8, 6, 8, 2, 2, 'hetanshreee - drashti - 4 - abcd', '2024-12-26 17:30:20', 6, '2025-01-28 13:06:42', 6),
(14, 4, 7, 3, 2, 'drashti - hetanshree - 10 - abcd', '2025-01-01 14:37:09', 16, '2025-01-01 14:37:09', 16);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_rack_master`
--

CREATE TABLE `tbl_rack_master` (
  `rack_id` int(11) NOT NULL,
  `rack` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_rack_master`
--

INSERT INTO `tbl_rack_master` (`rack_id`, `rack`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(2, 'abcd', '2025-01-01 13:00:28', 16, '2025-01-01 13:00:28', 16);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_state_master`
--

CREATE TABLE `tbl_state_master` (
  `state_id` int(11) NOT NULL,
  `state_name` varchar(100) NOT NULL,
  `country_id` varchar(100) NOT NULL,
  `created_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(50) NOT NULL,
  `modified_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_state_master`
--

INSERT INTO `tbl_state_master` (`state_id`, `state_name`, `country_id`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(8, 'victoria', '58', '2025-01-01 06:29:33', 16, '2025-01-01 06:29:33', 16),
(9, 'punjabb', '59', '2025-01-01 06:30:17', 16, '2025-01-01 06:36:29', 16),
(10, 'goaa', '56', '2025-01-01 06:36:51', 6, '2025-01-01 06:56:58', 6);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_unit_master`
--

CREATE TABLE `tbl_unit_master` (
  `unit_id` int(11) NOT NULL,
  `unit` varchar(100) NOT NULL,
  `conversion_factor` int(100) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(50) NOT NULL,
  `modified_date` datetime NOT NULL DEFAULT current_timestamp(),
  `modified_by` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_unit_master`
--

INSERT INTO `tbl_unit_master` (`unit_id`, `unit`, `conversion_factor`, `created_date`, `created_by`, `modified_date`, `modified_by`) VALUES
(7, '90kg', 198, '2025-01-01 12:47:29', 6, '2025-01-28 12:27:50', 6),
(8, '200kg', 441, '2025-01-01 12:47:59', 16, '2025-01-01 12:47:59', 16);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user_master`
--

CREATE TABLE `tbl_user_master` (
  `id` int(50) NOT NULL,
  `username` varchar(150) NOT NULL,
  `password` varchar(150) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_user_master`
--

INSERT INTO `tbl_user_master` (`id`, `username`, `password`, `name`, `status`) VALUES
(1, 'hetasvi', '$2y$10$ceVRiivzILNdCYRWJWEQiOlg32VnDBrN7gPLF.zffk2Tl3jcxfxKu', 'hetu', 1),
(5, 'hets', 'hetasvii', 'hetasvii', 1),
(6, 'hetu', '123', 'hetasvi', 1),
(18, 'riya', 'Riya Patel', 'Riya Patel', 1),
(19, 'hetu', '1234', 'hetu', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  ADD PRIMARY KEY (`chamber_id`);

--
-- Indexes for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  ADD PRIMARY KEY (`city_id`);

--
-- Indexes for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `tbl_customer_contact_detail`
--
ALTER TABLE `tbl_customer_contact_detail`
  ADD PRIMARY KEY (`customer_contact_detail_id`);

--
-- Indexes for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `tbl_district_master`
--
ALTER TABLE `tbl_district_master`
  ADD PRIMARY KEY (`district_id`);

--
-- Indexes for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  ADD PRIMARY KEY (`floor_id`);

--
-- Indexes for table `tbl_itemunit_mapping_master`
--
ALTER TABLE `tbl_itemunit_mapping_master`
  ADD PRIMARY KEY (`itemunit_id`);

--
-- Indexes for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `tbl_item_unit_master`
--
ALTER TABLE `tbl_item_unit_master`
  ADD PRIMARY KEY (`item_unit_id`);

--
-- Indexes for table `tbl_location_master`
--
ALTER TABLE `tbl_location_master`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `tbl_rack_master`
--
ALTER TABLE `tbl_rack_master`
  ADD PRIMARY KEY (`rack_id`);

--
-- Indexes for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `tbl_unit_master`
--
ALTER TABLE `tbl_unit_master`
  ADD PRIMARY KEY (`unit_id`);

--
-- Indexes for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbl_chamber_master`
--
ALTER TABLE `tbl_chamber_master`
  MODIFY `chamber_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_city_master`
--
ALTER TABLE `tbl_city_master`
  MODIFY `city_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_country_master`
--
ALTER TABLE `tbl_country_master`
  MODIFY `country_id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `tbl_customer_contact_detail`
--
ALTER TABLE `tbl_customer_contact_detail`
  MODIFY `customer_contact_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_customer_master`
--
ALTER TABLE `tbl_customer_master`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `tbl_district_master`
--
ALTER TABLE `tbl_district_master`
  MODIFY `district_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tbl_floor_master`
--
ALTER TABLE `tbl_floor_master`
  MODIFY `floor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_itemunit_mapping_master`
--
ALTER TABLE `tbl_itemunit_mapping_master`
  MODIFY `itemunit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_item_master`
--
ALTER TABLE `tbl_item_master`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_item_unit_master`
--
ALTER TABLE `tbl_item_unit_master`
  MODIFY `item_unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=139;

--
-- AUTO_INCREMENT for table `tbl_location_master`
--
ALTER TABLE `tbl_location_master`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_rack_master`
--
ALTER TABLE `tbl_rack_master`
  MODIFY `rack_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_state_master`
--
ALTER TABLE `tbl_state_master`
  MODIFY `state_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_unit_master`
--
ALTER TABLE `tbl_unit_master`
  MODIFY `unit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbl_user_master`
--
ALTER TABLE `tbl_user_master`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
