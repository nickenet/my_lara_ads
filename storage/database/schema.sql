-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2016 at 07:49 AM
-- Server version: 5.5.42
-- PHP Version: 5.6.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laraclassified`
--

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>ads`
--

DROP TABLE IF EXISTS `<<prefix>>ads`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ads` (
  `id` int(10) unsigned NOT NULL,
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `user_id` int(10) unsigned DEFAULT NULL,
  `category_id` int(10) unsigned NOT NULL COMMENT 'Sub-categories Only',
  `ad_type_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `price` float DEFAULT NULL COMMENT 'Price or Salary',
  `negotiable` tinyint(3) unsigned DEFAULT '0',
  `resume` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Category: Job Search',
  `new` tinyint(3) unsigned DEFAULT '0' COMMENT 'Category Dependance',
  `brand` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Category Dependance',
  `seller_name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `seller_email` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `seller_phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `seller_phone_hidden` tinyint(3) unsigned DEFAULT '0',
  `address` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `lat` float DEFAULT '0' COMMENT 'Latitude',
  `lon` float DEFAULT '0' COMMENT 'Longitude',
  `pack_id` int(10) unsigned DEFAULT NULL COMMENT 'Package ID',
  `ip_addr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visits` int(10) unsigned DEFAULT '0',
  `activation_token` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '0',
  `reviewed` tinyint(3) unsigned DEFAULT '0',
  `archived` tinyint(3) unsigned DEFAULT '0',
  `fb_profile` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>advertising`
--

DROP TABLE IF EXISTS `<<prefix>>advertising`;
CREATE TABLE IF NOT EXISTS `<<prefix>>advertising` (
  `id` int(10) unsigned NOT NULL,
  `slug` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `provider_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tracking_code_large` text COLLATE utf8_unicode_ci,
  `tracking_code_medium` text COLLATE utf8_unicode_ci,
  `tracking_code_small` text COLLATE utf8_unicode_ci,
  `active` tinyint(3) unsigned DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ad_type`
--

DROP TABLE IF EXISTS `<<prefix>>ad_type`;
CREATE TABLE IF NOT EXISTS `<<prefix>>ad_type` (
  `id` int(10) unsigned NOT NULL,
  `translation_lang` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `translation_of` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `active` tinyint(3) unsigned DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>blacklist`
--

DROP TABLE IF EXISTS `<<prefix>>blacklist`;
CREATE TABLE IF NOT EXISTS `<<prefix>>blacklist` (
  `id` int(10) unsigned NOT NULL,
  `type` enum('domain','email','ip','word') COLLATE utf8_unicode_ci DEFAULT NULL,
  `entry` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>cache`
--

DROP TABLE IF EXISTS `<<prefix>>cache`;
CREATE TABLE IF NOT EXISTS `<<prefix>>cache` (
  `key` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `value` text COLLATE utf8_unicode_ci,
  `expiration` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `<<prefix>>categories`;
CREATE TABLE IF NOT EXISTS `<<prefix>>categories` (
  `id` int(10) unsigned NOT NULL,
  `translation_lang` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `translation_of` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT '0',
  `name` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `slug` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `css_class` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `depth` int(10) unsigned DEFAULT NULL,
  `type` enum('classified','job-offer','job-search','service') COLLATE utf8_unicode_ci DEFAULT 'classified' COMMENT 'Only select this for parent categories',
  `active` tinyint(3) unsigned DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `<<prefix>>cities`;
CREATE TABLE IF NOT EXISTS `<<prefix>>cities` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'ISO-3166 2-letter country code, 2 characters',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'name of geographical point (utf8) varchar(200)',
  `asciiname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'name of geographical point in plain ascii characters, varchar(200)',
  `longitude` float DEFAULT NULL COMMENT 'longitude in decimal degrees (wgs84)',
  `latitude` float DEFAULT NULL COMMENT 'latitude in decimal degrees (wgs84)',
  `feature_class` char(1) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'see http://www.geonames.org/export/codes.html, char(1)',
  `feature_code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'see http://www.geonames.org/export/codes.html, varchar(10)',
  `subadmin1_code` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'fipscode (subject to change to iso code), see exceptions below, see file admin1Codes.txt for display names of this code; varchar(20)',
  `subadmin2_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'code for the second administrative division, a county in the US, see file admin2Codes.txt; varchar(80)',
  `population` bigint(20) DEFAULT NULL COMMENT 'bigint (4 byte int) ',
  `time_zone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'the timezone id (see file timeZone.txt)',
  `active` tinyint(3) unsigned DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>continents`
--

DROP TABLE IF EXISTS `<<prefix>>continents`;
CREATE TABLE IF NOT EXISTS `<<prefix>>continents` (
  `id` int(10) unsigned NOT NULL,
  `code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `active` tinyint(3) unsigned DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>countries`
--

DROP TABLE IF EXISTS `<<prefix>>countries`;
CREATE TABLE IF NOT EXISTS `<<prefix>>countries` (
  `id` int(11) unsigned NOT NULL,
  `code` char(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `iso3` char(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `iso_numeric` int(10) unsigned DEFAULT NULL,
  `fips` char(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asciiname` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `capital` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area` int(10) unsigned DEFAULT NULL,
  `population` int(10) unsigned DEFAULT NULL,
  `continent_code` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tld` char(4) COLLATE utf8_unicode_ci DEFAULT NULL,
  `currency_code` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code_format` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal_code_regex` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `languages` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `neighbours` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `equivalent_fips_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>currencies`
--

DROP TABLE IF EXISTS `<<prefix>>currencies`;
CREATE TABLE IF NOT EXISTS `<<prefix>>currencies` (
  `id` int(10) unsigned NOT NULL,
  `code` varchar(3) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `html_entity` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'From Github : An array of currency symbols as HTML entities',
  `font_arial` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `font_code2000` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unicode_decimal` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `unicode_hex` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL,
  `in_left` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>gender`
--

DROP TABLE IF EXISTS `<<prefix>>gender`;
CREATE TABLE IF NOT EXISTS `<<prefix>>gender` (
  `id` int(11) unsigned NOT NULL,
  `translation_lang` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `translation_of` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>languages`
--

DROP TABLE IF EXISTS `<<prefix>>languages`;
CREATE TABLE IF NOT EXISTS `<<prefix>>languages` (
  `id` int(10) unsigned NOT NULL,
  `abbr` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `locale` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `native` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `flag` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `app_name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `script` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `default` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>messages`
--

DROP TABLE IF EXISTS `<<prefix>>messages`;
CREATE TABLE IF NOT EXISTS `<<prefix>>messages` (
  `id` int(10) unsigned NOT NULL,
  `ad_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `message` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `filename` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'File attach (e.i. resume)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>migrations`
--

DROP TABLE IF EXISTS `<<prefix>>migrations`;
CREATE TABLE IF NOT EXISTS `<<prefix>>migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>packs`
--

DROP TABLE IF EXISTS `<<prefix>>packs`;
CREATE TABLE IF NOT EXISTS `<<prefix>>packs` (
  `id` int(10) unsigned NOT NULL,
  `translation_lang` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `translation_of` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'In country language',
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'In country language',
  `price` float unsigned DEFAULT NULL,
  `currency_code` varchar(3) COLLATE utf8_unicode_ci DEFAULT NULL,
  `duration` int(10) unsigned DEFAULT '30' COMMENT 'In days',
  `parent_id` int(10) unsigned DEFAULT NULL,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `depth` int(10) unsigned DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>pages`
--

DROP TABLE IF EXISTS `<<prefix>>pages`;
CREATE TABLE IF NOT EXISTS `<<prefix>>pages` (
  `id` int(10) unsigned NOT NULL,
  `translation_lang` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `translation_of` int(10) unsigned DEFAULT NULL,
  `type` enum('page','term','help') COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `slug` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8_unicode_ci,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `depth` int(10) unsigned DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>password_resets`
--

DROP TABLE IF EXISTS `<<prefix>>password_resets`;
CREATE TABLE IF NOT EXISTS `<<prefix>>password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>payments`
--

DROP TABLE IF EXISTS `<<prefix>>payments`;
CREATE TABLE IF NOT EXISTS `<<prefix>>payments` (
  `id` int(10) unsigned NOT NULL,
  `ad_id` int(10) unsigned DEFAULT NULL,
  `pack_id` int(10) unsigned DEFAULT NULL,
  `payment_method_id` int(10) unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>payment_methods`
--

DROP TABLE IF EXISTS `<<prefix>>payment_methods`;
CREATE TABLE IF NOT EXISTS `<<prefix>>payment_methods` (
  `id` int(10) unsigned NOT NULL,
  `country_code` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT '',
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `depth` int(10) unsigned DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>permissions`
--

DROP TABLE IF EXISTS `<<prefix>>permissions`;
CREATE TABLE IF NOT EXISTS `<<prefix>>permissions` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>permission_role`
--

DROP TABLE IF EXISTS `<<prefix>>permission_role`;
CREATE TABLE IF NOT EXISTS `<<prefix>>permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>pictures`
--

DROP TABLE IF EXISTS `<<prefix>>pictures`;
CREATE TABLE IF NOT EXISTS `<<prefix>>pictures` (
  `id` int(10) unsigned NOT NULL,
  `ad_id` int(10) unsigned DEFAULT NULL,
  `filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '1' COMMENT 'Set at 0 if ads is updated',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>report_type`
--

DROP TABLE IF EXISTS `<<prefix>>report_type`;
CREATE TABLE IF NOT EXISTS `<<prefix>>report_type` (
  `id` int(11) unsigned NOT NULL,
  `translation_lang` varchar(10) DEFAULT NULL,
  `translation_of` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>roles`
--

DROP TABLE IF EXISTS `<<prefix>>roles`;
CREATE TABLE IF NOT EXISTS `<<prefix>>roles` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>saved_ads`
--

DROP TABLE IF EXISTS `<<prefix>>saved_ads`;
CREATE TABLE IF NOT EXISTS `<<prefix>>saved_ads` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ad_id` int(10) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>saved_search`
--

DROP TABLE IF EXISTS `<<prefix>>saved_search`;
CREATE TABLE IF NOT EXISTS `<<prefix>>saved_search` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `keyword` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'To show',
  `query` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `count` int(10) unsigned DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>sessions`
--

DROP TABLE IF EXISTS `<<prefix>>sessions`;
CREATE TABLE IF NOT EXISTS `<<prefix>>sessions` (
  `id` varchar(11) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `payload` text COLLATE utf8_unicode_ci,
  `last_activity` int(10) unsigned DEFAULT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(250) COLLATE utf8_unicode_ci DEFAULT '',
  `user_agent` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>settings`
--

DROP TABLE IF EXISTS `<<prefix>>settings`;
CREATE TABLE IF NOT EXISTS `<<prefix>>settings` (
  `id` int(10) unsigned NOT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `field` text COLLATE utf8_unicode_ci NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `lft` int(10) unsigned DEFAULT NULL,
  `rgt` int(10) unsigned DEFAULT NULL,
  `depth` int(10) unsigned DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>subadmin1`
--

DROP TABLE IF EXISTS `<<prefix>>subadmin1`;
CREATE TABLE IF NOT EXISTS `<<prefix>>subadmin1` (
  `id` int(10) unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `asciiname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>subadmin2`
--

DROP TABLE IF EXISTS `<<prefix>>subadmin2`;
CREATE TABLE IF NOT EXISTS `<<prefix>>subadmin2` (
  `id` int(10) unsigned NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asciiname` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>time_zones`
--

DROP TABLE IF EXISTS `<<prefix>>time_zones`;
CREATE TABLE IF NOT EXISTS `<<prefix>>time_zones` (
  `id` int(11) unsigned NOT NULL,
  `country_code` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `time_zone_id` varchar(40) COLLATE utf8_unicode_ci DEFAULT '',
  `gmt` float DEFAULT NULL,
  `dst` float DEFAULT NULL,
  `raw` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>users`
--

DROP TABLE IF EXISTS `<<prefix>>users`;
CREATE TABLE IF NOT EXISTS `<<prefix>>users` (
  `id` int(11) unsigned NOT NULL,
  `country_code` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_type_id` int(10) unsigned DEFAULT NULL,
  `gender_id` int(10) unsigned DEFAULT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `about` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone_hidden` tinyint(3) unsigned DEFAULT '0',
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password` varchar(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_admin` tinyint(3) unsigned DEFAULT '0',
  `comments_enabled` tinyint(3) unsigned DEFAULT '0',
  `receive_newsletter` tinyint(3) unsigned DEFAULT '1',
  `receive_advice` tinyint(3) unsigned DEFAULT '1',
  `ip_addr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `provider` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'facebook, google, twitter, ...',
  `provider_id` int(10) unsigned DEFAULT NULL COMMENT 'Provider User ID',
  `activation_token` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '1',
  `blocked` tinyint(3) unsigned DEFAULT '0',
  `closed` tinyint(3) unsigned DEFAULT '0',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `<<prefix>>user_type`
--

DROP TABLE IF EXISTS `<<prefix>>user_type`;
CREATE TABLE IF NOT EXISTS `<<prefix>>user_type` (
  `id` tinyint(3) unsigned NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `active` tinyint(3) unsigned DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `<<prefix>>ads`
--
ALTER TABLE `<<prefix>>ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `country_code` (`country_code`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `ad_type_id` (`ad_type_id`),
  ADD KEY `title` (`title`),
  ADD KEY `seller_name` (`seller_name`),
  ADD KEY `address` (`address`),
  ADD KEY `city_id` (`city_id`),
  ADD KEY `lat` (`lat`,`lon`),
  ADD KEY `active` (`active`),
  ADD KEY `reviewed` (`reviewed`);

--
-- Indexes for table `<<prefix>>advertising`
--
ALTER TABLE `<<prefix>>advertising`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `<<prefix>>ad_type`
--
ALTER TABLE `<<prefix>>ad_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translation_lang` (`translation_lang`),
  ADD KEY `translation_of` (`translation_of`);

--
-- Indexes for table `<<prefix>>blacklist`
--
ALTER TABLE `<<prefix>>blacklist`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`,`entry`);

--
-- Indexes for table `<<prefix>>cache`
--
ALTER TABLE `<<prefix>>cache`
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `<<prefix>>categories`
--
ALTER TABLE `<<prefix>>categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translation_lang` (`translation_lang`),
  ADD KEY `translation_of` (`translation_of`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Indexes for table `<<prefix>>cities`
--
ALTER TABLE `<<prefix>>cities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_code` (`country_code`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `<<prefix>>continents`
--
ALTER TABLE `<<prefix>>continents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `<<prefix>>countries`
--
ALTER TABLE `<<prefix>>countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `<<prefix>>currencies`
--
ALTER TABLE `<<prefix>>currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `<<prefix>>gender`
--
ALTER TABLE `<<prefix>>gender`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translation_lang` (`translation_lang`),
  ADD KEY `translation_of` (`translation_of`);

--
-- Indexes for table `<<prefix>>languages`
--
ALTER TABLE `<<prefix>>languages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `abbr` (`abbr`);

--
-- Indexes for table `<<prefix>>messages`
--
ALTER TABLE `<<prefix>>messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `<<prefix>>packs`
--
ALTER TABLE `<<prefix>>packs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `<<prefix>>pages`
--
ALTER TABLE `<<prefix>>pages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type` (`type`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `translation_lang` (`translation_lang`),
  ADD KEY `translation_of` (`translation_of`);

--
-- Indexes for table `<<prefix>>payments`
--
ALTER TABLE `<<prefix>>payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_id` (`ad_id`),
  ADD KEY `pack_id` (`pack_id`),
  ADD KEY `payment_method_id` (`payment_method_id`);

--
-- Indexes for table `<<prefix>>payment_methods`
--
ALTER TABLE `<<prefix>>payment_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `<<prefix>>permissions`
--
ALTER TABLE `<<prefix>>permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_unique` (`name`);

--
-- Indexes for table `<<prefix>>permission_role`
--
ALTER TABLE `<<prefix>>permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_role_id_foreign` (`role_id`);

--
-- Indexes for table `<<prefix>>pictures`
--
ALTER TABLE `<<prefix>>pictures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ad_id` (`ad_id`);

--
-- Indexes for table `<<prefix>>report_type`
--
ALTER TABLE `<<prefix>>report_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `translation_lang` (`translation_lang`),
  ADD KEY `translation_of` (`translation_of`);

--
-- Indexes for table `<<prefix>>roles`
--
ALTER TABLE `<<prefix>>roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `<<prefix>>saved_ads`
--
ALTER TABLE `<<prefix>>saved_ads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ad_id` (`ad_id`);

--
-- Indexes for table `<<prefix>>saved_search`
--
ALTER TABLE `<<prefix>>saved_search`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `<<prefix>>sessions`
--
ALTER TABLE `<<prefix>>sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `<<prefix>>settings`
--
ALTER TABLE `<<prefix>>settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `key` (`key`);

--
-- Indexes for table `<<prefix>>subadmin1`
--
ALTER TABLE `<<prefix>>subadmin1`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `<<prefix>>subadmin2`
--
ALTER TABLE `<<prefix>>subadmin2`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `name` (`name`);

--
-- Indexes for table `<<prefix>>time_zones`
--
ALTER TABLE `<<prefix>>time_zones`
  ADD PRIMARY KEY (`id`),
  ADD INDEX(`country_code`),
  ADD UNIQUE(`time_zone_id`);

--
-- Indexes for table `<<prefix>>users`
--
ALTER TABLE `<<prefix>>users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `country_code` (`country_code`),
  ADD KEY `gender_id` (`gender_id`),
  ADD KEY `user_type_id` (`user_type_id`);

--
-- Indexes for table `<<prefix>>user_type`
--
ALTER TABLE `<<prefix>>user_type`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `<<prefix>>ads`
--
ALTER TABLE `<<prefix>>ads`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>advertising`
--
ALTER TABLE `<<prefix>>advertising`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>ad_type`
--
ALTER TABLE `<<prefix>>ad_type`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>blacklist`
--
ALTER TABLE `<<prefix>>blacklist`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>categories`
--
ALTER TABLE `<<prefix>>categories`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>continents`
--
ALTER TABLE `<<prefix>>continents`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>countries`
--
ALTER TABLE `<<prefix>>countries`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>currencies`
--
ALTER TABLE `<<prefix>>currencies`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>gender`
--
ALTER TABLE `<<prefix>>gender`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>languages`
--
ALTER TABLE `<<prefix>>languages`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>messages`
--
ALTER TABLE `<<prefix>>messages`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>packs`
--
ALTER TABLE `<<prefix>>packs`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>pages`
--
ALTER TABLE `<<prefix>>pages`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>payments`
--
ALTER TABLE `<<prefix>>payments`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>payment_methods`
--
ALTER TABLE `<<prefix>>payment_methods`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>permissions`
--
ALTER TABLE `<<prefix>>permissions`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>pictures`
--
ALTER TABLE `<<prefix>>pictures`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>report_type`
--
ALTER TABLE `<<prefix>>report_type`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>roles`
--
ALTER TABLE `<<prefix>>roles`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>saved_ads`
--
ALTER TABLE `<<prefix>>saved_ads`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>saved_search`
--
ALTER TABLE `<<prefix>>saved_search`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>settings`
--
ALTER TABLE `<<prefix>>settings`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>subadmin1`
--
ALTER TABLE `<<prefix>>subadmin1`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>subadmin2`
--
ALTER TABLE `<<prefix>>subadmin2`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>time_zones`
--
ALTER TABLE `<<prefix>>time_zones`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>users`
--
ALTER TABLE `<<prefix>>users`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `<<prefix>>user_type`
--
ALTER TABLE `<<prefix>>user_type`
  MODIFY `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT;
  
  
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
