/*
 Navicat Premium Data Transfer

 Source Server         : MAMP
 Source Server Type    : MySQL
 Source Server Version : 50726
 Source Host           : localhost:3306
 Source Schema         : hello_work

 Target Server Type    : MySQL
 Target Server Version : 50726
 File Encoding         : 65001

 Date: 29/01/2021 03:04:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for jobs
-- ----------------------------
DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reception_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `referral_deadline` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'full-time, part-time',
  `hope` text COLLATE utf8mb4_unicode_ci,
  `recruiting_office_id` bigint(20) unsigned DEFAULT NULL,
  `wage` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `holiday` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `occupation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `job_description` text COLLATE utf8mb4_unicode_ci,
  `contract` text COLLATE utf8mb4_unicode_ci,
  `employment_period` text COLLATE utf8mb4_unicode_ci,
  `work_place` text COLLATE utf8mb4_unicode_ci,
  `private_car_commute` text COLLATE utf8mb4_unicode_ci,
  `possibility_of_transfer` text COLLATE utf8mb4_unicode_ci,
  `age` text COLLATE utf8mb4_unicode_ci,
  `educational_background` text COLLATE utf8mb4_unicode_ci,
  `required` text COLLATE utf8mb4_unicode_ci,
  `license` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for links
-- ----------------------------
DROP TABLE IF EXISTS `links`;
CREATE TABLE `links` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `link` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(4) DEFAULT '0',
  `state_code` tinyint(4) DEFAULT NULL,
  `job_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44500 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------
-- Table structure for recruiting_offices
-- ----------------------------
DROP TABLE IF EXISTS `recruiting_offices`;
CREATE TABLE `recruiting_offices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `office_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `office_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `home_page` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
