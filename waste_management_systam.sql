-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2026 at 07:43 PM
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
-- Database: `waste_management_systam`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = active, 1 = trash',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

CREATE TABLE `cities` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL,
  `short_name` varchar(255) DEFAULT NULL,
  `state_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `name`, `short_name`, `state_id`) VALUES
(31273, 'Barkhan', 'BKH', 2723),
(31274, 'Bela', 'BLA', 2723),
(31275, 'Bhag', 'BHG', 2723),
(31276, 'Chaman', 'CHM', 2723),
(31277, 'Chitkan', 'CTK', 2723),
(31278, 'Dalbandin', 'DLB', 2723),
(31279, 'Dera Allah Yar', 'DAY', 2723),
(31280, 'Dera Bugti', 'DBG', 2723),
(31281, 'Dera Murad Jamali', 'DMJ', 2723),
(31282, 'Dhadar', 'DHD', 2723),
(31283, 'Duki', 'DUK', 2723),
(31284, 'Gaddani', 'GDN', 2723),
(31285, 'Gwadar', 'GWD', 2723),
(31286, 'Harnai', 'HRN', 2723),
(31287, 'Hub', 'HUB', 2723),
(31288, 'Jiwani', 'JWN', 2723),
(31289, 'Kalat', 'KLT', 2723),
(31290, 'Kharan', 'KRN', 2723),
(31291, 'Khuzdar', 'KZD', 2723),
(31292, 'Kohlu', 'KHL', 2723),
(31293, 'Loralai', 'LRL', 2723),
(31294, 'Mach', 'MCH', 2723),
(31295, 'Mastung', 'MST', 2723),
(31296, 'Nushki', 'NSK', 2723),
(31297, 'Ormara', 'ORM', 2723),
(31298, 'Pasni', 'PSN', 2723),
(31299, 'Pishin', 'PSH', 2723),
(31300, 'Quetta', 'QTA', 2723),
(31301, 'Sibi', 'SBI', 2723),
(31302, 'Sohbatpur', 'SHT', 2723),
(31303, 'Surab', 'SUR', 2723),
(31304, 'Turbat', 'TRB', 2723),
(31305, 'Usta Muhammad', 'UMM', 2723),
(31306, 'Uthal', 'UTH', 2723),
(31307, 'Wadh', 'WAD', 2723),
(31308, 'Winder', 'WND', 2723),
(31309, 'Zehri', 'ZHR', 2723),
(31310, 'Zhob', 'ZHB', 2723),
(31311, 'Ziarat', 'ZRT', 2723),
(31312, '\'Abdul Hakim', 'AHK', 2728),
(31313, 'Ahmadpur East', 'APE', 2728),
(31314, 'Ahmadpur Lumma', 'APL', 2728),
(31315, 'Ahmadpur Sial', 'APS', 2728),
(31316, 'Ahmedabad', 'AMD', 2728),
(31317, 'Alipur', 'ALP', 2728),
(31318, 'Alipur Chatha', 'ALC', 2728),
(31319, 'Arifwala', 'ARF', 2728),
(31320, 'Attock', 'ATT', 2728),
(31321, 'Baddomalhi', 'BDM', 2728),
(31322, 'Bagh', 'BAG', 2728),
(31323, 'Bahawalnagar', 'BHG', 2728),
(31324, 'Bahawalpur', 'BNR', 2728),
(31325, 'Bai Pheru', 'BPH', 2728),
(31326, 'Basirpur', 'BSP', 2728),
(31327, 'Begowala', 'BGW', 2728),
(31328, 'Bhakkar', 'BKR', 2728),
(31329, 'Bhalwal', 'BHW', 2728),
(31330, 'Bhawana', 'BWA', 2728),
(31331, 'Bhera', 'BHR', 2728),
(31332, 'Bhopalwala', 'BPL', 2728),
(31333, 'Burewala', 'BRW', 2728),
(31334, 'Chak Azam Sahu', 'CAS', 2728),
(31335, 'Chak Jhumra', 'CJM', 2728),
(31336, 'Chak Sarwar Shahid', 'CSS', 2728),
(31337, 'Chakwal', 'CWK', 2728),
(31338, 'Chawinda', 'CHW', 2728),
(31339, 'Chichawatni', 'CHC', 2728),
(31340, 'Chiniot', 'CNI', 2728),
(31341, 'Chishtian Mandi', 'CHM', 2728),
(31342, 'Choa Saidan Shah', 'CSS', 2728),
(31343, 'Chuhar Kana', 'CHK', 2728),
(31344, 'Chunian', 'CHN', 2728),
(31345, 'Dajal', 'DJL', 2728),
(31346, 'Darya Khan', 'DYK', 2728),
(31347, 'Daska', 'DSK', 2728),
(31348, 'Daud Khel', 'DKL', 2728),
(31349, 'Daultala', 'DLT', 2728),
(31350, 'Dera Din Panah', 'DDP', 2728),
(31351, 'Dera Ghazi Khan', 'DGK', 2728),
(31352, 'Dhanote', 'DNT', 2728),
(31353, 'Dhonkal', 'DHN', 2728),
(31354, 'Dijkot', 'DJK', 2728),
(31355, 'Dina', 'DNA', 2728),
(31356, 'Dinga', 'DNG', 2728),
(31357, 'Dipalpur', 'DPL', 2728),
(31358, 'Dullewala', 'DLW', 2728),
(31359, 'Dunga Bunga', 'DBU', 2728),
(31360, 'Dunyapur', 'DNY', 2728),
(31361, 'Eminabad', 'EMB', 2728),
(31362, 'Faisalabad', 'FSD', 2728),
(31363, 'Faqirwali', 'FQL', 2728),
(31364, 'Faruka', 'FRK', 2728),
(31365, 'Fateh Jang', 'FTJ', 2728),
(31366, 'Fatehpur', 'FTP', 2728),
(31367, 'Fazalpur', 'FZP', 2728),
(31368, 'Ferozwala', 'FRW', 2728),
(31369, 'Fort Abbas', 'FAB', 2728),
(31370, 'Garh Maharaja', 'GMR', 2728),
(31371, 'Ghakar', 'GHK', 2728),
(31372, 'Ghurgushti', 'GGS', 2728),
(31373, 'Gojra', 'GJR', 2728),
(31374, 'Gujar Khan', 'GKH', 2728),
(31375, 'Gujranwala', 'GRW', 2728),
(31376, 'Gujrat', 'GJT', 2728),
(31377, 'Hadali', 'HDL', 2728),
(31378, 'Hafizabad', 'HFZ', 2728),
(31379, 'Harnoli', 'HRL', 2728),
(31380, 'Harunabad', 'HRB', 2728),
(31381, 'Hasan Abdal', 'HAD', 2728),
(31382, 'Hasilpur', 'HSP', 2728),
(31383, 'Haveli', 'HVL', 2728),
(31384, 'Hazro', 'HZR', 2728),
(31385, 'Hujra Shah Muqim', 'HSM', 2728),
(31386, 'Isa Khel', 'ISK', 2728),
(31387, 'Jahanian', 'JHN', 2728),
(31388, 'Jalalpur Bhattian', 'JBB', 2728),
(31389, 'Jalalpur Jattan', 'JJJ', 2728),
(31390, 'Jalalpur Pirwala', 'JPP', 2728),
(31391, 'Jalla Jeem', 'JLM', 2728),
(31392, 'Jamke Chima', 'JKC', 2728),
(31393, 'Jampur', 'JMP', 2728),
(31394, 'Jand', 'JND', 2728),
(31395, 'Jandanwala', 'JNW', 2728),
(31396, 'Jandiala Sherkhan', 'JSH', 2728),
(31397, 'Jaranwala', 'JRW', 2728),
(31398, 'Jatoi', 'JTO', 2728),
(31399, 'Jauharabad', 'JHR', 2728),
(31400, 'Jhang', 'JHG', 2728),
(31401, 'Jhawarian', 'JWR', 2728),
(31402, 'Jhelum', 'JLM', 2728),
(31403, 'Kabirwala', 'KBR', 2728),
(31404, 'Kahna Nau', 'KNN', 2728),
(31405, 'Kahror Pakka', 'KPK', 2728),
(31406, 'Kahuta', 'KHT', 2728),
(31407, 'Kalabagh', 'KLB', 2728),
(31408, 'Kalaswala', 'KLS', 2728),
(31409, 'Kaleke', 'KLK', 2728),
(31410, 'Kalur Kot', 'KLK', 2728),
(31411, 'Kamalia', 'KML', 2728),
(31412, 'Kamar Mashani', 'KMS', 2728),
(31413, 'Kamir', 'KMR', 2728),
(31414, 'Kamoke', 'KMK', 2728),
(31415, 'Kamra', 'KMR', 2728),
(31416, 'Kanganpur', 'KGP', 2728),
(31417, 'Karampur', 'KRP', 2728),
(31418, 'Karor Lal Esan', 'KLE', 2728),
(31419, 'Kasur', 'KSR', 2728),
(31420, 'Khairpur Tamewali', 'KNL', 2728),
(31421, 'Khanewal', 'KDG', 2728),
(31422, 'Khangah Dogran', 'KDG', 2728),
(31423, 'Khangarh', 'KNG', 2728),
(31424, 'Khanpur', 'KPR', 2728),
(31425, 'Kharian', 'KHR', 2728),
(31426, 'Khewra', 'KHW', 2728),
(31427, 'Khundian', 'KHD', 2728),
(31428, 'Khurianwala', 'KWA', 2728),
(31429, 'Khushab', 'KSB', 2728),
(31430, 'Kot Abdul Malik', 'KAM', 2728),
(31431, 'Kot Addu', 'KAD', 2728),
(31432, 'Kot Mithan', 'KMT', 2728),
(31433, 'Kot Moman', 'KMM', 2728),
(31434, 'Kot Radha Kishan', 'KRK', 2728),
(31435, 'Kot Samaba', 'KSB', 2728),
(31436, 'Kotli Loharan', 'KLH', 2728),
(31437, 'Kundian', 'KDN', 2728),
(31438, 'Kunjah', 'KNJ', 2728),
(31439, 'Lahore', 'LHE', 2728),
(31440, 'Lalamusa', 'LLM', 2728),
(31441, 'Lalian', 'LLN', 2728),
(31442, 'Liaqatabad', 'LQB', 2728),
(31443, 'Liaqatpur', 'LQT', 2728),
(31444, 'Lieah', 'LEH', 2728),
(31445, 'Liliani', 'LLI', 2728),
(31446, 'Lodhran', 'LDR', 2728),
(31447, 'Ludhewala Waraich', 'LWW', 2728),
(31448, 'Mailsi', 'MLS', 2728),
(31449, 'Makhdumpur', 'MKP', 2728),
(31450, 'Makhdumpur Rashid', 'MKR', 2728),
(31451, 'Malakwal', 'MLK', 2728),
(31452, 'Mamu Kanjan', 'MKK', 2728),
(31453, 'Mananwala Jodh Singh', 'MJW', 2728),
(31454, 'Mandi Bahauddin', 'MDB', 2728),
(31455, 'Mandi Sadiq Ganj', 'MSG', 2728),
(31456, 'Mangat', 'MGT', 2728),
(31457, 'Mangla', 'MGL', 2728),
(31458, 'Mankera', 'MKR', 2728),
(31459, 'Mian Channun', 'MCN', 2728),
(31460, 'Miani', 'MNI', 2728),
(31461, 'Mianwali', 'MWL', 2728),
(31462, 'Minchinabad', 'MCH', 2728),
(31463, 'Mitha Tiwana', 'MTW', 2728),
(31464, 'Multan', 'MLT', 2728),
(31465, 'Muridke', 'MRK', 2728),
(31466, 'Murree', 'MRR', 2728),
(31467, 'Mustafabad', 'MTF', 2728),
(31468, 'Muzaffargarh', 'MZG', 2728),
(31469, 'Nankana Sahib', 'NKS', 2728),
(31470, 'Narang', 'NRG', 2728),
(31471, 'Narowal', 'NRW', 2728),
(31472, 'Noorpur Thal', 'NPT', 2728),
(31473, 'Nowshera', 'NWS', 2728),
(31474, 'Nowshera Virkan', 'NVR', 2728),
(31475, 'Okara', 'OKR', 2728),
(31476, 'Pakpattan', 'PKP', 2728),
(31477, 'Pasrur', 'PSR', 2728),
(31478, 'Pattoki', 'PTK', 2728),
(31479, 'Phalia', 'PHL', 2728),
(31480, 'Phularwan', 'PHW', 2728),
(31481, 'Pind Dadan Khan', 'PDK', 2728),
(31482, 'Pindi Bhattian', 'PBH', 2728),
(31483, 'Pindi Gheb', 'PGB', 2728),
(31484, 'Pirmahal', 'PMH', 2728),
(31485, 'Qadirabad', 'QDB', 2728),
(31486, 'Qadirpur Ran', 'QPR', 2728),
(31487, 'Qila Disar Singh', 'QDS', 2728),
(31488, 'Qila Sobha Singh', 'QSS', 2728),
(31489, 'Quaidabad', 'QAD', 2728),
(31490, 'Rabwah', 'RWB', 2728),
(31491, 'Rahim Yar Khan', 'RYK', 2728),
(31492, 'Raiwind', 'RWD', 2728),
(31493, 'Raja Jang', 'RJN', 2728),
(31494, 'Rajanpur', 'RJP', 2728),
(31495, 'Rasulnagar', 'RSN', 2728),
(31496, 'Rawalpindi', 'RWP', 2728),
(31497, 'Renala Khurd', 'RLK', 2728),
(31498, 'Rojhan', 'RJH', 2728),
(31499, 'Saddar Gogera', 'SDG', 2728),
(31500, 'Sadiqabad', 'SDQ', 2728),
(31501, 'Safdarabad', 'SFD', 2728),
(31502, 'Sahiwal', 'SHW', 2728),
(31503, 'Samasatta', 'SMS', 2728),
(31504, 'Sambrial', 'SBR', 2728),
(31505, 'Sammundri', 'SMR', 2728),
(31506, 'Sangala Hill', 'SGH', 2728),
(31507, 'Sanjwal', 'SJW', 2728),
(31508, 'Sarai Alamgir', 'SAM', 2728),
(31509, 'Sarai Sidhu', 'SSU', 2728),
(31510, 'Sargodha', 'SRD', 2728),
(31511, 'Shadiwal', 'SDW', 2728),
(31512, 'Shahkot', 'SHK', 2728),
(31513, 'Shahpur City', 'SHC', 2728),
(31514, 'Shahpur Saddar', 'SHS', 2728),
(31515, 'Shakargarh', 'SKG', 2728),
(31516, 'Sharqpur', 'SHQ', 2728),
(31517, 'Shehr Sultan', 'SHS', 2728),
(31518, 'Shekhupura', 'SKP', 2728),
(31519, 'Shujaabad', 'SJB', 2728),
(31520, 'Sialkot', 'SLK', 2728),
(31521, 'Sillanwali', 'SLW', 2728),
(31522, 'Sodhra', 'SDR', 2728),
(31523, 'Sohawa', 'SHW', 2728),
(31524, 'Sukheke', 'SKH', 2728),
(31525, 'Talagang', 'TLG', 2728),
(31526, 'Tandlianwala', 'TWL', 2728),
(31527, 'Taunsa', 'TNS', 2728),
(31528, 'Taxila', 'TXL', 2728),
(31529, 'Tibba Sultanpur', 'TSP', 2728),
(31530, 'Toba Tek Singh', 'TTS', 2728),
(31531, 'Tulamba', 'TLB', 2728),
(31532, 'Uch', 'UCH', 2728),
(31533, 'Vihari', 'VIH', 2728),
(31534, 'Wah', 'WAH', 2728),
(31535, 'Warburton', 'WRB', 2728),
(31536, 'Wazirabad', 'WZD', 2728),
(31537, 'Yazman', 'YZM', 2728),
(31538, 'Zafarwal', 'ZFW', 2728),
(31539, 'Zahir Pir', 'ZHP', 2728);

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_no` varchar(20) DEFAULT NULL,
  `cnic_no` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT 0.00,
  `allowance` decimal(10,2) DEFAULT 0.00,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_image` varchar(255) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `security_no` varchar(255) DEFAULT NULL,
  `oldage_benefits` text DEFAULT NULL,
  `license_no` varchar(255) DEFAULT NULL,
  `employee_type` varchar(255) DEFAULT NULL,
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = active, 1 = trash',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `reason` text DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `account_no` varchar(255) DEFAULT NULL,
  `iban` varchar(255) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `name`, `email`, `phone_no`, `cnic_no`, `address`, `salary`, `allowance`, `city_id`, `employee_image`, `gender`, `date_of_birth`, `security_no`, `oldage_benefits`, `license_no`, `employee_type`, `trash`, `status`, `reason`, `bank_name`, `account_no`, `iban`, `education`, `owner_id`, `created_at`, `updated_at`) VALUES
(9, 'Muhammad Ali', 'ali@gmail.com', '0300 0000000', '31303-0000000-0', 'Rahim Yar Khan', 15000.00, 300.00, 31491, 'images/employee/1770659213_698a1d8d2c22d.jpg', 'male', NULL, '123456', 'Natus id porro recus', 'Labore aut anim ea d', 'Helper', 0, 'active', NULL, 'UBL', '1232', '1232', 'At nisi deleniti min', 1, '2026-02-09 23:46:53', '2026-02-09 23:46:53'),
(10, 'Nasir', 'nasir@gmail.com', '0300 0000000', '31303-0000000-1', 'Dolorem ipsum cupida', 15000.00, 1000.00, 31316, NULL, 'male', NULL, '12435', 'Consequatur Cupidit', '78676', 'Driver', 0, 'active', NULL, 'HBL', '8756', '8786', 'Matric', 6, '2026-02-10 00:51:56', '2026-02-14 15:39:02');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_type_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `amount`, `expense_type_id`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(4, 100.00, 6, 1, 0, 1, '2026-02-10 00:21:55', '2026-02-10 00:21:55'),
(5, 500.00, 8, 1, 0, 6, '2026-02-13 17:01:44', '2026-02-13 17:01:44'),
(6, 2000.00, 9, 1, 0, 6, '2026-02-14 15:53:42', '2026-02-14 15:53:42');

-- --------------------------------------------------------

--
-- Table structure for table `expense_types`
--

CREATE TABLE `expense_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `city_id` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_types`
--

INSERT INTO `expense_types` (`id`, `name`, `city_id`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(6, 'Electricity Bill', 31491, 1, 0, 1, '2026-02-10 00:20:55', '2026-02-10 00:20:55'),
(8, 'Rent', 31316, 1, 0, 6, '2026-02-13 16:19:39', '2026-02-13 16:19:39'),
(9, 'Car Rent', 31316, 1, 0, 6, '2026-02-14 15:52:34', '2026-02-14 15:52:34');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hospitals`
--

CREATE TABLE `hospitals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `ntn` varchar(255) DEFAULT NULL,
  `phc` varchar(255) DEFAULT NULL,
  `pra` varchar(255) DEFAULT NULL,
  `secp` varchar(255) DEFAULT NULL,
  `facilities` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `payment` decimal(10,2) NOT NULL DEFAULT 0.00,
  `number_of_beds` int(11) NOT NULL DEFAULT 0,
  `waste_generation_volume` varchar(255) DEFAULT NULL,
  `waste_type` varchar(255) DEFAULT NULL,
  `preferred_waste_collection_frequency` varchar(255) DEFAULT NULL,
  `has_temporary_waste_storage` enum('yes','no') NOT NULL DEFAULT 'no',
  `waste_storage_method` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hospitals`
--

INSERT INTO `hospitals` (`id`, `company_name`, `owner_name`, `email`, `registration_number`, `ntn`, `phc`, `pra`, `secp`, `facilities`, `address`, `city_id`, `contact_number`, `payment`, `number_of_beds`, `waste_generation_volume`, `waste_type`, `preferred_waste_collection_frequency`, `has_temporary_waste_storage`, `waste_storage_method`, `date`, `expiry_date`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(7, 'Navarro Bean LLC', 'Sajid', 'test@gmail.com', '441', '449', '44', '30', '289', 'hospital,Blood Bank,Diagnostic Center,Clinic', 'Fugit accusamus vol', 31491, '0300 0000000', 10000.00, 486, '26', 'Infectious Waste,Pathological Waste', 'weekly 2 time', 'yes', 'Containers,Yellow Room', '2026-02-09', '2026-06-09', 1, 0, 1, '2026-02-10 00:25:47', '2026-02-12 13:46:08'),
(8, 'Wiggins and Burks Co', 'Veronica French', 'sydeva@mailinator.com', '442', '255', '842', '858', NULL, 'laboratory,Diagnostic Center,Polyclinic', 'Possimus sed maiore', 31316, '398', 3456.00, 401, '8', 'Pathological Waste,Pharmaceutical Waste', 'weekly 2 time', 'no', 'Bags,Containers', '2026-02-14', '2026-02-14', 1, 0, 6, '2026-02-14 15:37:11', '2026-02-14 15:37:55');

-- --------------------------------------------------------

--
-- Table structure for table `hospital_agreements`
--

CREATE TABLE `hospital_agreements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hospital_id` bigint(20) UNSIGNED DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `number_of_beds` int(11) NOT NULL DEFAULT 0,
  `waste_generation_volume` varchar(255) DEFAULT NULL,
  `waste_type` varchar(255) DEFAULT NULL,
  `preferred_waste_collection_frequency` varchar(255) DEFAULT NULL,
  `has_temporary_waste_storage` enum('yes','no') NOT NULL DEFAULT 'no',
  `waste_storage_method` varchar(255) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `document` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `receipt_no` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `hospital_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tax` double(10,2) NOT NULL DEFAULT 0.00,
  `tax_status` int(11) DEFAULT 0,
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `date`, `receipt_no`, `amount`, `hospital_id`, `employee_id`, `tax`, `tax_status`, `owner_id`, `created_at`, `updated_at`) VALUES
(12, '2026-02-09', '1', 10000.00, 7, 9, 1600.00, 1, 1, '2026-02-10 00:31:02', '2026-02-10 00:31:02'),
(13, '2026-02-10', '2', 10000.00, 7, 9, 0.00, NULL, 1, '2026-02-10 19:40:59', '2026-02-12 12:47:05'),
(14, '2026-02-11', '3', 10000.00, 7, 10, 1600.00, 1, 1, '2026-02-11 18:25:53', '2026-02-11 18:25:53'),
(15, '2026-02-14', '4', 3456.00, 8, 10, 0.00, NULL, 6, '2026-02-14 15:51:03', '2026-02-14 15:51:03');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_14_125949_create_employees_table', 1),
(5, '2026_01_15_074125_create_categories_table', 1),
(6, '2026_01_15_120443_create_expense_types_table', 1),
(7, '2026_01_15_125203_create_expense_table', 1),
(8, '2026_01_16_102919_create_salaries_table', 1),
(9, '2026_01_16_124145_create_hospitals_table', 1),
(10, '2026_01_16_144313_create_hospital_agreements_table', 1),
(11, '2026_01_18_143636_create_invoices_table', 2),
(12, '2026_01_19_101531_create_wastes_table', 3),
(13, '2026_01_21_094259_create_user_permissions_table', 4),
(14, '2026_01_23_103049_create_settings_table', 5),
(15, '2026_01_26_112423_create_waste_disposals_table', 6),
(16, '2026_01_30_135619_create_waste_disposals_table', 7),
(17, '2026_01_31_081726_create_waste_burn_table', 8),
(18, '2026_02_04_104746_create_products_table', 9),
(19, '2026_02_04_114804_create_purchases_table', 10),
(20, '2026_02_04_120633_create_total_purchases_table', 10),
(21, '2026_02_05_121619_create_sales_table', 11),
(22, '2026_02_05_121744_create_total_sales_table', 11),
(23, '2026_02_06_140721_create_total_rent_table', 12),
(24, '2026_02_06_140741_create_rent_table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `product_mode` enum('sale','rent') NOT NULL COMMENT 'sale = for selling, rent = for renting',
  `price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `quantity`, `product_mode`, `price`, `owner_id`, `status`, `trash`, `created_at`, `updated_at`) VALUES
(4, 'Mask', 480, 'sale', 30.00, 1, 1, 0, '2026-02-10 00:37:46', '2026-02-10 00:37:46'),
(6, 'Tonometer', 1, 'rent', 500.00, 1, 1, 0, '2026-02-10 00:45:16', '2026-02-10 00:45:16'),
(7, 'Gil Doyle', 1, 'sale', 315.00, 6, 1, 0, '2026-02-14 16:14:24', '2026-02-14 16:14:24');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `total_purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `total_purchase_id`, `product_id`, `quantity`, `purchase_price`, `total`, `status`, `trash`, `created_at`, `updated_at`) VALUES
(10, 6, 4, 2, 30.00, 60.00, 1, 0, '2026-02-10 00:41:38', '2026-02-10 00:41:38'),
(11, 7, 7, 1, 315.00, 315.00, 1, 0, '2026-02-14 16:31:35', '2026-02-14 16:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `rents`
--

CREATE TABLE `rents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `total_rent_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `rent_price` decimal(8,2) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rents`
--

INSERT INTO `rents` (`id`, `total_rent_id`, `product_id`, `rent_price`, `quantity`, `total`, `end_date`, `created_at`, `updated_at`) VALUES
(3, 2, 6, 500.00, 1, 500.00, '2026-02-17', '2026-02-10 00:45:40', '2026-02-16 21:37:54'),
(4, 3, 6, 500.00, 1, 500.00, '2026-03-18', '2026-02-16 21:29:58', '2026-02-16 21:29:58');

-- --------------------------------------------------------

--
-- Table structure for table `salaries`
--

CREATE TABLE `salaries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `allowance` decimal(10,2) DEFAULT 0.00,
  `bonus` decimal(10,2) DEFAULT 0.00,
  `date` date DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `salaries`
--

INSERT INTO `salaries` (`id`, `employee_id`, `amount`, `allowance`, `bonus`, `date`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(20, 9, 10000.00, 500.00, NULL, '2026-02-09', 1, 0, 1, '2026-02-09 23:50:52', '2026-02-09 23:50:52'),
(21, 10, 2000.00, 1000.00, NULL, '2026-02-04', 1, 0, 6, '2026-02-10 00:53:45', '2026-02-12 19:35:42');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `total_sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `total_sale_id`, `product_id`, `quantity`, `sale_price`, `total`, `status`, `trash`, `created_at`, `updated_at`) VALUES
(5, 4, 4, 1, 30.00, 30.00, 1, 0, '2026-02-10 00:42:17', '2026-02-10 00:42:17'),
(6, 5, 7, 1, 315.00, 315.00, 1, 0, '2026-02-14 16:20:00', '2026-02-14 16:20:00'),
(10, 6, 4, 1, 30.00, 30.00, 1, 0, '2026-02-16 15:49:56', '2026-02-16 15:49:56'),
(11, 6, 7, 3, 315.00, 945.00, 1, 0, '2026-02-16 15:49:56', '2026-02-16 15:49:56'),
(12, 6, 4, 1, 30.00, 30.00, 1, 0, '2026-02-16 15:49:56', '2026-02-16 15:49:56');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0tg7QbAzerJfuwA34kiUAtUb99KPzTRHwue7Xmgo', NULL, '16.144.17.106', 'Mozilla/5.0 (iPad; CPU OS 13_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) CriOS/76.0.3809.81 Mobile/15E148 Safari/605.1', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0ZuaWJhRmF5SFVMM3hVZFdFUlVpNWxCYWxsSUdnWmlTdEJTTEJtbSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vd3d3LmJpby5jem9uZXBrLmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771258587),
('AqATS7Wkucs0c3vkz0JMs0dVm9wzvc9Eo0NgYF3o', NULL, '149.57.180.4', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiOEZ1TGQwbVZXYWhMQ3ZwV05JVE5qcU9xaG9XVlZSbDZVYnFINjhwSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vd3d3LmJpby5jem9uZXBrLmNvbS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzM6Imh0dHBzOi8vd3d3LmJpby5jem9uZXBrLmNvbS9hZG1pbiI7fX0=', 1771247066),
('dslY5LXzvHOdVwWTcXAZRPtMdfmUr4Ypkv3NUMaH', 1, '103.203.47.45', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTmhzRnhVNmg5eGI4SDQxZUN4bHZ5Z0tjVGJqZHBEU2xidDBqdjBJcSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzk6Imh0dHBzOi8vYmlvLmN6b25lcGsuY29tL2FkbWluL3Byb2ZpbGUvMSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1771257670),
('gUGpBpaiownyByFHNXP2sf7Mc2bhBAvnBIb9dEBE', NULL, '54.162.53.4', 'Mozilla/5.0 (X11; Linux i686; rv:124.0) Gecko/20100101 Firefox/124.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibFZqZml5bHcxdzZZNGVWTm9DeHZ5b0FxUmNndWpSeUFZZVRyWDZ3WSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMzoiaHR0cHM6Ly93d3cuYmlvLmN6b25lcGsuY29tL2FkbWluIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vd3d3LmJpby5jem9uZXBrLmNvbS9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771258244),
('JBVWhMAHwt5Gvyw30Xie4lEgUn3NvODmp5F1sZSY', NULL, '23.27.145.34', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZm1FSHlvb3BhY2tib29EaDZDdVFNVkk4OXRSNDY2MXprU2JZVG9kTiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjM6Imh0dHBzOi8vYmlvLmN6b25lcGsuY29tIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1771247065),
('KvFgxe9EMlywpt7VtFTYyJjk4S2gBd9whO263VxD', NULL, '23.27.145.57', 'Mozilla/5.0 (X11; Linux i686; rv:109.0) Gecko/20100101 Firefox/120.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWEp1STl0VEg0dlhnMUltVUdsSWRUdHhTNlRuOVNVcmlEOU5uUGdidiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vYmlvLmN6b25lcGsuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyOToiaHR0cHM6Ly9iaW8uY3pvbmVway5jb20vYWRtaW4iO319', 1771260816),
('TMy5ZthnjA5AIjaEh30GZmo9WzYhtDvzKJJzdZOr', NULL, '54.162.53.4', 'Mozilla/5.0 (X11; Linux i686; rv:124.0) Gecko/20100101 Firefox/124.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNzRteTBvRGY2M2R5UXpJWmhTSGtVaFQ4aFNCRkpmODNSMjgxRjBrRSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vd3d3LmJpby5jem9uZXBrLmNvbSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771258244),
('xN3c480K0S6bxSrbqr1ywtW55SAmmADPBrJrKskn', NULL, '54.162.53.4', 'Mozilla/5.0 (X11; Linux i686; rv:124.0) Gecko/20100101 Firefox/124.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiajNZM1k4RHp3QnRZSHJIdk00OHhyajBBblYxSFFWNXFLYW9xY3FvTCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHBzOi8vd3d3LmJpby5jem9uZXBrLmNvbS9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1771258245),
('YGqzMMOShDy9r5Zv2gxFbyEYiehcgNyv2lNgoKTw', NULL, '122.129.86.208', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQWhkV0dST1FnVFk3ZnBuRHV3b0dVQ1FTRVlzUHBFblNLWWRscVgyOSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHBzOi8vYmlvLmN6b25lcGsuY29tL2xvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyOToiaHR0cHM6Ly9iaW8uY3pvbmVway5jb20vYWRtaW4iO319', 1771266822);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_name` varchar(255) DEFAULT NULL,
  `hospital_name` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `ntn` varchar(255) DEFAULT NULL,
  `pra` varchar(255) DEFAULT NULL,
  `secp` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `bank_account_holder` varchar(255) DEFAULT NULL,
  `bank_qr_code` varchar(255) DEFAULT NULL,
  `phone_account_name` varchar(255) DEFAULT NULL,
  `phone_account_no` varchar(255) DEFAULT NULL,
  `phone_account_holder` varchar(255) DEFAULT NULL,
  `phone_qr_code` varchar(255) DEFAULT NULL,
  `tax` varchar(255) DEFAULT NULL,
  `salary_limit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `site_name`, `hospital_name`, `logo`, `email`, `phone`, `address`, `ntn`, `pra`, `secp`, `bank_name`, `bank_account_no`, `bank_account_holder`, `bank_qr_code`, `phone_account_name`, `phone_account_no`, `phone_account_holder`, `phone_qr_code`, `tax`, `salary_limit`, `created_at`, `updated_at`) VALUES
(1, 'Hospital Mnagment', 'Biomed waste management company SMC Private Limited', 'images/setting/1769851334_697dc9c66d3ff.jpeg', NULL, '0333000003', 'Rahim Yar Khan', '1232324', '76767', '3434', 'UBL', '34343', 'Abu Yousha', 'images/setting/1769421368_69773a38dede2.png', 'Jazz Cash', '5436', 'Abu Yousha', 'images/setting/1769421377_69773a41bc9f9.png', '16', 40000.00, NULL, '2026-01-31 06:30:24');

-- --------------------------------------------------------

--
-- Table structure for table `total_purchases`
--

CREATE TABLE `total_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `total_purchases`
--

INSERT INTO `total_purchases` (`id`, `total`, `purchase_date`, `note`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(6, 60.00, '2026-02-09', NULL, 1, 0, NULL, '2026-02-10 00:41:38', '2026-02-10 00:41:38'),
(7, 315.00, '2026-02-14', NULL, 1, 0, 6, '2026-02-14 16:31:35', '2026-02-14 16:31:35');

-- --------------------------------------------------------

--
-- Table structure for table `total_rents`
--

CREATE TABLE `total_rents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `hospital_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive, 2=expired',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `total_rents`
--

INSERT INTO `total_rents` (`id`, `hospital_id`, `start_date`, `end_date`, `total`, `note`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(2, 7, '2026-02-09', '2026-02-25', 500.00, NULL, 1, 0, NULL, '2026-02-10 00:45:40', '2026-02-10 00:45:40'),
(3, 7, '2026-02-16', NULL, 500.00, NULL, 1, 0, NULL, '2026-02-16 21:29:58', '2026-02-16 21:29:58');

-- --------------------------------------------------------

--
-- Table structure for table `total_sales`
--

CREATE TABLE `total_sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `total` decimal(8,2) DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `hospital_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `total_sales`
--

INSERT INTO `total_sales` (`id`, `total`, `sale_date`, `note`, `status`, `trash`, `hospital_id`, `owner_id`, `created_at`, `updated_at`) VALUES
(4, 30.00, '2026-02-09', NULL, 1, 0, 7, NULL, '2026-02-10 00:42:17', '2026-02-10 00:42:17'),
(5, 315.00, '2026-02-14', NULL, 1, 0, 8, 6, '2026-02-14 16:20:00', '2026-02-14 16:20:00'),
(6, 1005.00, '2026-02-16', 'ok', 1, 0, 7, NULL, '2026-02-16 15:49:29', '2026-02-16 15:49:56');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `plan_password` text DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city_ids` text DEFAULT NULL,
  `ntn` varchar(255) DEFAULT NULL,
  `pra` varchar(255) DEFAULT NULL,
  `secp` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `bank_account_no` varchar(255) DEFAULT NULL,
  `bank_qr_code` varchar(255) DEFAULT NULL,
  `bank_account_holder` varchar(255) DEFAULT NULL,
  `phone_account_name` varchar(255) DEFAULT NULL,
  `phone_account_no` varchar(255) DEFAULT NULL,
  `phone_qr_code` varchar(255) DEFAULT NULL,
  `phone_account_holder` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(255) DEFAULT '1',
  `parent_id` int(11) DEFAULT NULL,
  `trash` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `email_verified_at`, `password`, `plan_password`, `phone`, `address`, `city_ids`, `ntn`, `pra`, `secp`, `bank_name`, `bank_account_no`, `bank_qr_code`, `bank_account_holder`, `phone_account_name`, `phone_account_no`, `phone_qr_code`, `phone_account_holder`, `remember_token`, `role`, `parent_id`, `trash`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad', 'Bilal', 'admin@gmail.com', NULL, '$2y$12$0AFWbMxYFn8JYw2kEIBg9u/GgqXP7oPl8tw.FGZdTwyEH4d1q/kdW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Admin', NULL, 0, '2026-01-19 08:15:42', '2026-02-16 17:10:57'),
(6, 'Doruk', 'Pektas', 'dorukpekt4s11@gmail.com', NULL, '$2y$12$OH8w/4KDaNxVOPQqV9jEuOZSBmALUnQu93totdvzqCa2GlnYggILm', 'eyJpdiI6IjVxR0ZveXgxU1REd05naTl0b3BXaEE9PSIsInZhbHVlIjoibnFIUjZua1FqZWNVaHJiQWVOSTJDQT09IiwibWFjIjoiODgxOGQ4MWIwNDE0YTcwNzFkZjBlMjZhZDBmMWIzZTAwMmQ4Yjk2NDQ0NmQ4YjU1MDNmOWFkM2I1NGYwNmYyNSIsInRhZyI6IiJ9', '0333000003', 'Impedit proident s', '31313,31316,31491', '1232324', '76767', '3434', 'UBL', '34343', '/tmp/phprgs7psjf9djjbtiC8W0', 'Abu Yousha', 'Jazz Cash', '5436', '/tmp/phpi6uce18spt8a6pTnFbV', 'Abu Yousha', NULL, 'Manager', NULL, 0, '2026-02-09 21:00:36', '2026-02-10 20:52:32'),
(7, 'Reed', 'Wilder', 'peqodim@mailinator.com', NULL, '$2y$12$1fCuvgu6c0I4avS51KfP9uB6XjxwDakmWCQdU/z4JAC2Dj2caq4zi', 'eyJpdiI6IkRDVkgyamZzNXhvSEVZWTVoM29OZ2c9PSIsInZhbHVlIjoid1RvR2FUcis3OTcvQWtxd09mRG9qdz09IiwibWFjIjoiN2UxMDJlYzcwYzY3YTI5ODkyMzA1NDM4MjI4NjYwZTlkYjhhZGQ4MGNmNTQxMzczNTVkNDFhZDkzNTJiNGExZCIsInRhZyI6IiJ9', '+1 (768) 249-8452', 'Eos eveniet repudi', '31314,31315,31317,31318', 'Provident cumque su', '283', '776', 'Jaden Hess', 'Explicabo Aperiam v', NULL, 'Harum vel quidem sap', 'Ivory Fields', '+1 (508) 819-1414', NULL, '+1 (641) 947-9881', NULL, 'Manager', NULL, 0, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(9, 'Ali', 'Chan', 'ali@gmail.com', NULL, '$2y$12$5hM00D8lviI3iOWQtCIL0eft32KzrtTfTPunIZlTOoC9qCXehMT2.', 'eyJpdiI6Inh2Y0FQYnV0a2NnU2pTSnZEWWxCU1E9PSIsInZhbHVlIjoiRGVqb3MxN3p6NEdwbFJuUzBLcmZ1UT09IiwibWFjIjoiNjRhNDIwMWYwNTkzMmQ2Yzg0ZjMyMGYwMTE3YzI1NzkyOTg1ZGQ2NjVhOTc4ZjNmNjMzOWQ4ZjYwMDk5MjgzMSIsInRhZyI6IiJ9', '+1 (435) 447-5547', 'Earum dignissimos au', '31316', 'Odio quia consectetu', '166', '927', 'Irma Cameron', 'Voluptas porro duis', NULL, 'Molestiae quo velit', 'Colette Perry', '+1 (176) 817-4446', NULL, '+1 (499) 348-6287', NULL, 'Employee', 6, 0, '2026-02-12 17:50:08', '2026-02-14 15:42:27'),
(10, 'Hassan', 'Ali', 'hassan@gmail.com', NULL, '$2y$12$Xecmkm3ViT3EmIcCXCZlBOt2HNpo9V/8ZPnF6xADoHrHarnxjR9gS', 'eyJpdiI6Im8zUG9mSlRub3F4enFKeURPZkdjRHc9PSIsInZhbHVlIjoicEhNSXdLUC9vZGt1cGVnVGVKRjYyUT09IiwibWFjIjoiOGQwNzFiMGVkMTVjNTA1MzhiMzhlNzg0NWI3YjIxNDM5MjhlZmU3MWU2ZjE4ODE0NWY0MzUzNTcwMTcyNDY4OCIsInRhZyI6IiJ9', '0333000003', 'Lahore , Pakistan', '31276,31277,31318,31319,31328', '1232324', '76767', '3434', 'UBL', '34343', '/tmp/phph232pg3k7omacQmUkQq', 'Abu Yousha', NULL, NULL, NULL, NULL, NULL, 'Manager', 1, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `page` varchar(255) NOT NULL,
  `can_view` tinyint(1) NOT NULL DEFAULT 0,
  `can_insert` tinyint(1) NOT NULL DEFAULT 0,
  `can_edit` tinyint(1) NOT NULL DEFAULT 0,
  `can_delete` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `page`, `can_view`, `can_insert`, `can_edit`, `can_delete`, `created_at`, `updated_at`) VALUES
(36, 6, 'employee', 1, 1, 1, 1, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(37, 6, 'expense_type', 1, 0, 1, 0, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(38, 6, 'expense', 1, 0, 1, 0, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(39, 6, 'salary', 1, 1, 1, 1, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(40, 6, 'company', 1, 1, 1, 1, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(41, 6, 'invoice', 1, 1, 1, 1, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(42, 6, 'waste', 1, 1, 1, 1, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(43, 6, 'waste_disposals', 1, 1, 1, 1, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(44, 6, 'waste_burn', 1, 1, 1, 1, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(45, 6, 'expense_report', 1, 0, 0, 0, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(46, 6, 'invoice_report', 1, 0, 0, 0, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(47, 6, 'waste_report', 1, 0, 0, 0, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(48, 6, 'salary_report', 1, 0, 0, 0, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(49, 6, 'general_report', 1, 0, 0, 0, '2026-02-09 21:00:36', '2026-02-14 17:11:29'),
(50, 7, 'employee', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(51, 7, 'expense_type', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(52, 7, 'expense', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(53, 7, 'salary', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(54, 7, 'company', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(55, 7, 'invoice', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(56, 7, 'waste', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(57, 7, 'waste_disposals', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(58, 7, 'waste_burn', 1, 1, 1, 1, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(59, 7, 'expense_report', 1, 0, 0, 0, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(60, 7, 'invoice_report', 1, 0, 0, 0, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(61, 7, 'waste_report', 1, 0, 0, 0, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(62, 7, 'salary_report', 1, 0, 0, 0, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(63, 7, 'general_report', 1, 0, 0, 0, '2026-02-09 21:22:24', '2026-02-09 21:22:24'),
(64, 6, 'users', 1, 1, 1, 1, '2026-02-12 16:36:05', '2026-02-14 17:11:29'),
(65, 9, 'employee', 1, 1, 1, 1, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(66, 9, 'expense_type', 1, 1, 1, 1, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(67, 9, 'expense', 1, 1, 1, 1, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(68, 9, 'salary', 1, 1, 1, 1, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(69, 9, 'company', 1, 1, 1, 1, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(70, 9, 'invoice', 1, 1, 1, 1, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(71, 9, 'waste', 1, 1, 1, 0, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(72, 9, 'users', 1, 1, 0, 0, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(73, 9, 'expense_report', 1, 0, 0, 0, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(74, 9, 'invoice_report', 1, 0, 0, 0, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(75, 9, 'waste_report', 1, 0, 0, 0, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(76, 9, 'salary_report', 1, 0, 0, 0, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(77, 9, 'general_report', 1, 0, 0, 0, '2026-02-12 17:50:08', '2026-02-14 15:45:46'),
(78, 6, 'product', 1, 0, 0, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(79, 6, 'product_purchase', 1, 0, 1, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(80, 6, 'product_sale', 1, 0, 1, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(81, 6, 'product_rent', 1, 1, 1, 1, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(82, 6, 'sale_report', 1, 0, 0, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(83, 6, 'purchase_report', 1, 0, 0, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(84, 6, 'rent_report', 1, 0, 0, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(85, 6, 'burn_waste_report', 1, 0, 0, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(86, 6, 'waste_disposal_report', 1, 0, 0, 0, '2026-02-14 13:42:59', '2026-02-14 17:11:29'),
(87, 9, 'product', 1, 1, 1, 1, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(88, 9, 'product_purchase', 1, 1, 1, 1, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(89, 9, 'product_sale', 1, 1, 1, 1, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(90, 9, 'product_rent', 1, 1, 1, 1, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(91, 9, 'waste_disposals', 1, 1, 1, 1, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(92, 9, 'waste_burn', 1, 1, 1, 1, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(93, 9, 'sale_report', 1, 0, 0, 0, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(94, 9, 'purchase_report', 1, 0, 0, 0, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(95, 9, 'rent_report', 1, 0, 0, 0, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(96, 9, 'burn_waste_report', 1, 0, 0, 0, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(97, 9, 'waste_disposal_report', 1, 0, 0, 0, '2026-02-14 15:42:27', '2026-02-14 15:45:46'),
(98, 10, 'employee', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(99, 10, 'expense_type', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(100, 10, 'expense', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(101, 10, 'salary', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(102, 10, 'company', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(103, 10, 'invoice', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(104, 10, 'product', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(105, 10, 'product_purchase', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(106, 10, 'product_sale', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(107, 10, 'product_rent', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(108, 10, 'waste', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(109, 10, 'waste_disposals', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(110, 10, 'waste_burn', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(111, 10, 'users', 1, 1, 1, 1, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(112, 10, 'expense_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(113, 10, 'sale_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(114, 10, 'purchase_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(115, 10, 'rent_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(116, 10, 'invoice_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(117, 10, 'waste_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(118, 10, 'burn_waste_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(119, 10, 'waste_disposal_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(120, 10, 'salary_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51'),
(121, 10, 'general_report', 1, 0, 0, 0, '2026-02-14 17:45:38', '2026-02-14 17:46:51');

-- --------------------------------------------------------

--
-- Table structure for table `wastes`
--

CREATE TABLE `wastes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `receipt_no` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city_id` int(11) DEFAULT NULL,
  `hospital_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_bags` int(11) DEFAULT NULL,
  `infections` decimal(8,2) DEFAULT NULL,
  `infections_bags` int(11) DEFAULT NULL,
  `pathological` decimal(10,2) DEFAULT NULL,
  `pathological_bags` int(11) DEFAULT NULL,
  `sharp` decimal(8,2) DEFAULT NULL,
  `sharp_bags` int(11) DEFAULT NULL,
  `chemical` decimal(10,2) DEFAULT NULL,
  `chemical_bags` int(11) DEFAULT NULL,
  `pharmaceutical` decimal(10,2) DEFAULT NULL,
  `pharmaceutical_bags` int(11) DEFAULT NULL,
  `total_weight` decimal(8,2) DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wastes`
--

INSERT INTO `wastes` (`id`, `date`, `receipt_no`, `address`, `city_id`, `hospital_id`, `total_bags`, `infections`, `infections_bags`, `pathological`, `pathological_bags`, `sharp`, `sharp_bags`, `chemical`, `chemical_bags`, `pharmaceutical`, `pharmaceutical_bags`, `total_weight`, `employee_id`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(7, '2026-02-09', '4543', NULL, 31491, 7, 127, 6.00, 2, 86.00, 16, 55.00, 81, 60.00, 27, 28.00, 1, 235.00, 9, 1, 0, 1, '2026-02-10 00:28:29', '2026-02-10 00:28:29'),
(8, '2026-02-14', '453', NULL, 31316, 8, 3, 5.00, 1, NULL, NULL, 10.00, 2, NULL, NULL, NULL, NULL, 15.00, 10, 1, 0, 6, '2026-02-14 15:39:39', '2026-02-14 15:39:39');

-- --------------------------------------------------------

--
-- Table structure for table `waste_burn`
--

CREATE TABLE `waste_burn` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `vehicle_no` varchar(255) DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `infections` decimal(8,2) DEFAULT NULL,
  `infections_bags` int(11) DEFAULT NULL,
  `sharp` decimal(8,2) DEFAULT NULL,
  `sharp_bags` int(11) DEFAULT NULL,
  `pathological` decimal(8,2) DEFAULT 0.00,
  `pathological_bags` int(11) DEFAULT NULL,
  `chemical` decimal(8,2) DEFAULT 0.00,
  `chemical_bags` int(11) DEFAULT NULL,
  `pharmaceutical` decimal(8,2) DEFAULT 0.00,
  `pharmaceutical_bags` int(11) DEFAULT NULL,
  `total_bags` int(11) DEFAULT NULL,
  `total_weight` decimal(8,2) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `waste_burn`
--

INSERT INTO `waste_burn` (`id`, `date`, `vehicle_no`, `city_id`, `infections`, `infections_bags`, `sharp`, `sharp_bags`, `pathological`, `pathological_bags`, `chemical`, `chemical_bags`, `pharmaceutical`, `pharmaceutical_bags`, `total_bags`, `total_weight`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(3, '2026-02-09', 'RAY1934', 31491, 89.00, 25, 86.00, 6, 40.00, 7, 7.00, 81, 20.00, 52, 171, 242.00, 1, 0, 1, '2026-02-10 00:34:38', '2026-02-16 15:46:30'),
(4, '2026-02-10', '12445', 31491, 89.00, 45, 15.00, 5, NULL, NULL, NULL, NULL, NULL, NULL, 50, 104.00, 1, 0, 1, '2026-02-10 20:02:59', '2026-02-10 20:02:59'),
(5, '2026-02-14', 'SED 245', 31316, 3.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 3.00, 1, 0, 6, '2026-02-14 15:50:25', '2026-02-14 15:50:25');

-- --------------------------------------------------------

--
-- Table structure for table `waste_disposals`
--

CREATE TABLE `waste_disposals` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `date` date DEFAULT NULL,
  `vehicle_no` varchar(255) DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `infections` decimal(8,2) DEFAULT NULL,
  `infections_bags` int(11) DEFAULT NULL,
  `sharp` decimal(8,2) DEFAULT NULL,
  `sharp_bags` int(11) DEFAULT NULL,
  `pathological` decimal(8,2) DEFAULT 0.00,
  `pathological_bags` int(11) DEFAULT NULL,
  `chemical` decimal(8,2) DEFAULT 0.00,
  `chemical_bags` int(11) DEFAULT NULL,
  `pharmaceutical` decimal(8,2) DEFAULT 0.00,
  `pharmaceutical_bags` int(11) DEFAULT NULL,
  `total_bags` int(11) DEFAULT NULL,
  `total_weight` decimal(8,2) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
  `trash` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=not deleted, 1=deleted',
  `owner_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `waste_disposals`
--

INSERT INTO `waste_disposals` (`id`, `date`, `vehicle_no`, `city_id`, `infections`, `infections_bags`, `sharp`, `sharp_bags`, `pathological`, `pathological_bags`, `chemical`, `chemical_bags`, `pharmaceutical`, `pharmaceutical_bags`, `total_bags`, `total_weight`, `status`, `trash`, `owner_id`, `created_at`, `updated_at`) VALUES
(3, '2026-02-09', 'RGP1213', 31491, 92.00, 29, 49.00, 60, 17.00, 76, 14.00, 14, 85.00, 49, 228, 257.00, 1, 0, 1, '2026-02-10 00:33:05', '2026-02-10 00:33:05'),
(4, '2026-02-10', '121321', 31491, 25.00, 9, 3.00, 2, NULL, NULL, NULL, NULL, NULL, NULL, 11, 28.00, 1, 0, 1, '2026-02-10 19:44:52', '2026-02-10 19:44:52'),
(5, '2026-02-14', 'SHE276', 31316, 5.00, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 5.00, 1, 0, 6, '2026-02-14 15:48:30', '2026-02-14 15:48:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employees_cnic_no_unique` (`cnic_no`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `hospitals`
--
ALTER TABLE `hospitals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hospital_agreements`
--
ALTER TABLE `hospital_agreements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_product_id_foreign` (`product_id`);

--
-- Indexes for table `rents`
--
ALTER TABLE `rents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `salaries`
--
ALTER TABLE `salaries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_product_id_foreign` (`product_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `total_purchases`
--
ALTER TABLE `total_purchases`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `total_rents`
--
ALTER TABLE `total_rents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `total_sales`
--
ALTER TABLE `total_sales`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_permissions_user_id_page_unique` (`user_id`,`page`);

--
-- Indexes for table `wastes`
--
ALTER TABLE `wastes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waste_burn`
--
ALTER TABLE `waste_burn`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `waste_disposals`
--
ALTER TABLE `waste_disposals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cities`
--
ALTER TABLE `cities`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31540;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expense_types`
--
ALTER TABLE `expense_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hospitals`
--
ALTER TABLE `hospitals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `hospital_agreements`
--
ALTER TABLE `hospital_agreements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rents`
--
ALTER TABLE `rents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `salaries`
--
ALTER TABLE `salaries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `total_purchases`
--
ALTER TABLE `total_purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `total_rents`
--
ALTER TABLE `total_rents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `total_sales`
--
ALTER TABLE `total_sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_permissions`
--
ALTER TABLE `user_permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `wastes`
--
ALTER TABLE `wastes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `waste_burn`
--
ALTER TABLE `waste_burn`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `waste_disposals`
--
ALTER TABLE `waste_disposals`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
