-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 19, 2023 at 11:31 AM
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
-- Database: `needValidation`
--

-- --------------------------------------------------------

--
-- Table structure for table `abilities`
--

CREATE TABLE `abilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `only_owned` tinyint(1) NOT NULL DEFAULT 0,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `scope` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `abilities`
--

INSERT INTO `abilities` (`id`, `name`, `title`, `entity_id`, `entity_type`, `only_owned`, `options`, `scope`, `created_at`, `updated_at`) VALUES
(1, 'abilities', 'Abilities Listing', NULL, NULL, 0, NULL, NULL, '2022-03-20 05:35:40', '2022-03-20 05:51:28'),
(2, 'create_ability', 'Create Ability', NULL, NULL, 0, NULL, NULL, '2022-03-20 05:52:10', '2022-03-20 05:52:52'),
(3, 'edit_ability', 'Edit Ability', NULL, NULL, 0, NULL, NULL, '2022-03-20 05:52:28', '2022-03-20 05:52:28'),
(4, 'roles', 'Roles Listing', NULL, NULL, 0, NULL, NULL, '2022-03-20 05:53:05', '2022-03-20 05:53:05'),
(5, 'create_role', 'Create Role', NULL, NULL, 0, NULL, NULL, '2022-03-20 05:54:22', '2022-03-20 05:54:22'),
(6, 'edit_role', 'Edit Role', NULL, NULL, 0, NULL, NULL, '2022-03-20 05:54:47', '2022-03-20 05:54:47'),
(7, 'dashboard', 'Dashboard', NULL, NULL, 0, NULL, NULL, '2022-03-20 08:21:30', '2022-03-20 08:21:30'),
(22, 'create_division', 'Create Division', NULL, NULL, 0, NULL, NULL, '2022-08-14 02:00:32', '2022-08-14 02:00:32'),
(23, 'edit_division', 'Edit Division', NULL, NULL, 0, NULL, NULL, '2022-08-14 02:00:54', '2022-08-14 02:00:54'),
(24, 'division_list', 'List Division', NULL, NULL, 0, NULL, NULL, '2022-08-14 02:01:27', '2022-08-14 02:01:27'),
(25, 'delete_division', 'Delete Division', NULL, NULL, 0, NULL, NULL, '2022-08-14 04:41:03', '2022-08-14 04:41:03'),
(26, 'location_list', 'Location List', NULL, NULL, 0, NULL, NULL, '2022-08-14 07:12:49', '2022-08-14 07:12:49'),
(27, 'create_location', 'Create Location', NULL, NULL, 0, NULL, NULL, '2022-08-14 07:13:01', '2022-08-14 07:13:01'),
(28, 'edit_location', 'Edit Location', NULL, NULL, 0, NULL, NULL, '2022-08-14 07:13:13', '2022-08-14 07:13:13'),
(29, 'delete_location', 'Delete Location', NULL, NULL, 0, NULL, NULL, '2022-08-14 07:13:29', '2022-08-14 07:13:29'),
(30, 'nv_service_list', 'NV Service List', NULL, NULL, 0, NULL, NULL, '2022-08-14 08:26:13', '2023-04-11 07:27:36'),
(31, 'create_nv_service', 'Create NV Service', NULL, NULL, 0, NULL, NULL, '2022-08-14 08:26:32', '2023-04-11 07:28:34'),
(32, 'edit_nv_service', 'Edit NV Service', NULL, NULL, 0, NULL, NULL, '2022-08-14 08:26:43', '2023-04-11 07:29:27'),
(33, 'delete_nv_service', 'Delete NV Service', NULL, NULL, 0, NULL, NULL, '2022-08-14 08:26:55', '2023-04-11 07:30:05'),
(34, 'create_vendor', 'Create Vendor', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:10:56', '2022-08-31 09:10:56'),
(35, 'vendor_list', 'Vendor List', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:11:09', '2022-08-31 09:11:09'),
(36, 'edit_vendors', 'Edit Vendor', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:11:20', '2022-08-31 09:11:20'),
(37, 'delete_vendors', 'Delete Vendor', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:11:31', '2022-08-31 09:11:31'),
(38, 'circle_list', 'Circle List', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:11:44', '2022-08-31 09:11:44'),
(39, 'create_circle', 'Create Circle', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:11:55', '2022-08-31 09:11:55'),
(40, 'edit_circle', 'Edit Circle', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:12:06', '2022-08-31 09:12:06'),
(41, 'delete_circle', 'Delete Circle', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:12:16', '2022-08-31 09:12:16'),
(42, 'department_list', 'Department List', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:12:28', '2022-08-31 09:12:28'),
(43, 'create_department', 'Create Department', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:12:39', '2022-08-31 09:12:39'),
(44, 'edit_department', 'Edit Department', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:12:53', '2022-08-31 09:12:53'),
(45, 'delete_department', 'Delete Department', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:13:11', '2022-08-31 09:13:11'),
(46, 'employee_list', 'Employee List', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:13:20', '2022-08-31 09:13:20'),
(47, 'create_employee', 'Create Employee', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:13:31', '2022-08-31 09:13:31'),
(48, 'edit_employee', 'Edit Employee', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:13:42', '2022-08-31 09:13:42'),
(49, 'delete_employee', 'Delete Employee', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:13:53', '2022-08-31 09:13:53'),
(50, 'asset_list', 'Asset List', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:14:04', '2022-08-31 09:14:04'),
(51, 'create_asset', 'Create Asset', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:14:18', '2022-08-31 09:14:18'),
(52, 'edit_asset', 'Edit Asset', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:14:28', '2022-08-31 09:14:28'),
(53, 'delete_asset', 'Delete Asset', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:14:38', '2022-08-31 09:14:38'),
(54, 'nv_material_list', 'NV Material List', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:14:49', '2023-04-11 05:41:48'),
(55, 'create_nv_material', 'Create NV Material', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:15:04', '2023-04-11 05:39:07'),
(56, 'edit_nv_material', 'Edit NV Material', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:15:15', '2023-04-11 05:39:49'),
(57, 'delete_nv_material', 'Delete NV Material', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:15:26', '2023-04-11 05:40:28'),
(58, 'create_itemreturn', 'Create Item Return', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:15:41', '2022-08-31 09:15:41'),
(59, 'itemreturn_list', 'Item Return List', NULL, NULL, 0, NULL, NULL, '2022-08-31 09:15:53', '2022-08-31 09:15:53'),
(60, 'create_NV', 'Create NV', NULL, NULL, 0, NULL, NULL, '2022-09-07 02:56:44', '2023-04-11 09:01:32'),
(61, 'NV_list', 'NV List', NULL, NULL, 0, NULL, NULL, '2022-09-07 02:58:24', '2023-04-11 09:00:37'),
(62, 'edit_NV', 'Edit NV', NULL, NULL, 0, NULL, NULL, '2022-09-07 02:59:50', '2023-04-11 09:02:07'),
(63, 'delete_NV', 'Delete NV', NULL, NULL, 0, NULL, NULL, '2022-09-07 03:00:07', '2023-04-11 09:02:27'),
(64, 'create_complaint', 'Manage Complaints', NULL, NULL, 0, NULL, NULL, '2022-09-07 04:25:36', '2022-09-07 04:25:36'),
(65, 'edit_complaint', 'Edit Complaints', NULL, NULL, 0, NULL, NULL, '2022-09-07 04:26:57', '2022-09-07 04:26:57'),
(66, 'complaint_list', 'List Complaints', NULL, NULL, 0, NULL, NULL, '2022-09-07 04:27:21', '2022-09-07 04:27:21'),
(67, 'qrCode', 'Qr Code', NULL, NULL, 0, NULL, NULL, '2022-09-21 06:12:38', '2022-09-21 06:12:38'),
(68, 'createGatePass', 'GatePass', NULL, NULL, 0, NULL, NULL, '2022-09-21 06:13:14', '2022-09-21 06:13:14'),
(69, 'reports_list', 'reports', NULL, NULL, 0, NULL, NULL, '2023-01-10 07:05:48', '2023-01-10 07:05:48'),
(70, 'manage_division', 'Manage Division', NULL, NULL, 0, NULL, NULL, '2023-03-15 05:34:22', '2023-03-15 05:34:22'),
(71, 'listlocation', 'Listing Location', NULL, NULL, 0, NULL, NULL, '2023-03-15 07:18:01', '2023-03-15 07:19:52'),
(72, 'create_floor', 'Create Floor', NULL, NULL, 0, NULL, NULL, '2023-03-15 09:56:03', '2023-03-18 19:23:12'),
(73, 'floor_list', 'Floor Listing', NULL, NULL, 0, NULL, NULL, '2023-03-16 07:10:30', '2023-03-16 07:10:30'),
(74, 'edit_floor', 'Edit Floor', NULL, NULL, 0, NULL, NULL, '2023-03-16 10:20:09', '2023-03-16 10:20:09'),
(75, 'index', 'Bank', NULL, NULL, 0, NULL, NULL, '2023-03-17 11:54:51', '2023-03-17 11:54:51'),
(76, 'manage_floor', 'Manage Floor', NULL, NULL, 0, NULL, NULL, '2023-03-20 06:40:49', '2023-03-20 06:40:49'),
(77, 'myPage', 'My Page', NULL, NULL, 0, NULL, NULL, '2023-03-23 06:22:28', '2023-03-23 06:22:28'),
(78, 'ticket_list', 'Ticket List', NULL, NULL, 0, NULL, NULL, '2023-03-30 09:13:41', '2023-03-30 09:13:41'),
(79, 'department_nv_list', 'NV Department List', NULL, NULL, 0, NULL, NULL, '2023-04-12 05:42:49', '2023-04-12 05:42:49'),
(80, 'create_nv_department', 'Create NV Department', NULL, NULL, 0, NULL, NULL, '2023-04-12 05:43:19', '2023-04-12 05:43:19'),
(81, 'edit_nv_department', 'Edit NV Department', NULL, NULL, 0, NULL, NULL, '2023-04-12 05:43:41', '2023-04-12 05:43:41'),
(82, 'delete_nv_department', 'Delete NV Department', NULL, NULL, 0, NULL, NULL, '2023-04-12 05:44:06', '2023-04-12 05:44:06'),
(83, 'createView', 'NV View', NULL, NULL, 0, NULL, NULL, '2023-04-12 09:34:43', '2023-04-12 09:34:43'),
(84, 'createPrintService', 'Print Service', NULL, NULL, 0, NULL, NULL, '2023-04-12 09:35:12', '2023-04-12 09:35:12'),
(85, 'createPrintMaterial', 'Print Material', NULL, NULL, 0, NULL, NULL, '2023-04-12 09:35:57', '2023-04-12 09:35:57'),
(86, 'capex_list', 'CAPEX List', NULL, NULL, 0, NULL, NULL, '2023-04-20 06:58:55', '2023-04-20 06:58:55'),
(87, 'create_capex_master', 'Create CAPEX', NULL, NULL, 0, NULL, NULL, '2023-04-20 06:59:34', '2023-04-20 06:59:34'),
(88, 'edit_capex_master', 'Edit CAPEX', NULL, NULL, 0, NULL, NULL, '2023-04-20 07:00:02', '2023-04-20 07:00:02'),
(89, 'capex_otp', 'CAPEX OTP', NULL, NULL, 0, NULL, NULL, '2023-05-03 09:01:32', '2023-05-03 09:01:32'),
(90, 'opex_otp', 'OPEX OTP', NULL, NULL, 0, NULL, NULL, '2023-05-03 09:17:49', '2023-05-03 09:17:49'),
(91, 'preview', 'Preview Service', NULL, NULL, 0, NULL, NULL, '2023-05-04 05:21:10', '2023-05-04 05:21:10'),
(92, 'opex_list', 'OPEX List', NULL, NULL, 0, NULL, NULL, '2023-05-04 11:08:44', '2023-05-04 11:08:44'),
(93, 'serviceboq_list', 'Service BOQ List', NULL, NULL, 0, NULL, NULL, '2023-05-04 11:10:32', '2023-05-04 11:10:32'),
(94, 'create_serviceboq', 'Create Service BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-04 11:12:28', '2023-05-04 11:12:28'),
(95, 'edit_serviceboq', 'Edit Service BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-04 11:13:52', '2023-05-04 11:13:52'),
(96, 'materialboq_list', 'Material BOQ List', NULL, NULL, 0, NULL, NULL, '2023-05-08 05:52:18', '2023-05-08 05:52:18'),
(97, 'create_materialboq', 'Create Material BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-08 05:52:42', '2023-05-08 05:52:42'),
(98, 'edit_materialboq', 'Edit Material BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-08 05:53:12', '2023-05-08 05:53:12'),
(99, 'store_serviceboq', 'Store Service BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-08 06:00:32', '2023-05-08 06:00:32'),
(100, 'update_serviceboq', 'Update Service BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-08 06:00:53', '2023-05-08 06:00:53'),
(101, 'store_materialboq', 'Store Material BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-08 06:02:00', '2023-05-08 06:02:00'),
(102, 'update_materialboq', 'Update Material BOQ', NULL, NULL, 0, NULL, NULL, '2023-05-08 06:02:24', '2023-05-08 06:02:24'),
(103, 'create_opex', 'Create Opex', NULL, NULL, 0, NULL, NULL, '2023-05-09 06:17:49', '2023-05-09 06:17:49'),
(104, 'edit_opex', 'Edit OPEX', NULL, NULL, 0, NULL, NULL, '2023-05-09 06:25:02', '2023-05-09 06:25:02');

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `activity` varchar(255) NOT NULL,
  `action` text DEFAULT NULL,
  `performed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `activity`, `action`, `performed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'opex created', 'POST admin/opex/store', '2023-05-09 06:28:44', '2023-05-09 06:28:44', '2023-05-09 06:28:44'),
(2, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-09 06:42:49', '2023-05-09 06:42:49', '2023-05-09 06:42:49'),
(3, 1, 'Store Employee', 'POST admin/employees/store', '2023-05-09 07:27:08', '2023-05-09 07:27:08', '2023-05-09 07:27:08'),
(4, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-09 07:27:42', '2023-05-09 07:27:42', '2023-05-09 07:27:42'),
(5, 1, 'Store Employee', 'POST admin/employees/store', '2023-05-09 08:27:13', '2023-05-09 08:27:13', '2023-05-09 08:27:13'),
(6, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-09 08:27:39', '2023-05-09 08:27:39', '2023-05-09 08:27:39'),
(7, 1, 'Store Employee', 'POST admin/employees/store', '2023-05-09 08:31:49', '2023-05-09 08:31:49', '2023-05-09 08:31:49'),
(8, 1, 'Store Employee', 'POST admin/employees/store', '2023-05-09 08:32:48', '2023-05-09 08:32:48', '2023-05-09 08:32:48'),
(9, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-09 08:33:02', '2023-05-09 08:33:02', '2023-05-09 08:33:02'),
(10, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-09 08:33:14', '2023-05-09 08:33:14', '2023-05-09 08:33:14'),
(11, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-11 08:30:06', '2023-05-11 08:30:06', '2023-05-11 08:30:06'),
(12, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-19 06:43:37', '2023-05-19 06:43:37', '2023-05-19 06:43:37'),
(13, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-19 06:44:04', '2023-05-19 06:44:04', '2023-05-19 06:44:04'),
(14, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-19 06:44:25', '2023-05-19 06:44:25', '2023-05-19 06:44:25'),
(15, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-19 06:45:19', '2023-05-19 06:45:19', '2023-05-19 06:45:19'),
(16, 1, 'Store Employee', 'POST admin/employees/store', '2023-05-19 07:03:08', '2023-05-19 07:03:08', '2023-05-19 07:03:08'),
(17, 1, 'Update Employee', 'POST admin/employees/update', '2023-05-19 07:08:45', '2023-05-19 07:08:45', '2023-05-19 07:08:45');

-- --------------------------------------------------------

--
-- Table structure for table `assigned_roles`
--

CREATE TABLE `assigned_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `restricted_to_id` bigint(20) UNSIGNED DEFAULT NULL,
  `restricted_to_type` varchar(255) DEFAULT NULL,
  `scope` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assigned_roles`
--

INSERT INTO `assigned_roles` (`id`, `role_id`, `entity_id`, `entity_type`, `restricted_to_id`, `restricted_to_type`, `scope`) VALUES
(1, 1, 1, 'App\\Models\\User', NULL, NULL, NULL),
(4, 2, 3, 'App\\Models\\User', NULL, NULL, NULL),
(5, 8, 3, 'App\\Models\\User', NULL, NULL, NULL),
(6, 2, 4, 'App\\Models\\User', NULL, NULL, NULL),
(7, 8, 4, 'App\\Models\\User', NULL, NULL, NULL),
(8, 9, 4, 'App\\Models\\User', NULL, NULL, NULL),
(9, 2, 5, 'App\\Models\\User', NULL, NULL, NULL),
(10, 8, 5, 'App\\Models\\User', NULL, NULL, NULL),
(11, 9, 5, 'App\\Models\\User', NULL, NULL, NULL),
(12, 2, 6, 'App\\Models\\User', NULL, NULL, NULL),
(13, 8, 6, 'App\\Models\\User', NULL, NULL, NULL),
(14, 9, 6, 'App\\Models\\User', NULL, NULL, NULL),
(15, 2, 7, 'App\\Models\\User', NULL, NULL, NULL),
(16, 8, 7, 'App\\Models\\User', NULL, NULL, NULL),
(17, 9, 7, 'App\\Models\\User', NULL, NULL, NULL),
(18, 2, 8, 'App\\Models\\User', NULL, NULL, NULL),
(19, 8, 8, 'App\\Models\\User', NULL, NULL, NULL),
(20, 9, 8, 'App\\Models\\User', NULL, NULL, NULL),
(21, 2, 9, 'App\\Models\\User', NULL, NULL, NULL),
(22, 8, 9, 'App\\Models\\User', NULL, NULL, NULL),
(23, 9, 9, 'App\\Models\\User', NULL, NULL, NULL),
(24, 2, 10, 'App\\Models\\User', NULL, NULL, NULL),
(25, 8, 10, 'App\\Models\\User', NULL, NULL, NULL),
(26, 9, 10, 'App\\Models\\User', NULL, NULL, NULL),
(27, 2, 12, 'App\\Models\\User', NULL, NULL, NULL),
(28, 8, 12, 'App\\Models\\User', NULL, NULL, NULL),
(29, 9, 12, 'App\\Models\\User', NULL, NULL, NULL),
(30, 2, 13, 'App\\Models\\User', NULL, NULL, NULL),
(31, 8, 13, 'App\\Models\\User', NULL, NULL, NULL),
(32, 9, 13, 'App\\Models\\User', NULL, NULL, NULL),
(33, 2, 14, 'App\\Models\\User', NULL, NULL, NULL),
(34, 8, 14, 'App\\Models\\User', NULL, NULL, NULL),
(35, 9, 14, 'App\\Models\\User', NULL, NULL, NULL),
(36, 2, 15, 'App\\Models\\User', NULL, NULL, NULL),
(37, 8, 15, 'App\\Models\\User', NULL, NULL, NULL),
(38, 9, 15, 'App\\Models\\User', NULL, NULL, NULL),
(39, 2, 16, 'App\\Models\\User', NULL, NULL, NULL),
(40, 8, 16, 'App\\Models\\User', NULL, NULL, NULL),
(41, 9, 16, 'App\\Models\\User', NULL, NULL, NULL),
(42, 2, 17, 'App\\Models\\User', NULL, NULL, NULL),
(43, 8, 17, 'App\\Models\\User', NULL, NULL, NULL),
(44, 9, 17, 'App\\Models\\User', NULL, NULL, NULL),
(45, 2, 18, 'App\\Models\\User', NULL, NULL, NULL),
(46, 8, 18, 'App\\Models\\User', NULL, NULL, NULL),
(47, 9, 18, 'App\\Models\\User', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(15, 'Airtel', 1, '2022-12-28 09:10:29', '2022-12-28 09:10:29'),
(16, 'Sify', 1, '2022-12-28 09:11:13', '2022-12-28 09:11:13'),
(17, 'Rcom', 1, '2022-12-28 09:11:46', '2022-12-28 09:11:46'),
(18, 'CISCO', 1, '2023-01-05 06:20:20', '2023-01-05 06:20:20'),
(19, 'FORTINET', 1, '2023-01-05 07:10:15', '2023-01-05 07:10:15');

-- --------------------------------------------------------

--
-- Table structure for table `capex_master`
--

CREATE TABLE `capex_master` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `head` varchar(255) NOT NULL,
  `sub_head` varchar(255) NOT NULL,
  `brp_head` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `capx_fy_one` double NOT NULL,
  `capx_fy_two` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `capex_master`
--

INSERT INTO `capex_master` (`id`, `head`, `sub_head`, `brp_head`, `status`, `capx_fy_one`, `capx_fy_two`, `created_at`, `updated_at`) VALUES
(1, '0', 'Distribution(11 kV & below)', '0', '0', 186.46, 204.67, '2023-04-20 07:07:19', '2023-04-20 07:07:44');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'BPI', 1, '2023-04-13 08:12:14', '2023-04-13 08:12:14'),
(2, 'CEO Cell', 1, '2023-04-13 08:12:34', '2023-04-13 08:12:34'),
(3, 'Regulatory', 1, '2023-04-13 08:13:08', '2023-04-13 08:13:08'),
(4, 'O&M', 1, '2023-04-13 08:14:20', '2023-04-13 08:14:20'),
(5, 'Enforcement', 1, '2023-04-13 08:14:44', '2023-04-13 08:14:44'),
(6, 'Company Secretary Office', 1, '2023-04-13 08:15:34', '2023-04-13 08:15:34'),
(7, 'Safety', 1, '2023-04-13 08:15:50', '2023-04-13 08:15:50'),
(8, 'Business Excellence Team', 1, '2023-04-13 08:16:26', '2023-04-13 08:16:26'),
(9, 'Corporate Communication', 1, '2023-04-13 08:16:56', '2023-04-13 08:16:56'),
(10, 'DSM & PAT', 1, '2023-04-13 08:17:18', '2023-04-13 08:17:18'),
(11, 'Business Process Improvement', 1, '2023-04-13 08:18:25', '2023-04-13 08:18:25');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `short_code` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `name`, `short_code`, `status`, `created_at`, `updated_at`) VALUES
(5, 'BYPL', 'bypl', 1, '2022-12-28 12:01:37', '2023-03-10 07:25:50'),
(6, 'BRPL', 'brpl', 1, '2022-12-28 12:01:59', '2023-03-10 07:25:34');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `division_id` bigint(20) UNSIGNED NOT NULL,
  `location_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `password` varchar(255) NOT NULL,
  `role_id` varchar(255) NOT NULL,
  `role_name` varchar(255) NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `department_id` varchar(255) DEFAULT NULL,
  `report_to` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `name`, `email`, `phone`, `division_id`, `location_id`, `status`, `created_at`, `updated_at`, `password`, `role_id`, `role_name`, `employee_id`, `user_id`, `department_id`, `report_to`, `last_login_at`) VALUES
(3, 'Shipra', 'shipra.a@rediansoftware.com', '5665656565', 5, 238, 1, '2023-04-19 12:05:10', '2023-05-18 13:11:31', '12345678', '9', '9', 'RS192', '4', '3', '6', '2023-05-18 13:11:31'),
(4, 'Hareram kumar', 'hareram12.y@redianglobal.com', '6523653265', 5, 238, 1, '2023-04-20 11:12:12', '2023-05-19 06:45:19', '12345678', '9', '9', 'RS193', '5', '2', '6', '2023-05-17 10:49:37'),
(5, 'Preeti', 'preeti.s@rediansoftware.com', '6566532895', 5, 238, 1, '2023-04-21 07:09:58', '2023-05-19 08:38:15', '12345678', '2', '2', 'RS196', '6', '2', '13', '2023-05-19 08:38:15'),
(6, 'Raushan', 'raushan.y@rediansoftware.com', '6598326598', 5, 233, 1, '2023-05-03 09:43:14', '2023-05-17 13:16:37', '12345678', '2', '2', 'RS126', '7', '2', '13', '2023-05-11 08:32:29'),
(7, 'pushpendra', 'pushpendra@gmail.com', '8956324578', 5, 234, 1, '2023-05-03 09:46:19', '2023-05-03 09:47:54', '12345678', '9', '9', 'RS186', '8', '2', '7', '2023-05-03 09:47:54'),
(8, 'ashu', 'ashu@gmail.com', '6598326595', 5, 237, 1, '2023-05-03 10:06:50', '2023-05-09 09:33:05', '12345678', '9', '9', 'RS89', '9', '6', '7', '2023-05-09 09:33:05'),
(9, 'Arpan', 'arpan.p@rediansoftware.com', '1234567890', 5, 234, 1, '2023-05-03 12:20:21', '2023-05-19 07:12:34', '12345678', '5', '5', 'RS188', '13', '2', '14', '2023-05-19 07:12:34'),
(10, 'Megha', 'ashu.s@rediansoftware.com', '1234567895', 5, 238, 1, '2023-05-09 07:27:08', '2023-05-19 07:13:25', '12345678', '10', '10', 'RS194', '14', '2', '15', '2023-05-19 07:13:25'),
(11, 'Kundan', 'pooja@rediansoftware.com', '1211212213', 5, 238, 1, '2023-05-09 08:27:13', '2023-05-17 13:25:30', '12345678', '6', '6', 'RS195', '15', '9', '16', '2023-05-17 11:27:15'),
(12, 'Arunesh', 'arunesh@rediansoftware.com', '8965986532', 5, 238, 1, '2023-05-09 08:31:49', '2023-05-17 13:24:14', '12345678', '7', '7', 'RS197', '16', '9', '17', '2023-05-17 11:46:07'),
(13, 'hareram', 'hareram2.y@redianglobal.com', '9878456523', 5, 238, 1, '2023-05-09 08:32:48', '2023-05-19 07:06:42', '12345678', '9', '9', 'RS198', '17', '9', '6', '2023-05-19 06:59:50'),
(14, 'hareram test', 'hareram.y@redianglobal.com', '6294211979', 5, 232, 1, '2023-05-19 07:03:08', '2023-05-19 08:39:24', '12345678', '9', '9', 'hareram001', '18', '2', '6', '2023-05-19 08:39:24');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `divisions_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `divisions_id`, `status`, `created_at`, `updated_at`, `name`) VALUES
(119, 6, 1, '2022-12-28 10:51:11', '2023-03-10 07:34:47', 'SCADA Balaji'),
(120, 5, 1, '2022-12-28 12:03:32', '2022-12-28 12:03:32', '66 KV S/STN RIDGE VALLEY Near Satnam Dharam College, Dhaula Kuan Flyover Opp. Police Chowki, Ring Road,new delhi'),
(121, 5, 1, '2022-12-28 12:06:38', '2022-12-28 12:06:38', '66 KV  Grid S/Stn. Jasola, DDA COMMERCIAL COMPLEX JASOLA, Opposite Sarita Vihar,New Delhi - 44'),
(122, 6, 1, '2022-12-28 12:07:41', '2022-12-28 12:07:41', '220 PPK-2 Sector 16D,Dwarka (Near Sector 14 Metro station), NEW DELHI'),
(123, 6, 1, '2022-12-28 12:08:53', '2022-12-28 12:08:53', '66 KV BUDELLA-2 GRID SUB STATION NEW UJWAL APARTMENTS, VIKAS PURI, NEW DELHI'),
(124, 5, 1, '2022-12-28 12:09:41', '2022-12-28 12:09:41', '66kv Grid Stationff Kunj Block-C, Vasant Kunj, New Delhi'),
(125, 5, 1, '2022-12-28 12:10:38', '2022-12-28 12:10:38', '66kV Grid Station malviya Nagar, Near M.B. Road Saket, New Delhi'),
(126, 6, 1, '2022-12-28 12:11:34', '2022-12-28 12:11:34', '66 KV BINDAPUR GRID SUB STATION POCKET-3, BINDAPUR,NEAR DDA FLATS, NEW DELHI-110059'),
(127, 6, 1, '2022-12-28 12:16:20', '2022-12-28 12:16:20', 'G-5 PAPPAN KALAN GRID SUB STATION, MATIALA  ,BEHIND DPS SCHOOL, DWARKA, SECTOR-3, NEW DELHI -110045'),
(128, 6, 1, '2022-12-28 12:17:23', '2022-12-28 12:17:23', '66KV  DJB Najafgarh'),
(129, 6, 1, '2022-12-28 12:18:18', '2022-12-28 12:18:18', '66/11 KV HASTAAL GRID'),
(130, 6, 1, '2022-12-28 12:19:11', '2022-12-28 12:19:11', '66 KV NANGLOI GRID SUB STATION, JWALA PURI,OPP. NANGLOI BUS TERMINAL, NEW DELHI-110041'),
(131, 6, 1, '2022-12-28 12:20:02', '2022-12-28 12:20:02', '66 KV NANGLOI WATER WORKS SUB STATION, WATER PLANT ,KAMMRUDDIN  NAGAR , NEW DELHI-110041'),
(132, 5, 1, '2022-12-28 12:21:26', '2022-12-28 12:21:26', '66 KV GRID S/STN T.I.A (BATRA) NEAR BATRA HOSPITAL, NEW DELHI'),
(133, 5, 1, '2022-12-28 12:22:11', '2022-12-28 12:22:11', '66 Kv Grid Substation, C-Dot Campus, MEHRAULI GADAI PUR ROAD, BEHIND INDRA GANDHI FARM HOUSE, MEHRAULI,'),
(134, 5, 1, '2022-12-28 12:22:57', '2022-12-28 12:22:57', '33 KV GRID S/STN KILOKRI NEAR JEEVAN NURSING HOME, NEW DELHI'),
(135, 5, 1, '2022-12-28 12:23:52', '2022-12-28 12:23:52', '66 kv grid Sarita vihar TELEPHONE EXCHANGE JANTA COLONY,SARITA VIHAR'),
(136, 6, 1, '2022-12-28 12:24:56', '2022-12-28 12:24:56', '33 KV MUKHERJI PARK GRID SUB STATION , KHYALA ROAD, SUBHASH NAGAR MORE, NEW DELHI-110018'),
(137, 5, 1, '2022-12-28 12:25:42', '2022-12-28 12:25:42', 'BSES , 66 KV GRID S/STN IOC BIJWASAN DELHI'),
(138, 5, 1, '2022-12-28 12:29:01', '2022-12-28 12:29:01', '33kV grid Station Nehru Place, BSES Bhawan , New Delhi'),
(139, 5, 1, '2022-12-28 12:30:00', '2022-12-28 12:30:00', '66 KV JAFFARPUR'),
(140, 5, 1, '2022-12-30 09:58:09', '2022-12-30 09:58:09', '66kV Grid Station Okhla Phase-1, Near DTC Workshop, New Delhi'),
(141, 6, 1, '2022-12-30 09:59:11', '2022-12-30 09:59:11', '66kv grid station, G-6 Pappankalan Sec -9 Dwarka'),
(142, 6, 1, '2022-12-30 10:26:27', '2022-12-30 10:26:27', 'Location 33 KV MAYAPURI GRID SUB STATION, NEAR MAYA ENCLAVE, HARI NAGAR , NEW DELHI 33 KV MAYAPURI GRID SUB STATION, NEAR MAYA ENCLAVE, HARI NAGAR , NEW DELHI'),
(143, 6, 1, '2022-12-30 10:33:38', '2022-12-30 10:33:38', '33kv Paschim puri Grid sub station'),
(144, 6, 1, '2022-12-30 10:34:27', '2022-12-30 10:34:27', '66 KV AREA SAGARPUR GRID SUB STATION, NEAR DVB COLONY, NEW DELHI'),
(145, 6, 1, '2022-12-30 10:35:27', '2022-12-30 10:35:27', '33 KV Vishal GRID W.D.D.C GRID SUB STATION , NEAR SHIVAJI COLLEGE, VISHAL CINEMA, NEW DELHI'),
(146, 6, 1, '2022-12-30 10:36:23', '2022-12-30 10:36:23', '66KV Grid s/stn. G-15 Pappankalan Dwarka Opp.Sec.12 Metro Station near Petrol pump Delhi'),
(147, 6, 1, '2022-12-30 10:37:24', '2022-12-30 10:37:24', '66 KV BUDELLA-1 GRID SUB STATION OUTER RING ROAD, NEAR VIKAS KUNJ SOCIETY,VIKAS PURI, NEW DELHI-110018'),
(148, 6, 1, '2022-12-30 10:46:26', '2022-12-30 10:46:26', '66/11 kV Grid Substation G-7 Dwarka, Sector - 8 Dwarka 1177 Near DTC Bus Depot / Queens Valley School, New Delhi 1177     New Delhi Delhi India'),
(149, 6, 1, '2022-12-30 10:47:27', '2022-12-30 10:47:27', '66KV  GRID S/STN, BSES RAJDHANI, IGNOU'),
(150, 5, 1, '2022-12-30 10:48:26', '2022-12-30 10:48:26', 'BSES, 33 KV GRID S/STN BHIKAJI CAMA PLACE NEAR AUGUST KRANTI BHAWAN, NEW DELHI'),
(151, 5, 1, '2022-12-30 10:49:20', '2022-12-30 10:49:20', 'BSES NIZAMUDDIN,BRPL BUILDING  SUB STN BLDG NIZAMUDDIN WEST, NEW DELHI'),
(152, 5, 1, '2022-12-30 10:50:20', '2022-12-30 10:50:20', '33kV Grid Station East Of Kailash, Near DAV School, New Delhi'),
(153, 6, 1, '2022-12-30 10:51:18', '2022-12-30 10:51:18', '33kV Grid Station IAAI Palam, Palam Airport, Palam New Delhi'),
(154, 6, 1, '2022-12-30 10:52:21', '2022-12-30 10:52:21', 'G4 Pappankalan Grid station,Dwarka,110045'),
(155, 6, 1, '2022-12-30 10:53:37', '2022-12-30 10:53:37', '33KV A4 Paschim Vihar Substation Grid, near Vidyapeeth Engineering College, A4 Paschim Vihar, New Delhi'),
(156, 6, 1, '2022-12-30 10:54:26', '2022-12-30 10:54:26', '33 KV UDYOG NAGAR GRID SUB STATION, NEW MCD OFFICE, UDYOG NAGAR, NEW DELHI-110041'),
(157, 5, 1, '2022-12-30 10:56:28', '2022-12-30 10:56:28', '66kv Fatehpur beri'),
(158, 5, 1, '2022-12-30 10:57:16', '2022-12-30 10:57:16', '33KV  GRID S/STN, BSES RAJDHANI, TUGLAKABAD'),
(159, 5, 1, '2022-12-30 10:58:05', '2022-12-30 10:58:05', 'Alaknanda'),
(160, 5, 1, '2022-12-30 10:58:56', '2022-12-30 10:58:56', '66/11 kV Grid Substation at Mithapur , Village Molarbandh/Jiatpur Behind NTPC Badarpur'),
(161, 6, 1, '2022-12-30 10:59:38', '2022-12-30 10:59:38', '66/11 KV MUNDKA GRID,C/O 400 KV DTL GRID,VILLAGE TIKRI KALAN,NEAR SANSKRIT GURUKUL,MUNDKA - 110041.'),
(162, 5, 1, '2022-12-30 11:00:21', '2022-12-30 11:00:21', '33kV Grid Station Sirifort, Near Shah Pur Jat Village, New Delhi-110017'),
(163, 6, 1, '2022-12-30 11:01:09', '2022-12-30 11:01:09', '33 KV MADIPUR GRID SUB STATION NEAR SHIV MANDIR NEW DELHI-110053'),
(164, 5, 1, '2022-12-30 11:01:59', '2022-12-30 11:01:59', '33kV Grid Station Shivalik, Block-C, Near DERC Building, New Delhi'),
(165, 5, 1, '2022-12-30 11:02:48', '2022-12-30 11:02:48', '33kV Grid Station VSNL, GK-1, Near Savitri Cinema, Near GK Metro New Delhi'),
(166, 5, 1, '2022-12-30 11:03:32', '2022-12-30 11:03:32', '33KV Grid Okhla phase 2New Delhi'),
(167, 5, 1, '2022-12-30 11:04:40', '2022-12-30 11:04:40', '33KV GRID IIT CAMPUS, HAUZ KHAS, NEW DELHI'),
(168, 5, 1, '2022-12-30 11:05:47', '2022-12-30 11:05:47', '33kV Grid Station D.C.Saket, Opp.Khirki Village, New Delhi'),
(169, 6, 1, '2022-12-30 11:07:21', '2022-12-30 11:07:21', '66 KV PPK 2 G2 Dwarka Pappankalan'),
(170, 5, 1, '2022-12-30 11:08:21', '2022-12-30 11:08:21', '66kv Grid Station Vasant Kunj Block-D, Near D-7 and D-8, New Delhi'),
(171, 5, 1, '2022-12-30 11:10:57', '2022-12-30 11:10:57', '66kv MCIA Grid Sub Station, Mohan Cooperative, Badarpur New Delhi 110044'),
(172, 5, 1, '2022-12-30 11:11:35', '2022-12-30 11:11:35', '33kV Grid Station HUDCO Complex, Andrews Ganj, New Delhi'),
(173, 6, 1, '2022-12-30 11:12:22', '2022-12-30 11:12:22', '33kV Grid Station Defence Colony, opp,C-23, New Delhi'),
(174, 6, 1, '2022-12-30 11:15:41', '2022-12-30 11:15:41', 'Aerocity'),
(175, 5, 1, '2022-12-30 11:16:22', '2022-12-30 11:16:22', '33kv JLN stadium Grid'),
(176, 5, 1, '2022-12-30 11:19:18', '2022-12-30 11:19:18', '33 KV GRID S/STN , NDSE -2, Near South EX metro, NEW DELHI'),
(177, 5, 1, '2022-12-30 11:19:58', '2022-12-30 11:19:58', '66 KV PUSHP VIHAR GRID SECTOR 7 OPPOSITE METRO ENCLAVE PUSHP VIHAR PINCODE 110017'),
(178, 6, 1, '2022-12-30 11:20:46', '2022-12-30 11:20:46', '33kv CBI BUILding'),
(179, 5, 1, '2022-12-30 11:21:29', '2022-12-30 11:21:29', '33kV Grid Station Masjith Moth, Panchsheel Enclave, Near Chirag Delhi Flyover, New Delhi'),
(180, 6, 1, '2022-12-30 11:22:11', '2022-12-30 11:22:11', '33 KV S.B MILLS GRID SUB STATION,NAJAFGARH ROAD,NEAR SWATANTRE BHARAT MILLS, NEW DELHI-110015'),
(181, 5, 1, '2022-12-30 11:22:57', '2022-12-30 11:22:57', '33kV Grid Station Lajpat Nagar-2, Behind Alankar Cinema, New Delhi'),
(182, 5, 1, '2022-12-30 11:24:31', '2022-12-30 11:24:31', '66 KV Vasant Kunj Institutional area'),
(183, 6, 1, '2022-12-30 11:25:09', '2022-12-30 11:25:09', '33 kV DLF Tower BSES Grid'),
(184, 5, 1, '2022-12-30 11:25:52', '2022-12-30 11:25:52', '66kV Grid Station JNU, New Delhi'),
(185, 6, 1, '2022-12-30 11:26:32', '2022-12-30 11:26:32', 'G-1, Pappankalan,dwarka'),
(186, 6, 1, '2022-12-30 11:27:25', '2022-12-30 11:27:25', '33kV Grid Station Exihibition Ground-2, Near Gate No-1, ITPO,New Delhi'),
(187, 6, 1, '2022-12-30 11:28:09', '2022-12-30 11:28:09', '66 KV HARI NAGAR GRID SUB STATION,'),
(188, 5, 1, '2022-12-30 11:28:51', '2022-12-30 11:28:51', 'IHC - Indian Habitat Center'),
(189, 5, 1, '2022-12-30 11:29:32', '2022-12-30 11:29:32', '33kV Grid Station Vasant Vihar, New Delhi-57'),
(190, 6, 1, '2022-12-30 11:30:28', '2022-12-30 11:30:28', '66 KV PANKHA ROAD GRID SUB STATION,C-1 JANAK PURI, BEHIND MATA CHANNAN DEVI HOSPITAL, NEW DELHI-110058'),
(191, 5, 1, '2022-12-30 11:31:17', '2022-12-30 11:31:17', '33kV Grid Station, Near Adchini Village, New Delhi'),
(192, 5, 1, '2022-12-30 11:34:04', '2022-12-30 11:34:04', '66kv Grid Station Vasant Kunj Block-B, Near B-6and B-8, New Delhi'),
(193, 6, 1, '2022-12-30 11:34:45', '2022-12-30 11:34:45', '33 KV DIST. CENTRE JanakPuri GRID SUB STATION NEAR TRANSPORT AUTHORITY, NEW DELHI'),
(194, 5, 1, '2022-12-30 11:35:25', '2022-12-30 11:35:25', '66kV Gird Station Mathura Road, A-777, Sarita Vihar, New Delhi'),
(195, 5, 1, '2022-12-30 11:36:03', '2022-12-30 11:36:03', '33kv grid sub stn sarai jullena,near masih garh church, new delhi'),
(196, 5, 1, '2022-12-30 11:39:34', '2022-12-30 11:39:34', '33kV Grid Station R.K.Puram2, West Block New Delhi'),
(197, 6, 1, '2022-12-30 11:40:22', '2022-12-30 11:40:22', '66kV Grid Station Palam, New Delhi'),
(198, 5, 1, '2022-12-30 11:41:04', '2022-12-30 11:41:04', '33 KV Grid Jamia Milai Oklha , Near Holy Family Hospital New Delhi'),
(199, 6, 1, '2022-12-30 11:41:49', '2022-12-30 11:41:49', '66kv Chaukhandi near Keshavpur Mandi'),
(200, 5, 1, '2022-12-30 11:42:55', '2022-12-30 11:42:55', '66 KV PASCHIM VIHAR GRID S/Stn, Near Syed Village, Next to Mira Bagh, Group Housing 5&7, NEAR CH-517,DDA FLATS,'),
(201, 5, 1, '2022-12-30 11:43:45', '2022-12-30 11:43:45', 'nilothi'),
(202, 6, 1, '2022-12-30 11:44:37', '2022-12-30 11:44:37', 'Paciffic mall'),
(203, 6, 1, '2022-12-30 11:45:28', '2022-12-30 11:45:28', '66kv South asian university'),
(204, 6, 1, '2022-12-30 11:46:09', '2022-12-30 11:46:09', '33kv Dlf2'),
(205, 5, 1, '2022-12-30 11:46:50', '2022-12-30 11:46:50', 'ILBS Vasantkunj'),
(206, 5, 1, '2022-12-30 11:47:58', '2022-12-30 11:47:58', 'City Walk 33KV'),
(207, 5, 1, '2022-12-30 11:48:39', '2022-12-30 11:48:39', '33kV NSIC Grid (BSES Grid)   National Small Industries Corporation Ltd.   NSIC Bhawan    Okhla Industrial Estate    New Delhi- 110020'),
(208, 5, 1, '2022-12-30 11:49:44', '2022-12-30 11:49:44', 'ITPO'),
(209, 6, 1, '2022-12-30 11:50:34', '2022-12-30 11:50:34', '66/11 KV GURU GOBIND SINGH HOSPITAL GRID, Ragubir Market, near Tagore Garden Metro'),
(210, 5, 1, '2022-12-30 11:56:27', '2022-12-30 11:56:27', '33kV Grid Station R.K.Puram1, West Block New Delhi'),
(211, 5, 1, '2022-12-30 11:57:06', '2022-12-30 11:57:06', 'sangam vihar'),
(212, 6, 1, '2022-12-30 11:59:03', '2022-12-30 11:59:03', 'Mitraon village, Jaffarpur,Mitraon,,04,New Delhi, Delhi, 110073, IN'),
(213, 5, 1, '2022-12-30 12:00:05', '2022-12-30 12:00:05', 'NAT'),
(214, 5, 1, '2022-12-30 12:00:49', '2022-12-30 12:00:49', '33kV Ambience Mall Grid (BSES Grid),    Ambience Mall,    Nelson Mandela Road,    Vasant Kunj,    New Delhi-110070'),
(215, 5, 1, '2022-12-30 12:01:35', '2022-12-30 12:01:35', 'TCIL'),
(216, 6, 1, '2022-12-30 12:02:31', '2022-12-30 12:02:31', '33 kV GRID STATION ANDHERIA BAGH OPP GANGA NURSERY WELCOME GARDEN NEW DELHI 110038 Delhi Delhi'),
(217, 5, 1, '2022-12-30 12:03:22', '2022-12-30 12:03:22', '220 kV Najafgarh Grid'),
(218, 5, 1, '2022-12-30 12:04:15', '2022-12-30 12:04:15', '220 kV Lodhi road DTL Grid'),
(219, 5, 1, '2022-12-30 12:05:13', '2022-12-30 12:05:13', '220 kV Vasant Kunj DTL Grid-c9'),
(220, 5, 1, '2022-12-30 12:05:58', '2022-12-30 12:05:58', '220 kV Okhla DTL Grid'),
(221, 6, 1, '2022-12-30 12:06:41', '2022-12-30 12:06:41', '220 KV PPK 1 DTL Grid'),
(222, 6, 1, '2022-12-30 12:07:37', '2022-12-30 12:07:37', 'A 43 Mayapuri A 43 Mayapuri New Delhi 110064'),
(223, 6, 1, '2022-12-30 12:08:24', '2022-12-30 12:08:24', '66 KV DMICDC RS-I Sector 25 Dwarka New'),
(224, 6, 1, '2022-12-30 12:09:06', '2022-12-30 12:09:06', '66 KV DMICDC RS-II Sector 25 Dwarka New'),
(225, 6, 1, '2022-12-30 12:13:19', '2022-12-30 12:13:19', '400/220KV Mundka,BSES,Neelwal Road, Near Vaishno Devi Mandir,04,NEW DELHI, Delhi, 110041, IN'),
(226, 6, 1, '2022-12-30 12:14:01', '2022-12-30 12:14:01', '220KV,BSES,PPK3, Sector 19B,04,NEW DELHI, Delhi, 110071, IN'),
(227, 5, 1, '2022-12-30 12:19:51', '2022-12-30 12:19:51', '33/11 KV Grid Substation AIIMS Masjid Moth Near Hostel No 14 AIIMS Hospital  New Delhi'),
(228, 5, 1, '2022-12-30 12:20:34', '2022-12-30 12:20:34', '220KV,BSES,DIAL, Panchvati Palam,04,NEW DELHI, Delhi, 110037, IN'),
(229, 5, 1, '2022-12-30 12:21:13', '2022-12-30 12:21:13', '220KV,BSES,Sector 4, RK Puram,04,NEW DELHI, Delhi, 110022, IN'),
(230, 5, 1, '2022-12-30 12:21:50', '2022-12-30 12:21:50', '66/11 KV Grid Substation CAPFIMS UER II Asola Wild Life Sanctuary Maidan Garhi'),
(231, 6, 1, '2022-12-30 12:22:30', '2022-12-30 12:22:30', '66 KV Goyla Qutub Vihar'),
(232, 5, 1, '2022-12-30 12:23:10', '2022-12-30 12:23:10', 'Central Link Airtel (P2P)'),
(233, 5, 1, '2022-12-30 12:23:50', '2022-12-30 12:23:50', 'Central Link Airtel MPLS (NHP)'),
(234, 5, 1, '2022-12-30 12:25:29', '2022-12-30 12:25:29', 'Central Link Airtel MPLS (Balaji R2)'),
(236, 6, 1, '2023-01-23 06:04:32', '2023-01-23 06:04:32', 'Core Device Balaji DC'),
(237, 5, 1, '2023-01-23 06:09:41', '2023-01-23 06:09:41', '220KV  SARITA VIHAR DTL'),
(238, 5, 1, '2023-01-23 06:10:07', '2023-01-23 06:10:07', '220 kv mehrauli DTL'),
(239, 6, 1, '2023-02-13 10:43:56', '2023-02-13 10:43:56', '33 KV S/STN, METAL FORGING IND. AREA, MAIN ROAD, MAYAPURI');

-- --------------------------------------------------------

--
-- Table structure for table `master_material_boq`
--

CREATE TABLE `master_material_boq` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `material_short_text` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `master_material_boq`
--

INSERT INTO `master_material_boq` (`id`, `company_id`, `activity`, `uom`, `material_short_text`, `created_at`, `updated_at`) VALUES
(1, 5, '123456', 'Test', 'Test material data', '2023-05-08 06:04:32', '2023-05-08 06:04:32');

-- --------------------------------------------------------

--
-- Table structure for table `materialboq`
--

CREATE TABLE `materialboq` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `bun` varchar(255) DEFAULT NULL,
  `service_short_text` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `materialboq`
--

INSERT INTO `materialboq` (`id`, `activity`, `bun`, `service_short_text`, `created_at`, `updated_at`) VALUES
(1, '123456', 'Test', 'test data First', '2023-05-08 06:03:26', '2023-05-08 06:03:44');

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
(5, '2014_10_12_000000_create_users_table', 1),
(6, '2014_10_12_100000_create_password_resets_table', 1),
(7, '2019_08_19_000000_create_failed_jobs_table', 1),
(8, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(9, '2022_03_19_105511_create_bouncer_tables', 2),
(11, '2022_03_27_111419_create_media_table', 4),
(28, '2022_08_14_072559_create_division_table', 5),
(30, '2022_08_14_124121_create_locations_table', 6),
(31, '2022_08_14_135158_create_inventory_table', 7),
(32, '2022_08_22_064223_create_vendor_table', 8),
(33, '2022_08_22_125656_create_circle_table', 8),
(34, '2022_08_23_053435_create_department_table', 8),
(35, '2022_08_23_102927_create_employee_table', 8),
(36, '2022_08_25_065901_create_itemissue_table', 8),
(37, '2022_08_25_135742_create_itemreturn_table', 8),
(38, '2022_08_27_061931_create_asset_table', 8),
(39, '2022_08_27_101531_alter_serial_number_inventory_table', 8),
(40, '2022_08_27_103129_create_inventory_serial_number_mapping_table', 8),
(41, '2022_08_28_103749_alter_unique_model_number_inventory_table', 8),
(42, '2022_08_28_105225_alter_vender_id_inventory_table', 8),
(43, '2022_08_28_111251_alter_unique_serial_number_inventory_mapping_table', 8),
(44, '2022_08_28_135136_alter_add_division_id_item_issue_table', 8),
(45, '2022_08_28_180810_create_complaint_table', 9),
(46, '2022_09_04_091258_create_brand_table', 9),
(47, '2022_09_05_182251_alter_add_issue_ref_no_issue_item_table', 9),
(48, '2022_09_05_191741_alter_add_mrna_number_inventory_table', 9),
(49, '2022_09_05_192105_create_brand_table', 9),
(50, '2022_09_05_192202_alter_add_brand_id_inventory_table', 9),
(51, '2022_09_06_124338_alter_add_asset_type_table', 9),
(52, '2022_09_06_131936_alter_add_brand_id_item_issue_table', 9),
(53, '2022_09_07_110419_alter_add_overall_asset_item_issue_table', 10),
(54, '2022_11_07_061039_alter_emp_id_employee_table', 11),
(55, '2022_11_07_061344_alter_add_brand_id_asset_table', 11),
(56, '2022_11_16_065041_alter_add_softdelete_vendor_table', 11),
(57, '2022_12_09_122328_add_column_to_inventory_table', 12),
(58, '2022_12_09_182134_alter_location_column_to_locations_table', 13),
(59, '2022_12_09_182539_alter_location_column_to_locations_table', 13),
(60, '2022_12_10_004950_drop_coulmn_to_employee_table', 13),
(61, '2022_12_10_005201_add_coulmn_to_employee_table', 13),
(62, '2022_12_14_180628_add_mapping_id_column_to_inventory_serial_number_mapping', 14),
(63, '2022_12_29_110226_alter_po_start_column_to_inventory_table', 15),
(64, '2023_03_20_151003_create_floor_plan_table', 16),
(65, '2023_03_20_124355_create_employee_table', 17),
(66, '2023_03_20_152530_alter_table_employee_add_password_roles', 17),
(67, '2023_03_20_180701_create_service_table', 17),
(68, '2023_03_21_134822_create_taks_table', 18),
(69, '2023_03_21_182343_add_frequency_taks_table', 18),
(70, '2023_03_22_110950_add_coloumn_status_taks_table', 18),
(71, '2023_03_23_163353_add_role_id_table', 19),
(72, '2023_03_24_141648_add_role_id_table', 20),
(73, '2023_03_24_142602_create_tickets_table', 20),
(74, '2023_03_27_114454_drop_employee_id_employee_table', 21),
(75, '2023_03_27_134033_add_image_column_table', 22),
(76, '2023_03_28_123548_add_image_column_table', 23),
(77, '2023_03_29_125509_add_user_id_employee', 24),
(78, '2023_03_31_153558_add_oc_status_table', 25),
(79, '2023_04_07_124525_alter_coloumn_oc', 25),
(80, '2023_04_11_122031_create_needvalidations_table', 26),
(81, '2023_04_12_124223_create_needvalidations_table', 27),
(82, '2023_04_14_095141_add_department_id_employee_table', 27),
(83, '2023_04_17_165819_create_tbl_service_table', 28),
(87, '2023_04_17_180152_create_tbl_service_table', 29),
(88, '2023_04_19_151950_create_tbl_service_doc_table', 30),
(89, '2023_04_18_175859_capex__masters', 31),
(90, '2023_04_19_114950_alter__capex_master_columntype', 31),
(91, '2023_04_20_155328_add_report_to_employee_table', 32),
(92, '2023_04_21_111023_create_opex_table', 33),
(93, '2023_04_24_151024_create_nvservicestatus_table', 34),
(94, '2023_04_26_060928_add_three_column_to_users_table', 34),
(95, '2023_04_28_112056_add_last_login_at_to_employee_table', 34),
(96, '2023_04_28_115823_add_two_column_to_users_table', 34),
(97, '2023_04_28_151009_add_column_to_users_table', 34),
(98, '2023_05_02_143647_add_user_id_needvalidation', 35),
(99, '2023_05_02_151610_create_otps_table', 36),
(100, '2023_04_25_153018_create_tbl_material_table', 37),
(101, '2023_05_02_105652_create_nvmaterialstatus_table', 37),
(102, '2023_05_02_140427_create_tbl_material_doc_table', 37),
(103, '2023_05_03_142338_create_materialboq_table', 38),
(104, '2023_05_04_114227_create_activity_logs_table', 39),
(105, '2023_05_04_172409_create_master_material_boq_table', 40),
(106, '2023_05_08_110445_add_material_id_nvservicestatus_table', 41),
(107, '2023_05_05_143051_create_tbl_material_table', 42),
(108, '2023_05_10_125023_add_ces_nvservicestatus_table', 43),
(109, '2023_05_12_113913_add_remarks_nvservicestatus_table', 44),
(112, '2023_05_12_155657_create_tble_service_stages_table', 45),
(113, '2023_05_12_160707_create_tble_material_stages_table', 45),
(114, '2023_05_15_142629_add_service_id_tbl_service_table', 46),
(115, '2023_05_15_143055_add_service_id_tbl_service_table', 47),
(116, '2023_05_15_143349_add_service_id_tbl_service_table', 48),
(117, '2023_05_15_165432_add_material_id_tbl_material_table', 49),
(118, '2023_05_18_162006_add_cpmg_action_ip_to_nvservicestatus', 50),
(119, '2023_05_18_162745_add_ces_action_ip_to_nvservicestatus', 50),
(120, '2023_05_18_170457_add_hod_action_ip_to_nvservicestatus', 50),
(121, '2023_05_19_122338_add_ceo_nomnee_action_ip_to_nvservicestatus_table', 50);

-- --------------------------------------------------------

--
-- Table structure for table `needvalidations`
--

CREATE TABLE `needvalidations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `company_id` bigint(20) UNSIGNED NOT NULL,
  `budget_type` varchar(255) NOT NULL,
  `budgetary_provision` varchar(255) NOT NULL,
  `proposal_type` varchar(255) NOT NULL,
  `fiscal_year` varchar(255) NOT NULL,
  `service_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `needvalidations`
--

INSERT INTO `needvalidations` (`id`, `company_id`, `budget_type`, `budgetary_provision`, `proposal_type`, `fiscal_year`, `service_id`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 5, 'CAPEX', 'Approved', 'One Time', '2022-23', 1, '2023-05-16 09:59:05', '2023-05-16 09:59:05', '4'),
(2, 6, 'CAPEX', 'Approved', 'Regular', '2024-25', 2, '2023-05-16 09:59:25', '2023-05-16 09:59:25', '4'),
(3, 5, 'CAPEX', 'Approved', 'One Time', '2023-24', 1, '2023-05-17 10:04:43', '2023-05-17 10:04:43', '4'),
(4, 5, 'OPEX', 'Additional', 'One Time', '2023-24', 1, '2023-05-18 09:55:33', '2023-05-18 09:55:33', '1'),
(5, 6, 'CAPEX', 'Approved', 'One Time', '2022-23', 2, '2023-05-19 06:47:09', '2023-05-19 06:47:09', '5'),
(6, 5, 'OPEX', 'Approved', 'Regular', '2023-24', 2, '2023-05-19 07:00:49', '2023-05-19 07:00:49', '5'),
(7, 6, 'OPEX', 'Additional', 'One Time', '2022-23', 1, '2023-05-19 07:01:14', '2023-05-19 07:01:14', '5'),
(8, 6, 'OPEX', 'Approved', 'Regular', '2022-23', 2, '2023-05-19 07:04:11', '2023-05-19 07:04:11', '18'),
(9, 6, 'CAPEX', 'Additional', 'Regular', '2024-25', 1, '2023-05-19 08:32:31', '2023-05-19 08:32:31', '18');

-- --------------------------------------------------------

--
-- Table structure for table `nvmaterialstatus`
--

CREATE TABLE `nvmaterialstatus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` varchar(255) DEFAULT NULL,
  `hod_id` varchar(255) DEFAULT NULL,
  `hod_status` tinyint(4) NOT NULL DEFAULT 0,
  `hod_timestamp` timestamp NULL DEFAULT NULL,
  `cpmg_id` varchar(255) DEFAULT NULL,
  `cpmg_status` tinyint(4) NOT NULL DEFAULT 0,
  `cpmg_timestamp` timestamp NULL DEFAULT NULL,
  `bt_id` varchar(255) DEFAULT NULL,
  `bt_status` tinyint(4) NOT NULL DEFAULT 0,
  `bt_timestamp` timestamp NULL DEFAULT NULL,
  `ceo_nominee_id` varchar(255) DEFAULT NULL,
  `ceo_nominee_status` tinyint(4) NOT NULL DEFAULT 0,
  `ceo_nominee_timestamp` timestamp NULL DEFAULT NULL,
  `ceo_id` varchar(255) DEFAULT NULL,
  `ceo_status` tinyint(4) NOT NULL DEFAULT 0,
  `ceo_timestamp` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nvmaterialstatus`
--

INSERT INTO `nvmaterialstatus` (`id`, `service_id`, `hod_id`, `hod_status`, `hod_timestamp`, `cpmg_id`, `cpmg_status`, `cpmg_timestamp`, `bt_id`, `bt_status`, `bt_timestamp`, `ceo_nominee_id`, `ceo_nominee_status`, `ceo_nominee_timestamp`, `ceo_id`, `ceo_status`, `ceo_timestamp`, `created_at`, `updated_at`) VALUES
(1, '1', NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2023-05-03 10:23:02', '2023-05-03 10:23:02'),
(2, '1', NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, NULL, 0, NULL, '2023-05-08 06:16:13', '2023-05-08 06:16:13');

-- --------------------------------------------------------

--
-- Table structure for table `nvservicestatus`
--

CREATE TABLE `nvservicestatus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nv_id` varchar(255) DEFAULT NULL,
  `service_id` varchar(255) DEFAULT NULL,
  `hod_id` varchar(255) DEFAULT NULL,
  `hod_status` tinyint(4) NOT NULL DEFAULT 0,
  `hod_timestamp` timestamp NULL DEFAULT NULL,
  `hod_remark` text DEFAULT NULL,
  `cpmg_id` varchar(255) DEFAULT NULL,
  `cpmg_status` tinyint(4) NOT NULL DEFAULT 0,
  `cpmg_timestamp` timestamp NULL DEFAULT NULL,
  `cpmg_remark` text DEFAULT NULL,
  `ces_id` varchar(255) DEFAULT NULL,
  `ces_status` tinyint(4) NOT NULL DEFAULT 0,
  `ces_timestamp` timestamp NULL DEFAULT NULL,
  `ces_remark` text DEFAULT NULL,
  `bt_id` varchar(255) DEFAULT NULL,
  `bt_status` tinyint(4) NOT NULL DEFAULT 0,
  `bt_timestamp` timestamp NULL DEFAULT NULL,
  `bt_remark` text DEFAULT NULL,
  `ceo_nominee_id` varchar(255) DEFAULT NULL,
  `ceo_nominee_status` tinyint(4) NOT NULL DEFAULT 0,
  `ceo_nominee_timestamp` timestamp NULL DEFAULT NULL,
  `ceo_nominee_remark` text DEFAULT NULL,
  `ceo_id` varchar(255) DEFAULT NULL,
  `ceo_status` tinyint(4) NOT NULL DEFAULT 0,
  `ceo_timestamp` timestamp NULL DEFAULT NULL,
  `ceo_remark` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `material_id` varchar(255) DEFAULT NULL,
  `cpmg_action_ip` varchar(255) DEFAULT NULL,
  `bt_action_ip` varchar(255) DEFAULT NULL,
  `ces_action_ip` varchar(255) DEFAULT NULL,
  `ceo_action_ip` varchar(255) DEFAULT NULL,
  `hod_action_ip` varchar(255) DEFAULT NULL,
  `ceo_nomnee_action_ip` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nvservicestatus`
--

INSERT INTO `nvservicestatus` (`id`, `nv_id`, `service_id`, `hod_id`, `hod_status`, `hod_timestamp`, `hod_remark`, `cpmg_id`, `cpmg_status`, `cpmg_timestamp`, `cpmg_remark`, `ces_id`, `ces_status`, `ces_timestamp`, `ces_remark`, `bt_id`, `bt_status`, `bt_timestamp`, `bt_remark`, `ceo_nominee_id`, `ceo_nominee_status`, `ceo_nominee_timestamp`, `ceo_nominee_remark`, `ceo_id`, `ceo_status`, `ceo_timestamp`, `ceo_remark`, `created_at`, `updated_at`, `material_id`, `cpmg_action_ip`, `bt_action_ip`, `ces_action_ip`, `ceo_action_ip`, `hod_action_ip`, `ceo_nomnee_action_ip`) VALUES
(1, '1', NULL, '6', 1, '2023-05-16 10:05:00', 'Approve', '13', 1, '2023-05-16 10:07:00', 'approved', '14', 1, '2023-05-16 10:09:00', 'approved', '15', 1, '2023-05-16 10:10:00', 'approved', '16', 1, '2023-05-16 10:10:00', 'approved', '17', 1, '2023-05-16 10:11:00', 'approved', '2023-05-16 10:01:53', '2023-05-16 10:11:51', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(2, '2', '1', '6', 2, '2023-05-16 10:17:00', 'Rejected', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '2023-05-16 10:16:36', '2023-05-16 10:17:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, '2', '2', '6', 1, '2023-05-16 10:27:00', 'approved', '13', 1, '2023-05-17 05:51:00', 'approved', '14', 2, '2023-05-17 05:52:00', 'Rejected', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '2023-05-16 10:19:54', '2023-05-17 05:52:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, '2', '3', '6', 1, '2023-05-17 10:53:00', 'sdrftghjuk', '13', 1, '2023-05-17 11:19:00', 'awsedfgh', '14', 1, '2023-05-17 11:24:00', 'ap', '15', 1, '2023-05-17 11:27:00', 'apkjhgf', '16', 2, '2023-05-17 11:34:00', 'sdfghb', NULL, 0, NULL, NULL, '2023-05-17 05:54:16', '2023-05-17 11:34:07', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(5, '3', NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '2023-05-17 10:05:58', '2023-05-17 10:05:58', '2', NULL, NULL, NULL, NULL, NULL, NULL),
(6, '2', '4', '6', 1, '2023-05-17 13:50:00', 'approved', '13', 2, '2023-05-17 13:56:00', 'Reject by cpmg', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '2023-05-17 12:59:21', '2023-05-17 13:56:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(7, '8', '5', '6', 1, '2023-05-19 07:10:00', 'apprved', '13', 2, '2023-05-19 07:11:00', 'reject', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '2023-05-19 07:09:13', '2023-05-19 07:11:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(8, '9', NULL, '6', 1, '2023-05-19 08:40:00', 'approved', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, '2023-05-19 08:33:41', '2023-05-19 08:40:30', '3', NULL, NULL, NULL, NULL, '127.0.0.1', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `opex`
--

CREATE TABLE `opex` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `sub_department` varchar(225) NOT NULL,
  `expenses_head` varchar(255) NOT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `initial_approved_budget` bigint(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `opex`
--

INSERT INTO `opex` (`id`, `department_id`, `sub_department`, `expenses_head`, `activity`, `initial_approved_budget`, `created_at`, `updated_at`) VALUES
(1, 1, '2', 'Test', '0', 10000, '2023-05-09 06:28:44', '2023-05-09 06:28:44');

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `otp` varchar(255) NOT NULL,
  `expiry` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otps`
--

INSERT INTO `otps` (`id`, `user_id`, `email`, `phone`, `otp`, `expiry`, `used_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, '4716', '2023-05-19 08:54:40', '2023-05-19 08:54:40', '2023-05-03 05:11:05', '2023-05-19 08:54:40'),
(2, 4, NULL, NULL, '9513', '2023-05-18 12:32:34', '2023-05-18 12:32:34', '2023-05-03 05:23:42', '2023-05-18 12:32:34'),
(3, 6, NULL, NULL, '6245', '2023-05-19 08:39:41', '2023-05-19 08:39:41', '2023-05-03 05:25:14', '2023-05-19 08:39:41'),
(4, 6, NULL, NULL, '9744', '2023-05-03 05:26:08', '2023-05-03 05:26:08', '2023-05-03 05:25:52', '2023-05-03 05:26:08'),
(5, 1, NULL, NULL, '8469', '2023-05-03 05:42:31', '2023-05-03 05:42:31', '2023-05-03 05:42:18', '2023-05-03 05:42:31'),
(6, 6, NULL, NULL, '3965', '2023-05-03 06:10:04', '2023-05-03 06:10:04', '2023-05-03 06:09:51', '2023-05-03 06:10:04'),
(7, 4, NULL, NULL, '3451', '2023-05-03 07:36:34', '2023-05-03 07:36:34', '2023-05-03 07:35:57', '2023-05-03 07:36:34'),
(8, 6, NULL, NULL, '9026', '2023-05-03 07:38:32', '2023-05-03 07:38:32', '2023-05-03 07:38:12', '2023-05-03 07:38:32'),
(9, 4, NULL, NULL, '5071', '2023-05-03 07:40:50', '2023-05-03 07:40:50', '2023-05-03 07:40:26', '2023-05-03 07:40:50'),
(10, 4, NULL, NULL, '2396', '2023-05-03 08:11:35', '2023-05-03 08:11:35', '2023-05-03 08:11:15', '2023-05-03 08:11:35'),
(11, 6, NULL, NULL, '9174', '2023-05-03 08:12:29', '2023-05-03 08:12:29', '2023-05-03 08:12:05', '2023-05-03 08:12:29'),
(12, 1, NULL, NULL, '9194', '2023-05-03 08:32:07', '2023-05-03 08:32:07', '2023-05-03 08:31:46', '2023-05-03 08:32:07'),
(13, 1, NULL, NULL, '8752', '2023-05-03 08:39:56', NULL, '2023-05-03 08:34:56', '2023-05-03 08:34:56'),
(14, 1, NULL, NULL, '8730', '2023-05-03 08:40:12', NULL, '2023-05-03 08:35:12', '2023-05-03 08:35:12'),
(15, 1, NULL, NULL, '9186', '2023-05-03 08:42:08', NULL, '2023-05-03 08:37:08', '2023-05-03 08:37:08'),
(16, 1, NULL, NULL, '2286', '2023-05-03 08:42:18', NULL, '2023-05-03 08:37:18', '2023-05-03 08:37:18'),
(17, 1, NULL, NULL, '5139', '2023-05-03 08:52:27', NULL, '2023-05-03 08:47:27', '2023-05-03 08:47:27'),
(18, 1, NULL, NULL, '7439', '2023-05-03 09:02:56', NULL, '2023-05-03 08:57:56', '2023-05-03 08:57:56'),
(19, 1, NULL, NULL, '5622', '2023-05-03 09:04:45', NULL, '2023-05-03 08:59:45', '2023-05-03 08:59:45'),
(20, 1, NULL, NULL, '6398', '2023-05-03 09:07:01', NULL, '2023-05-03 09:02:01', '2023-05-03 09:02:01'),
(21, 1, NULL, NULL, '2639', '2023-05-03 09:07:40', NULL, '2023-05-03 09:02:40', '2023-05-03 09:02:40'),
(22, 8, NULL, NULL, '2828', '2023-05-03 09:47:28', '2023-05-03 09:47:28', '2023-05-03 09:47:06', '2023-05-03 09:47:28'),
(23, 3, NULL, NULL, '1224', '2023-05-03 09:50:07', '2023-05-03 09:50:07', '2023-05-03 09:49:52', '2023-05-03 09:50:07'),
(24, 1, NULL, NULL, '7686', '2023-05-03 09:52:05', '2023-05-03 09:52:05', '2023-05-03 09:51:34', '2023-05-03 09:52:05'),
(25, 3, NULL, NULL, '6816', '2023-05-03 09:54:47', '2023-05-03 09:54:47', '2023-05-03 09:54:24', '2023-05-03 09:54:47'),
(26, 1, NULL, NULL, '6026', '2023-05-03 10:01:36', '2023-05-03 10:01:36', '2023-05-03 10:01:08', '2023-05-03 10:01:36'),
(27, 7, NULL, NULL, '7068', '2023-05-11 08:31:18', '2023-05-11 08:31:18', '2023-05-03 10:02:17', '2023-05-11 08:31:18'),
(28, 3, NULL, NULL, '7771', '2023-05-03 10:04:00', '2023-05-03 10:04:00', '2023-05-03 10:03:44', '2023-05-03 10:04:00'),
(29, 1, NULL, NULL, '3720', '2023-05-03 10:05:26', '2023-05-03 10:05:26', '2023-05-03 10:04:52', '2023-05-03 10:05:26'),
(30, 9, NULL, NULL, '8287', '2023-05-09 08:57:05', '2023-05-09 08:57:05', '2023-05-03 10:07:18', '2023-05-09 08:57:05'),
(31, 7, NULL, NULL, '6415', '2023-05-03 10:08:46', '2023-05-03 10:08:46', '2023-05-03 10:08:29', '2023-05-03 10:08:46'),
(32, 1, NULL, NULL, '1015', '2023-05-03 10:19:46', '2023-05-03 10:19:46', '2023-05-03 10:19:27', '2023-05-03 10:19:46'),
(33, 4, NULL, NULL, '8917', '2023-05-03 11:20:29', '2023-05-03 11:20:29', '2023-05-03 11:20:11', '2023-05-03 11:20:29'),
(34, 6, NULL, NULL, '7984', '2023-05-03 11:37:20', '2023-05-03 11:37:20', '2023-05-03 11:37:03', '2023-05-03 11:37:20'),
(35, 1, NULL, NULL, '5585', '2023-05-03 11:52:53', '2023-05-03 11:52:53', '2023-05-03 11:52:38', '2023-05-03 11:52:53'),
(36, 1, NULL, NULL, '4225', '2023-05-04 04:30:54', '2023-05-04 04:30:54', '2023-05-04 04:30:33', '2023-05-04 04:30:54'),
(37, 4, NULL, NULL, '4407', '2023-05-04 04:32:18', '2023-05-04 04:32:18', '2023-05-04 04:31:58', '2023-05-04 04:32:18'),
(38, 5, NULL, NULL, '5878', '2023-05-19 07:00:20', '2023-05-19 07:00:20', '2023-05-08 12:44:47', '2023-05-19 07:00:20'),
(39, 13, NULL, NULL, '9865', '2023-05-19 07:11:23', '2023-05-19 07:11:23', '2023-05-11 08:02:05', '2023-05-19 07:11:23'),
(40, 14, NULL, NULL, '2377', '2023-05-19 07:12:51', '2023-05-19 07:12:51', '2023-05-11 09:33:53', '2023-05-19 07:12:51'),
(41, 15, NULL, NULL, '1252', '2023-05-17 11:26:21', '2023-05-17 11:26:21', '2023-05-11 10:11:44', '2023-05-17 11:26:21'),
(42, 16, NULL, NULL, '9614', '2023-05-17 11:29:57', '2023-05-17 11:29:57', '2023-05-11 10:14:37', '2023-05-17 11:29:57'),
(43, 17, NULL, NULL, '4707', '2023-05-19 06:50:57', '2023-05-19 06:50:57', '2023-05-11 10:18:05', '2023-05-19 06:50:57'),
(44, 18, NULL, NULL, '4929', '2023-05-19 08:38:45', '2023-05-19 08:38:45', '2023-05-19 07:03:47', '2023-05-19 08:38:45');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ability_id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `entity_type` varchar(255) DEFAULT NULL,
  `forbidden` tinyint(1) NOT NULL DEFAULT 0,
  `scope` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `ability_id`, `entity_id`, `entity_type`, `forbidden`, `scope`) VALUES
(21, 4, 4, 'roles', 0, NULL),
(22, 4, 5, 'roles', 0, NULL),
(23, 4, 6, 'roles', 0, NULL),
(24, 4, 7, 'roles', 0, NULL),
(25, 4, 8, 'roles', 0, NULL),
(155, 4, 3, 'roles', 0, NULL),
(179, 3, 10, 'roles', 0, NULL),
(180, 1, 11, 'roles', 0, NULL),
(181, 1, 12, 'roles', 0, NULL),
(1049, 27, 14, 'roles', 0, NULL),
(1050, 69, 14, 'roles', 0, NULL),
(1051, 73, 14, 'roles', 0, NULL),
(1052, 74, 14, 'roles', 0, NULL),
(1053, 75, 14, 'roles', 0, NULL),
(1262, 7, 13, 'roles', 0, NULL),
(1263, 77, 13, 'roles', 0, NULL),
(1264, 7, 15, 'roles', 0, NULL),
(1265, 77, 15, 'roles', 0, NULL),
(1649, 7, 5, 'roles', 0, NULL),
(1650, 7, 6, 'roles', 0, NULL),
(1651, 7, 7, 'roles', 0, NULL),
(1652, 7, 8, 'roles', 0, NULL),
(1773, 7, 2, 'roles', 0, NULL),
(1774, 7, 10, 'roles', 0, NULL),
(2307, 7, 9, 'roles', 0, NULL),
(2308, 31, 9, 'roles', 0, NULL),
(2309, 55, 9, 'roles', 0, NULL),
(2310, 60, 9, 'roles', 0, NULL),
(2311, 61, 9, 'roles', 0, NULL),
(2312, 62, 9, 'roles', 0, NULL),
(2313, 83, 9, 'roles', 0, NULL),
(2314, 84, 9, 'roles', 0, NULL),
(2315, 85, 9, 'roles', 0, NULL),
(2316, 91, 9, 'roles', 0, NULL),
(2827, 1, 1, 'roles', 0, NULL),
(2828, 2, 1, 'roles', 0, NULL),
(2829, 3, 1, 'roles', 0, NULL),
(2830, 4, 1, 'roles', 0, NULL),
(2831, 5, 1, 'roles', 0, NULL),
(2832, 6, 1, 'roles', 0, NULL),
(2833, 7, 1, 'roles', 0, NULL),
(2834, 22, 1, 'roles', 0, NULL),
(2835, 23, 1, 'roles', 0, NULL),
(2836, 24, 1, 'roles', 0, NULL),
(2837, 25, 1, 'roles', 0, NULL),
(2838, 26, 1, 'roles', 0, NULL),
(2839, 27, 1, 'roles', 0, NULL),
(2840, 28, 1, 'roles', 0, NULL),
(2841, 29, 1, 'roles', 0, NULL),
(2842, 30, 1, 'roles', 0, NULL),
(2843, 31, 1, 'roles', 0, NULL),
(2844, 32, 1, 'roles', 0, NULL),
(2845, 33, 1, 'roles', 0, NULL),
(2846, 34, 1, 'roles', 0, NULL),
(2847, 35, 1, 'roles', 0, NULL),
(2848, 36, 1, 'roles', 0, NULL),
(2849, 37, 1, 'roles', 0, NULL),
(2850, 38, 1, 'roles', 0, NULL),
(2851, 39, 1, 'roles', 0, NULL),
(2852, 40, 1, 'roles', 0, NULL),
(2853, 41, 1, 'roles', 0, NULL),
(2854, 42, 1, 'roles', 0, NULL),
(2855, 43, 1, 'roles', 0, NULL),
(2856, 44, 1, 'roles', 0, NULL),
(2857, 45, 1, 'roles', 0, NULL),
(2858, 46, 1, 'roles', 0, NULL),
(2859, 47, 1, 'roles', 0, NULL),
(2860, 48, 1, 'roles', 0, NULL),
(2861, 49, 1, 'roles', 0, NULL),
(2862, 50, 1, 'roles', 0, NULL),
(2863, 51, 1, 'roles', 0, NULL),
(2864, 52, 1, 'roles', 0, NULL),
(2865, 53, 1, 'roles', 0, NULL),
(2866, 54, 1, 'roles', 0, NULL),
(2867, 55, 1, 'roles', 0, NULL),
(2868, 57, 1, 'roles', 0, NULL),
(2869, 58, 1, 'roles', 0, NULL),
(2870, 59, 1, 'roles', 0, NULL),
(2871, 60, 1, 'roles', 0, NULL),
(2872, 61, 1, 'roles', 0, NULL),
(2873, 62, 1, 'roles', 0, NULL),
(2874, 63, 1, 'roles', 0, NULL),
(2875, 64, 1, 'roles', 0, NULL),
(2876, 65, 1, 'roles', 0, NULL),
(2877, 66, 1, 'roles', 0, NULL),
(2878, 67, 1, 'roles', 0, NULL),
(2879, 68, 1, 'roles', 0, NULL),
(2880, 69, 1, 'roles', 0, NULL),
(2881, 70, 1, 'roles', 0, NULL),
(2882, 71, 1, 'roles', 0, NULL),
(2883, 72, 1, 'roles', 0, NULL),
(2884, 73, 1, 'roles', 0, NULL),
(2885, 74, 1, 'roles', 0, NULL),
(2886, 76, 1, 'roles', 0, NULL),
(2887, 77, 1, 'roles', 0, NULL),
(2888, 78, 1, 'roles', 0, NULL),
(2889, 79, 1, 'roles', 0, NULL),
(2890, 80, 1, 'roles', 0, NULL),
(2891, 81, 1, 'roles', 0, NULL),
(2892, 82, 1, 'roles', 0, NULL),
(2893, 83, 1, 'roles', 0, NULL),
(2894, 84, 1, 'roles', 0, NULL),
(2895, 85, 1, 'roles', 0, NULL),
(2896, 86, 1, 'roles', 0, NULL),
(2897, 87, 1, 'roles', 0, NULL),
(2898, 88, 1, 'roles', 0, NULL),
(2899, 89, 1, 'roles', 0, NULL),
(2900, 90, 1, 'roles', 0, NULL),
(2901, 91, 1, 'roles', 0, NULL),
(2902, 92, 1, 'roles', 0, NULL),
(2903, 93, 1, 'roles', 0, NULL),
(2904, 94, 1, 'roles', 0, NULL),
(2905, 95, 1, 'roles', 0, NULL),
(2906, 96, 1, 'roles', 0, NULL),
(2907, 97, 1, 'roles', 0, NULL),
(2908, 98, 1, 'roles', 0, NULL),
(2909, 99, 1, 'roles', 0, NULL),
(2910, 100, 1, 'roles', 0, NULL),
(2911, 101, 1, 'roles', 0, NULL),
(2912, 102, 1, 'roles', 0, NULL),
(2913, 103, 1, 'roles', 0, NULL),
(2914, 104, 1, 'roles', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 37, 'apiToken', '85da88ab2d22a018014515ebc66cdb67a9c86cc58bffb46f8c353659da33550d', '[\"*\"]', '2023-04-06 04:49:57', '2023-04-06 04:40:55', '2023-04-06 04:49:57');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `scope` int(11) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `title`, `scope`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'Admin', NULL, 0, NULL, '2023-04-11 05:42:35'),
(2, 'HOD', 'H o d', NULL, 0, '2023-04-10 09:22:19', '2023-04-21 06:54:05'),
(5, 'CPMG', 'C p m g', NULL, 1, '2023-04-17 12:54:29', '2023-04-17 12:54:29'),
(6, 'Buget Team', 'Buget team', NULL, 1, '2023-04-17 12:55:02', '2023-04-17 12:55:02'),
(7, 'CEO Nominee', 'C e o nominee', NULL, 1, '2023-04-17 12:55:37', '2023-04-17 12:55:37'),
(8, 'CEO', 'C e o', NULL, 1, '2023-04-17 12:56:05', '2023-04-17 12:56:05'),
(9, 'User', 'User', NULL, 0, '2023-04-19 05:23:40', '2023-04-20 05:12:57'),
(10, 'CES', 'CES', NULL, 1, '2023-04-21 09:02:49', '2023-04-21 09:02:49');

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`id`, `name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Material', 1, '2023-04-10 11:07:10', '2023-04-10 11:12:53'),
(2, 'Service', 1, '2023-04-11 12:10:18', '2023-04-11 12:10:18');

-- --------------------------------------------------------

--
-- Table structure for table `taks`
--

CREATE TABLE `taks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_description` varchar(255) NOT NULL,
  `frequency` text NOT NULL,
  `company_id` varchar(255) NOT NULL,
  `location_id` varchar(255) NOT NULL,
  `assigne_id` varchar(255) NOT NULL,
  `floor` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `d1` varchar(255) DEFAULT NULL,
  `d2` varchar(255) DEFAULT NULL,
  `d3` varchar(255) DEFAULT NULL,
  `d4` varchar(255) DEFAULT NULL,
  `d5` varchar(255) DEFAULT NULL,
  `d6` varchar(255) DEFAULT NULL,
  `d7` varchar(255) DEFAULT NULL,
  `m1` varchar(255) DEFAULT NULL,
  `m2` varchar(255) DEFAULT NULL,
  `m3` varchar(255) DEFAULT NULL,
  `m4` varchar(255) DEFAULT NULL,
  `m5` varchar(255) DEFAULT NULL,
  `m6` varchar(255) DEFAULT NULL,
  `m7` varchar(255) DEFAULT NULL,
  `m8` varchar(255) DEFAULT NULL,
  `m9` varchar(255) DEFAULT NULL,
  `m10` varchar(255) DEFAULT NULL,
  `m11` varchar(255) DEFAULT NULL,
  `m12` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `role_id` varchar(255) DEFAULT NULL,
  `tickets_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_material`
--

CREATE TABLE `tbl_material` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dept_id` varchar(255) NOT NULL,
  `nv_id` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `material_id` varchar(255) DEFAULT NULL,
  `dop` varchar(255) DEFAULT NULL,
  `proposal_name` longtext DEFAULT NULL,
  `background` longtext DEFAULT NULL,
  `just_Prop` longtext DEFAULT NULL,
  `cost_trend_year1` varchar(255) DEFAULT NULL,
  `cost_trend_year2` varchar(255) DEFAULT NULL,
  `cost_trend_year3` varchar(255) DEFAULT NULL,
  `benefit` longtext DEFAULT NULL,
  `imp_to` date DEFAULT NULL,
  `imp_from` date DEFAULT NULL,
  `imp_plan` longtext DEFAULT NULL,
  `prop_type` varchar(255) DEFAULT NULL,
  `worktype` varchar(255) DEFAULT NULL,
  `scheme_no` varchar(255) DEFAULT NULL,
  `scheme_des` longtext DEFAULT NULL,
  `scheme_type` varchar(255) DEFAULT NULL,
  `derc_ref_no` varchar(255) DEFAULT NULL,
  `derc_approval` varchar(255) DEFAULT NULL,
  `derc_app_date` date DEFAULT NULL,
  `material_code` varchar(255) DEFAULT NULL,
  `mat_des` longtext DEFAULT NULL,
  `mat_just` longtext DEFAULT NULL,
  `mat_group` varchar(255) DEFAULT NULL,
  `uom` varchar(255) DEFAULT NULL,
  `rate` varchar(255) DEFAULT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `total_amount` varchar(255) DEFAULT NULL,
  `delivery_schedule` varchar(255) DEFAULT NULL,
  `service_code` varchar(255) DEFAULT NULL,
  `ser_des` longtext DEFAULT NULL,
  `ser_uom` varchar(255) DEFAULT NULL,
  `ser_rate` varchar(255) DEFAULT NULL,
  `ser_quantity` varchar(255) DEFAULT NULL,
  `ser_total_amount` varchar(255) DEFAULT NULL,
  `ser_rate_ref` varchar(255) DEFAULT NULL,
  `budget_avl` varchar(255) DEFAULT NULL,
  `ptr_mva` varchar(255) DEFAULT NULL,
  `dt_mva` varchar(255) DEFAULT NULL,
  `ehv_line` varchar(255) DEFAULT NULL,
  `ht_line` varchar(255) DEFAULT NULL,
  `lt_line` varchar(255) DEFAULT NULL,
  `root_cause_analysis` longtext DEFAULT NULL,
  `cause_analysis` longtext DEFAULT NULL,
  `special_remarks` longtext DEFAULT NULL,
  `total_budget_material` decimal(5,2) DEFAULT NULL,
  `prop_number` varchar(255) DEFAULT NULL,
  `mode_award` varchar(255) DEFAULT NULL,
  `past_3_year_actual_cost_fy` varchar(255) DEFAULT NULL,
  `past_3_year_actual_cost` varchar(255) DEFAULT NULL,
  `past_3_year_actual_cost_service` varchar(255) DEFAULT NULL,
  `past_practice_follow` varchar(255) DEFAULT NULL,
  `amc_prop_start_date` date DEFAULT NULL,
  `amc_prop_end_date` date DEFAULT NULL,
  `estimate_amount_of_service` varchar(255) DEFAULT NULL,
  `estimate_amount_of_service_civil` varchar(255) DEFAULT NULL,
  `estimate_amount_of_rr_charge` varchar(255) DEFAULT NULL,
  `estimate_amount_other` varchar(255) DEFAULT NULL,
  `total_budget_service` decimal(5,2) DEFAULT NULL,
  `total_budget_both` decimal(5,2) DEFAULT NULL,
  `cap_add` varchar(255) DEFAULT NULL,
  `ser_rel_nv` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_material`
--

INSERT INTO `tbl_material` (`id`, `dept_id`, `nv_id`, `user_id`, `material_id`, `dop`, `proposal_name`, `background`, `just_Prop`, `cost_trend_year1`, `cost_trend_year2`, `cost_trend_year3`, `benefit`, `imp_to`, `imp_from`, `imp_plan`, `prop_type`, `worktype`, `scheme_no`, `scheme_des`, `scheme_type`, `derc_ref_no`, `derc_approval`, `derc_app_date`, `material_code`, `mat_des`, `mat_just`, `mat_group`, `uom`, `rate`, `quantity`, `total_amount`, `delivery_schedule`, `service_code`, `ser_des`, `ser_uom`, `ser_rate`, `ser_quantity`, `ser_total_amount`, `ser_rate_ref`, `budget_avl`, `ptr_mva`, `dt_mva`, `ehv_line`, `ht_line`, `lt_line`, `root_cause_analysis`, `cause_analysis`, `special_remarks`, `total_budget_material`, `prop_number`, `mode_award`, `past_3_year_actual_cost_fy`, `past_3_year_actual_cost`, `past_3_year_actual_cost_service`, `past_practice_follow`, `amc_prop_start_date`, `amc_prop_end_date`, `estimate_amount_of_service`, `estimate_amount_of_service_civil`, `estimate_amount_of_rr_charge`, `estimate_amount_other`, `total_budget_service`, `total_budget_both`, `cap_add`, `ser_rel_nv`, `created_at`, `updated_at`, `status`) VALUES
(1, '3', '1', '4', NULL, '12345', 'ffgtv', 'derf', 'rftg', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '10000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5', '3', '2023-05-16 15:31:52', '2023-05-16 15:33:21', 1),
(2, '3', '3', '4', NULL, '4565', 'testdata', 'testdata', 'testdata', '45,56,57', 'abc,dfgh,sdfghj', '2023,2022,2021', NULL, NULL, NULL, NULL, NULL, NULL, '123', NULL, NULL, NULL, NULL, NULL, '', '', NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '10000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 23.00, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5', '3', '2023-05-17 15:35:58', '2023-05-17 16:06:07', 1),
(3, '2', '9', '18', NULL, '12345999', NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '123456,123456', 'Test material data,Test material data', NULL, ',', 'Test,Test', ',', ',', ',', ',', '', '', '', '', '', '', '', '200000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '5', '3', '2023-05-19 14:03:41', '2023-05-19 14:03:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_material_doc`
--

CREATE TABLE `tbl_material_doc` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` varchar(255) DEFAULT NULL,
  `previous_work_order` varchar(255) DEFAULT NULL,
  `derc_stakeholder_approvals` varchar(255) DEFAULT NULL,
  `consumption_details` varchar(255) DEFAULT NULL,
  `vend_quatation` varchar(255) DEFAULT NULL,
  `photo_product` varchar(255) DEFAULT NULL,
  `material_procurement` varchar(255) DEFAULT NULL,
  `budget_for_both` varchar(255) DEFAULT NULL,
  `others` varchar(255) DEFAULT NULL,
  `new_product` varchar(255) DEFAULT NULL,
  `cm_rate_ref` varchar(255) DEFAULT NULL,
  `vendor_quatation` varchar(255) DEFAULT NULL,
  `last_purchase_price` varchar(255) DEFAULT NULL,
  `user_estimation` varchar(255) DEFAULT NULL,
  `cost_calculation_for_service` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_material_doc`
--

INSERT INTO `tbl_material_doc` (`id`, `service_id`, `previous_work_order`, `derc_stakeholder_approvals`, `consumption_details`, `vend_quatation`, `photo_product`, `material_procurement`, `budget_for_both`, `others`, `new_product`, `cm_rate_ref`, `vendor_quatation`, `last_purchase_price`, `user_estimation`, `cost_calculation_for_service`, `created_by`, `created_at`, `updated_at`, `status`) VALUES
(1, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '4', '2023-05-16 15:31:53', '2023-05-16 15:33:21', 1),
(2, '2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '4', '2023-05-17 15:35:58', '2023-05-17 18:29:39', 1),
(3, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '18', '2023-05-19 14:03:41', '2023-05-19 14:03:41', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_service`
--

CREATE TABLE `tbl_service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dept_id` varchar(255) NOT NULL,
  `nv_id` varchar(255) DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `service_id` varchar(255) DEFAULT NULL,
  `dop_ref_no` varchar(255) DEFAULT NULL,
  `proposal_name` longtext DEFAULT NULL,
  `background` longtext DEFAULT NULL,
  `just_of_proposal` longtext DEFAULT NULL,
  `past_3_year_actual_cost_fy` varchar(255) DEFAULT NULL,
  `past_3_year_actual_cost` varchar(255) DEFAULT NULL,
  `past_3_year_actual_cost_service` varchar(255) DEFAULT NULL,
  `benefit` longtext DEFAULT NULL,
  `implementation_period_from` varchar(255) DEFAULT NULL,
  `implementation_period_to` varchar(255) DEFAULT NULL,
  `implementation_plan_year_wise` longtext DEFAULT NULL,
  `type_of_proposal` varchar(255) DEFAULT NULL,
  `mode_of_award_of_service` varchar(255) DEFAULT NULL,
  `amc_proposal_sdate` varchar(255) DEFAULT NULL,
  `amc_proposal_edate` varchar(255) DEFAULT NULL,
  `budget_available` varchar(255) DEFAULT NULL,
  `estimate_amount_of_service` varchar(255) DEFAULT NULL,
  `estimate_amount_of_service_civil` varchar(255) DEFAULT NULL,
  `estimate_amount_of_rr_chnage` varchar(255) DEFAULT NULL,
  `estimate_amount_other` varchar(255) DEFAULT NULL,
  `total_buget` decimal(5,2) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_service`
--

INSERT INTO `tbl_service` (`id`, `dept_id`, `nv_id`, `user_id`, `service_id`, `dop_ref_no`, `proposal_name`, `background`, `just_of_proposal`, `past_3_year_actual_cost_fy`, `past_3_year_actual_cost`, `past_3_year_actual_cost_service`, `benefit`, `implementation_period_from`, `implementation_period_to`, `implementation_plan_year_wise`, `type_of_proposal`, `mode_of_award_of_service`, `amc_proposal_sdate`, `amc_proposal_edate`, `budget_available`, `estimate_amount_of_service`, `estimate_amount_of_service_civil`, `estimate_amount_of_rr_chnage`, `estimate_amount_other`, `total_buget`, `created_at`, `updated_at`, `status`) VALUES
(1, '3', '2', '4', NULL, '123456', 'asdf', 'szdx', 'sdfg', '2019', '47', 'Cleaning', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '546', NULL, NULL, '2023-05-16 15:46:36', '2023-05-16 15:47:30', 1),
(2, '3', '2', '4', '1', '12345', 'asdf', 'szdx', 'sdfg', '2019,2018,2022', '47,45,46', 'Cleaning,abc,abcd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '546', NULL, NULL, '2023-05-16 15:49:54', '2023-05-16 15:49:54', 1),
(3, '3', '2', '4', '2', '12345', 'asdf', 'szdx', 'sdfg', '2019,2018,2022', '47,45,46', 'Cleaning,abc,abcd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '66565', NULL, NULL, '546', NULL, NULL, '2023-05-17 11:24:16', '2023-05-17 11:24:16', 1),
(4, '3', '2', '4', '3', '12345', 'asdf', 'szdx', 'sdfg', '2019,2018,2022', '47,45,46', 'Cleaning,abc,abcd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '66565', NULL, NULL, '546', NULL, NULL, '2023-05-17 18:29:21', '2023-05-17 18:29:21', 1),
(5, '2', '8', '18', NULL, '1234567890', NULL, NULL, NULL, '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2023-05-19 12:39:13', '2023-05-19 12:39:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_service_doc`
--

CREATE TABLE `tbl_service_doc` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `service_id` varchar(255) DEFAULT NULL,
  `cost_calculation_for_service` varchar(255) DEFAULT NULL,
  `copy_of_previous_work` varchar(255) DEFAULT NULL,
  `copy_of_derc_other` varchar(255) DEFAULT NULL,
  `consuption_details` varchar(255) DEFAULT NULL,
  `buget_stmt_for_both` varchar(255) DEFAULT NULL,
  `photographs_of_product` varchar(255) DEFAULT NULL,
  `material_procurement` varchar(255) DEFAULT NULL,
  `vendor_quatation` varchar(255) DEFAULT NULL,
  `others` varchar(255) DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  `updated_at` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_service_doc`
--

INSERT INTO `tbl_service_doc` (`id`, `service_id`, `cost_calculation_for_service`, `copy_of_previous_work`, `copy_of_derc_other`, `consuption_details`, `buget_stmt_for_both`, `photographs_of_product`, `material_procurement`, `vendor_quatation`, `others`, `created_by`, `created_at`, `updated_at`, `status`) VALUES
(1, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '4', '2023-05-16 15:46:36', '2023-05-16 15:47:30', 1),
(2, '2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '4', '2023-05-16 15:49:54', '2023-05-16 15:49:54', 1),
(3, '3', NULL, '1684302856.Screenshot from 2023-05-10 13-39-05.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '4', '2023-05-17 11:24:16', '2023-05-17 11:24:16', 1),
(4, '4', NULL, '1684330903.Screenshot from 2023-05-08 12-53-38.png', NULL, '1684330903.Screenshot from 2023-05-10 13-39-05.png', NULL, NULL, NULL, '1684330903.Screenshot from 2023-05-10 13-39-05.png', NULL, '4', '2023-05-17 18:29:21', '2023-05-17 19:11:43', 1),
(5, '5', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '18', '2023-05-19 12:39:13', '2023-05-19 12:39:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` varchar(255) NOT NULL,
  `task_name` varchar(255) NOT NULL,
  `task_description` varchar(255) NOT NULL,
  `company_id` varchar(255) NOT NULL,
  `location_id` varchar(255) NOT NULL,
  `assigne_id` varchar(255) NOT NULL,
  `role_id` varchar(255) NOT NULL,
  `day` varchar(255) NOT NULL,
  `floor` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `img1` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `img3` varchar(255) DEFAULT NULL,
  `img4` varchar(255) DEFAULT NULL,
  `img5` varchar(255) DEFAULT NULL,
  `img6` varchar(255) DEFAULT NULL,
  `img7` varchar(255) DEFAULT NULL,
  `img8` varchar(255) DEFAULT NULL,
  `oc_status` varchar(255) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_password_change_date` timestamp NULL DEFAULT NULL,
  `disabled` tinyint(1) NOT NULL DEFAULT 0,
  `mobile_number` varchar(255) DEFAULT NULL,
  `otp` varchar(255) DEFAULT NULL,
  `otp_expiration` datetime DEFAULT NULL,
  `otp_verify` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `username`, `email_verified_at`, `password`, `remember_token`, `status`, `created_at`, `updated_at`, `last_login_at`, `last_password_change_date`, `disabled`, `mobile_number`, `otp`, `otp_expiration`, `otp_verify`) VALUES
(1, 1, 'Admin', 'admin@gmail.com', NULL, '2022-03-19 18:11:29', '$2y$10$XZ/y/LMcp3oaxyFjZu6iBuRXj0oIhtWGKKgew2.hmLarjFv9GcliW', 'kp70qy8OdbLuK0wLBF6dOpK0S1bXZ8NkR33suUOoRc2ZNqRnKJpVkM7c2T00', 1, '2023-04-10 07:25:23', '2023-05-19 08:54:31', '2023-05-19 04:30:01', NULL, 0, '6294211979', '6648', '2023-05-02 18:12:12', 0),
(4, 9, 'Shipra', 'shipra.a@rediansoftware.com', NULL, '2023-04-19 12:05:10', '$2y$10$AitFrnwCJ7RYIWFjNG28v.K7AxLW.pUnvvc0nlJpG0h3IvE11yMk6', NULL, 1, '2023-04-19 12:05:10', '2023-05-18 13:11:31', '2023-05-18 13:11:31', NULL, 0, '6294411979', '7766', '2023-05-02 18:15:52', 0),
(5, 5, 'Hareram kumar', 'hareram.1y@redianglobal.com', NULL, '2023-04-20 11:12:12', '$2y$10$1PJLoojzMNxso398tyYg3uWvcbk7/cLKTAzwuZhZOUKvp2IM5STvm', NULL, 1, '2023-04-20 11:12:12', '2023-05-19 07:03:15', '2023-05-19 07:03:15', NULL, 0, '8962786250', '2930', '2023-05-03 10:45:51', 0),
(6, 2, 'Preeti', 'preeti.s@rediansoftware.com', NULL, '2023-04-21 07:09:58', '$2y$10$vaHtKiCfba/YoHPqk0JXnu.mfqa1t5K.su24DcmceUcqavrG9VL7q', NULL, 1, '2023-04-21 07:09:58', '2023-05-19 08:39:35', '2023-05-19 08:38:15', NULL, 0, '8602347452', '3675', '2023-05-02 18:23:31', 0),
(7, 2, 'Raushan', 'raushan@gmail.com', NULL, '2023-05-03 09:43:14', '$2y$10$Yb74uDH77GyRx4hsPUkjoeEWki1aDFxRXlJFYwnKghgeR7JTThOKK', NULL, 1, '2023-05-03 09:43:14', '2023-05-11 08:32:29', '2023-05-11 08:32:29', NULL, 0, NULL, NULL, NULL, 0),
(8, 9, 'pushpendra', 'pushpendra@gmail.com', NULL, '2023-05-03 09:46:19', '$2y$10$OZ.UyDJi1VYZ4CXG0Xo9JuQSYj/V0Dtj7F0naE9lptfxV0/qFU.s2', NULL, 1, '2023-05-03 09:46:19', '2023-05-03 09:47:54', '2023-05-03 09:47:54', NULL, 0, NULL, NULL, NULL, 0),
(9, 9, 'ashu', 'ashu@gmail.com', NULL, '2023-05-03 10:06:50', '$2y$10$20vTH7lya0Pwj8ZISMKw3.UL7KBFLdKlmd/TEL3g0.iZL0vd7nOj6', NULL, 1, '2023-05-03 10:06:50', '2023-05-09 09:33:05', '2023-05-09 09:33:05', NULL, 0, NULL, NULL, NULL, 0),
(13, 5, 'Arpan', 'arpan.p@rediansoftware.com', NULL, '2023-05-03 12:20:21', '$2y$10$iiikZweow1iVz7RKTz9xi.BG2W8C4tQXRI21FipNbVnCdxCorCeQK', NULL, 1, '2023-05-03 12:20:21', '2023-05-19 07:12:34', '2023-05-19 07:12:34', NULL, 0, NULL, NULL, NULL, 0),
(14, 10, 'Megha', 'ashu.s@rediansoftware.com', NULL, '2023-05-09 07:27:08', '$2y$10$2T6kTBw4DnHc78wXrPaMguSjLcntFnU.DSlqSs4F3r6V/KKAaLTxm', NULL, 1, '2023-05-09 07:27:08', '2023-05-19 07:13:25', '2023-05-19 07:13:25', NULL, 0, NULL, NULL, NULL, 0),
(15, 6, 'Kundan', 'pooja@rediansoftware.com', NULL, '2023-05-09 08:27:13', '$2y$10$7rrzM3dIH4Qm5kYHsOYog.asaZWLKDfcdFVzfpHhWugYZOJZSSwTS', NULL, 1, '2023-05-09 08:27:13', '2023-05-17 13:26:08', '2023-05-17 11:27:15', NULL, 0, NULL, NULL, NULL, 0),
(16, 7, 'Arunesh', 'arunesh@rediansoftware.com', NULL, '2023-05-09 08:31:49', '$2y$10$wAzZuHoewXzWRUbtKXBnt.QDBbKAYSDvUKaCV5WZzlpfQgpMEXKO6', NULL, 1, '2023-05-09 08:31:49', '2023-05-17 13:25:58', '2023-05-17 11:46:07', NULL, 0, NULL, NULL, NULL, 0),
(17, 8, 'hareram', 'hareram2.y@redianglobal.com', NULL, '2023-05-09 08:32:48', '$2y$10$kvd47auRMKIGM5S23n96eexwi8XuIYkWJc4ekn92xieMy2tXDXVBe', NULL, 1, '2023-05-09 08:32:48', '2023-05-19 07:05:43', '2023-05-19 06:59:50', NULL, 0, NULL, NULL, NULL, 0),
(18, 9, 'hareram test', 'hareram.y@redianglobal.com', NULL, '2023-05-19 07:03:08', '$2y$10$vYmAjOCINOLaaEOE.u4mBuqrxQYmgmurzHtkx.BOOgN55IC4.UCoG', NULL, 1, '2023-05-19 07:03:08', '2023-05-19 08:39:24', '2023-05-19 08:39:24', NULL, 0, NULL, NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abilities`
--
ALTER TABLE `abilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `abilities_scope_index` (`scope`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assigned_roles`
--
ALTER TABLE `assigned_roles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assigned_roles_entity_index` (`entity_id`,`entity_type`,`scope`),
  ADD KEY `assigned_roles_role_id_index` (`role_id`),
  ADD KEY `assigned_roles_scope_index` (`scope`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `capex_master`
--
ALTER TABLE `capex_master`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_email_unique` (`email`),
  ADD KEY `employee_division_id_foreign` (`division_id`),
  ADD KEY `employee_location_id_foreign` (`location_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `locations_divisions_id_foreign` (`divisions_id`);

--
-- Indexes for table `master_material_boq`
--
ALTER TABLE `master_material_boq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `materialboq`
--
ALTER TABLE `materialboq`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `needvalidations`
--
ALTER TABLE `needvalidations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `needvalidations_company_id_foreign` (`company_id`),
  ADD KEY `needvalidations_service_id_foreign` (`service_id`);

--
-- Indexes for table `nvmaterialstatus`
--
ALTER TABLE `nvmaterialstatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nvservicestatus`
--
ALTER TABLE `nvservicestatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `opex`
--
ALTER TABLE `opex`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_entity_index` (`entity_id`,`entity_type`,`scope`),
  ADD KEY `permissions_ability_id_index` (`ability_id`),
  ADD KEY `permissions_scope_index` (`scope`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`,`scope`),
  ADD KEY `roles_scope_index` (`scope`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `taks`
--
ALTER TABLE `taks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_material`
--
ALTER TABLE `tbl_material`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_material_doc`
--
ALTER TABLE `tbl_material_doc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_service`
--
ALTER TABLE `tbl_service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_service_doc`
--
ALTER TABLE `tbl_service_doc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_mobile_number_unique` (`mobile_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abilities`
--
ALTER TABLE `abilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `assigned_roles`
--
ALTER TABLE `assigned_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `capex_master`
--
ALTER TABLE `capex_master`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=254;

--
-- AUTO_INCREMENT for table `master_material_boq`
--
ALTER TABLE `master_material_boq`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `materialboq`
--
ALTER TABLE `materialboq`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `needvalidations`
--
ALTER TABLE `needvalidations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `nvmaterialstatus`
--
ALTER TABLE `nvmaterialstatus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `nvservicestatus`
--
ALTER TABLE `nvservicestatus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `opex`
--
ALTER TABLE `opex`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2915;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `taks`
--
ALTER TABLE `taks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_material`
--
ALTER TABLE `tbl_material`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_material_doc`
--
ALTER TABLE `tbl_material_doc`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_service`
--
ALTER TABLE `tbl_service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_service_doc`
--
ALTER TABLE `tbl_service_doc`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assigned_roles`
--
ALTER TABLE `assigned_roles`
  ADD CONSTRAINT `assigned_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `employee`
--
ALTER TABLE `employee`
  ADD CONSTRAINT `employee_division_id_foreign` FOREIGN KEY (`division_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `employee_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`);

--
-- Constraints for table `locations`
--
ALTER TABLE `locations`
  ADD CONSTRAINT `locations_divisions_id_foreign` FOREIGN KEY (`divisions_id`) REFERENCES `divisions` (`id`);

--
-- Constraints for table `needvalidations`
--
ALTER TABLE `needvalidations`
  ADD CONSTRAINT `needvalidations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `divisions` (`id`),
  ADD CONSTRAINT `needvalidations_service_id_foreign` FOREIGN KEY (`service_id`) REFERENCES `service` (`id`);

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ability_id_foreign` FOREIGN KEY (`ability_id`) REFERENCES `abilities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
