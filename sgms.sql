-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 01, 2026 at 05:13 PM
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
-- Database: `sgms`
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
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `school_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('enrolled','dropped','completed') NOT NULL DEFAULT 'enrolled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `student_id`, `section_id`, `school_year_id`, `status`, `created_at`, `updated_at`) VALUES
(10, 18, 10, 10, 'enrolled', '2026-08-31 06:51:49', '2026-08-31 06:51:49'),
(11, 19, 10, 10, 'enrolled', '2026-08-31 06:51:49', '2026-08-31 06:51:49'),
(20, 21, 16, 10, 'enrolled', '2026-08-31 14:43:58', '2026-08-31 14:43:58');

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
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_assignment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `prelim` decimal(5,2) DEFAULT NULL,
  `midterm` decimal(5,2) DEFAULT NULL,
  `final` decimal(5,2) DEFAULT NULL,
  `final_grade` decimal(5,2) DEFAULT NULL,
  `remarks` enum('Passed','Failed') DEFAULT NULL,
  `status` enum('draft','submitted') NOT NULL DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `teacher_assignment_id`, `student_id`, `enrollment_id`, `subject_id`, `teacher_id`, `prelim`, `midterm`, `final`, `final_grade`, `remarks`, `status`, `submitted_at`, `created_at`, `updated_at`) VALUES
(1, 9, 18, 10, 10, 15, 90.00, 92.00, 93.00, 91.67, 'Passed', 'submitted', '2026-08-31 07:09:29', '2026-08-31 07:09:29', '2026-08-31 07:09:29'),
(2, 9, 19, 11, 10, 15, 82.00, 95.00, 70.00, 82.33, 'Passed', 'submitted', '2026-08-31 07:09:29', '2026-08-31 07:09:29', '2026-08-31 07:09:29');

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
(4, '2026_08_27_000003_add_academic_details_to_users_table', 1),
(5, '2026_08_27_000004_create_subjects_table', 1),
(6, '2026_08_27_000005_create_school_years_table', 1),
(7, '2026_08_27_225531_create_sections_table', 1),
(8, '2026_08_27_225546_create_enrollments_table', 1),
(9, '2026_08_27_225548_create_grades_table', 1),
(10, '2026_08_27_225551_create_teacher_subjects_table', 1),
(11, '2026_08_31_120000_add_description_to_subjects_table', 1),
(12, '2026_08_31_120001_add_semester_and_status_to_teacher_subjects_table', 1),
(13, '2026_08_31_120002_restructure_grades_for_teacher_assignments', 1),
(14, '2026_08_31_130000_add_block_to_users_table', 1),
(15, '2026_08_31_140000_add_block_to_sections_table', 2),
(16, '2026_08_31_140000_drop_department_from_subjects_table', 2),
(17, '2026_08_31_150000_add_submitted_at_to_grades_table', 2),
(18, '2026_08_31_160000_add_phone_to_users_table', 2),
(19, '2026_09_01_000000_create_notification_reads_table', 3),
(20, '2026_09_01_010000_create_settings_table', 4),
(21, '2026_09_01_020000_split_users_name_into_first_and_last', 5);

-- --------------------------------------------------------

--
-- Table structure for table `notification_reads`
--

CREATE TABLE `notification_reads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` varchar(64) NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification_reads`
--

INSERT INTO `notification_reads` (`id`, `user_id`, `notification_id`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 18, 'grade-1', '2026-08-31 15:41:17', '2026-08-31 15:41:17', '2026-08-31 15:41:17'),
(2, 4, 'student-21', '2026-08-31 15:48:59', '2026-08-31 15:48:59', '2026-08-31 15:48:59'),
(3, 4, 'grade-1', '2026-08-31 15:48:59', '2026-08-31 15:48:59', '2026-08-31 15:48:59'),
(4, 4, 'grade-2', '2026-08-31 15:48:59', '2026-08-31 15:48:59', '2026-08-31 15:48:59'),
(5, 4, 'student-19', '2026-08-31 15:48:59', '2026-08-31 15:48:59', '2026-08-31 15:48:59'),
(6, 4, 'student-18', '2026-08-31 15:48:59', '2026-08-31 15:48:59', '2026-08-31 15:48:59'),
(7, 15, 'grade-1', '2026-08-31 15:49:23', '2026-08-31 15:49:23', '2026-08-31 15:49:23'),
(8, 15, 'grade-2', '2026-08-31 15:49:23', '2026-08-31 15:49:23', '2026-08-31 15:49:23'),
(10, 19, 'grade-2', '2026-08-31 22:06:32', '2026-08-31 22:06:32', '2026-08-31 22:06:32');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('admin@ckcgingoog.edu.ph', '$2y$12$6OJN5sAmpWZIXdyGVUeH8.72aybihXVnGoOaxvBf4XrJH0reYPvEC', '2026-08-31 22:25:00'),
('jatis@ckcgingoog.edu.ph', '$2y$12$sxx1DGbKuSho04v4tPTaxe4NNDRbuWWItBVYH8di6IFhbeY8cfGZ2', '2026-08-31 22:40:11');

-- --------------------------------------------------------

--
-- Table structure for table `school_years`
--

CREATE TABLE `school_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `semester` enum('First Semester','Second Semester') NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `school_years`
--

INSERT INTO `school_years` (`id`, `name`, `semester`, `is_current`, `is_active`, `created_at`, `updated_at`) VALUES
(10, '2026-2027', 'First Semester', 0, 1, '2026-08-31 06:03:16', '2026-08-31 06:03:16');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_level` varchar(255) NOT NULL,
  `block` varchar(255) DEFAULT NULL,
  `adviser_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `course`, `year_level`, `block`, `adviser_id`, `school_year_id`, `is_active`, `created_at`, `updated_at`) VALUES
(10, 'BSIT 4', 'BS Information Technology', '4th Year', NULL, NULL, 10, 1, '2026-08-31 06:23:01', '2026-08-31 06:23:01'),
(14, 'BSIT 3', 'BS Information Technology', '3rd Year', NULL, NULL, 10, 1, '2026-08-31 07:51:32', '2026-08-31 07:51:32'),
(16, 'BSIT 4-B', 'BS Information Technology', '4th Year', 'B', NULL, 10, 1, '2026-08-31 08:03:53', '2026-08-31 08:03:53');

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
('0DtAt2fxlYvYO8Qeju2cQwkurwzFC3kFwC0c0UBb', NULL, '127.0.0.1', 'curl/8.19.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiYVpnMk9WRDdRWWJPZk9Bamp3YW02Y0h6Tjc5NFluaG5rR0xZa2lxeCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjY6ImVycm9ycyI7fXM6MzoibmV3IjthOjA6e319czo2OiJlcnJvcnMiO086MzE6IklsbHVtaW5hdGVcU3VwcG9ydFxWaWV3RXJyb3JCYWciOjE6e3M6NzoiACoAYmFncyI7YToxOntzOjc6ImRlZmF1bHQiO086Mjk6IklsbHVtaW5hdGVcU3VwcG9ydFxNZXNzYWdlQmFnIjoyOntzOjExOiIAKgBtZXNzYWdlcyI7YToxOntzOjU6ImVtYWlsIjthOjE6e2k6MDtzOjI4OiJQbGVhc2Ugd2FpdCBiZWZvcmUgcmV0cnlpbmcuIjt9fXM6OToiACoAZm9ybWF0IjtzOjg6IjptZXNzYWdlIjt9fX19', 1788243047),
('0L2YXBM2KZSMrlEt4fhc8qJ1sscf618uzslepTyf', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.135.0 Chrome/148.0.7778.280 Electron/42.8.1 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibEdTeEdIVGc2WjZSTWM4RDZrcU9UZUl1Rmc2T001d2VSVTlWdFRPeSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243146),
('1SEFXiNafs3xvKCgLi0rZL0gMGiYSyGndd6q79Tn', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidWFWem51eFBRODU2STZMek9ZNEdIbG5xRHN3QTNNcnJyNjVlMUJPMSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243860),
('4xXEtiRtyalVyjlqiswxKYYf19tkjmcdYsvJVj8b', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicHQxZDNQZ2ZZQWN5bXAzbm14MDdPZ2dpNHpBN0JWcW5iaUpSTHdsVSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243025),
('79Ioh9XkBOXJAvhS5FM3YOyu1vPsUBvI5z2kPcfO', 4, '127.0.0.1', 'curl/8.19.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoidXQ0bUw4dTFER0NoY1FOcnFEenFRN3k0NG9YSXNZQ0p6aGxYOHphQyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7fQ==', 1788243167),
('8PAbwQZ78rDfIs4bx1bl4mQQVY4NHNQTNA0By4RC', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiMVNtU25nc2JxV1BCUTNycmF3RVYxZ0FYcGY3TEpubFRXcGVKd09raiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTMzOiJodHRwOi8vMTI3LjAuMC4xOjg3NjUvcmVzZXQtcGFzc3dvcmQvZTM5OTZiYTRkNzk1NWYyMzk5YmY4OTU3MDU5NTI2ZjZlZjJmYjBkYzY5YzY1ZmFkZWU1NGZhMGFjYWViNDZmOT9lbWFpbD1hZG1pbiU0MGNrY2dpbmdvb2cuZWR1LnBoIjtzOjU6InJvdXRlIjtzOjE0OiJwYXNzd29yZC5yZXNldCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243093),
('duPSmbUIJJWPGWQ6oJnDG5rGX1GUAck6ocs273de', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiazhQa0xWZ1NLdjlnVlZNY2xPSFpDY2N3MGJCRmZvbmRKT2dUVHN0QSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243901),
('KFX6CI5UFRSn8THtj9PdxDAOXXyGYGy1he8WjDuP', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/151.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiU2RKeGxvYlRtck9BejZiSkl6OFhtOFJJVDVsZ3hwcExRT3kxa0xHRiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9yZXBvcnRzIjtzOjU6InJvdXRlIjtzOjEzOiJhZG1pbi5yZXBvcnRzIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDt9', 1788243397),
('m5c8oNbCUXKl6vhAR7UwzhPLTfy6fFDLUhgdmO7g', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWjhSSmR3NEEzUk9vN0JzNWpEak9hUmdPdmQ2ZnhnT2dHYWhQQWVNWSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243601),
('Mv1kjT67TDbK8rRfwZ1LAn49azm0nm4d0HpF2wXk', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibUxvS1k2VTNRbTFOUHp4M0JUUEl1TGc1N1Q1Y1MzRWRqbFhKNExZaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243669),
('NMwgiKTavo1sW9RrIDgwXEwawZdjqBoOwJFkkylO', 4, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiSVJSRDJnZm42UW1JT0JPQ3hZYUZyRFhHN1RiUTBqSkNsSzJKVW01NSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjQ7fQ==', 1788275523),
('SHY0IAFl2fFhwjUz8Pt61hmOxoBJvZH36Oj1r0PU', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiNFBaMEp6VmxSRkdkMHFxSnNVc2F2bVFnMHp3QXBDN25wS2JkYktFVyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243182),
('snDnMBwr7ntZQkxDAkVc0z3WMQrMGOliCrcOagyY', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib3VBbzRUNk56WjdJWVh2cEQwZHo1c2pCbmdTYTI5Nk1MVVRYNW5jTCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788242980),
('tcTS243Jd7ZyCCLVUtKfFSJ6EYzSvI8xPu3QNqki', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoicUVXTlpqWFpscTh6blVXZDUyVmJZR21iZUhGSlpQOXQ0eHVpeXRpbCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788242980),
('TW5kRqaCCU3GM5rcwt3jhHG72lh6fheqHsK5I1Zv', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiejl5SWZVY2JOTzhPTGtaMktkZE5XbnphN3E3TXJ6dHFLR0tVa2FaRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJuZXciO2E6MDp7fXM6Mzoib2xkIjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fX0=', 1788244812),
('VmgRkaEaP2ukJ2k7Nvqm7dPJj76SfTvLJ3rGaW1A', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiU3hFNGdvMlF5SEhKZDZWZUNObFhhY053WEhrSjc5MDVpYUJOaUE3ayI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly8xMjcuMC4wLjE6ODc2NS9mb3Jnb3QtcGFzc3dvcmQiO3M6NToicm91dGUiO3M6MTU6InBhc3N3b3JkLmZvcmdvdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243565),
('wgd01LaUBuuBdLzM6rTY1jRrZGXdbHITCR0YSRsn', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUjdFQ2lXWEZ5bjdsSDRZaVVEakF2TDVWSjNVeG5YQUZPaWJZUWIzaSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTMzOiJodHRwOi8vMTI3LjAuMC4xOjg3NjUvcmVzZXQtcGFzc3dvcmQvYjlhM2ZhZWVjYmQ0NjcxY2ZjMGU0YjM2ZmY0ZTg1ZjIxZGRkOGI4MTRmMTk3Yjc5YzJjMmViMzQ2MjczYzE2Yz9lbWFpbD1hZG1pbiU0MGNrY2dpbmdvb2cuZWR1LnBoIjtzOjU6InJvdXRlIjtzOjE0OiJwYXNzd29yZC5yZXNldCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243918),
('WQ4GLBWLn8288d9Ye3lHoLGH24bS1PhYz47XImUA', NULL, '127.0.0.1', 'curl/8.19.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoic2k0cGtxSTQzS25rRnh1SXMwRTViejYzc2RuTVlHa1dUWHVjeW8wSSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTMzOiJodHRwOi8vMTI3LjAuMC4xOjg3NjUvcmVzZXQtcGFzc3dvcmQvZTM5OTZiYTRkNzk1NWYyMzk5YmY4OTU3MDU5NTI2ZjZlZjJmYjBkYzY5YzY1ZmFkZWU1NGZhMGFjYWViNDZmOT9lbWFpbD1hZG1pbiU0MGNrY2dpbmdvb2cuZWR1LnBoIjtzOjU6InJvdXRlIjtzOjE0OiJwYXNzd29yZC5yZXNldCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjE6e2k6MDtzOjY6InN0YXR1cyI7fXM6MzoibmV3IjthOjA6e319czo2OiJzdGF0dXMiO3M6Mjk6IllvdXIgcGFzc3dvcmQgaGFzIGJlZW4gcmVzZXQuIjt9', 1788243137),
('zdMYWnu5Uaghh20C9MJtRVSt6MpmtURavYx1fF4I', NULL, '127.0.0.1', 'curl/8.19.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibnMydDZHUG9uWk5jWFBjUU9WWVNOellrWFNUaEVURjZKN2Y4Nmh4MCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MTMzOiJodHRwOi8vMTI3LjAuMC4xOjg3NjUvcmVzZXQtcGFzc3dvcmQvZTM5OTZiYTRkNzk1NWYyMzk5YmY4OTU3MDU5NTI2ZjZlZjJmYjBkYzY5YzY1ZmFkZWU1NGZhMGFjYWViNDZmOT9lbWFpbD1hZG1pbiU0MGNrY2dpbmdvb2cuZWR1LnBoIjtzOjU6InJvdXRlIjtzOjE0OiJwYXNzd29yZC5yZXNldCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1788243072);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(100) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `units` tinyint(3) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `name`, `description`, `units`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'PE 1', 'Physical Education 1', 'Foundational fitness, wellness, and movement competencies for college students.', 2, 1, '2026-08-31 03:05:48', '2026-08-31 04:37:04'),
(2, 'IT 101', 'Introduction to Information Technology', 'Survey of computing fundamentals, hardware, software, and the role of IT in modern organizations.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 04:08:00'),
(3, 'MATH 101', 'Mathematics in the Modern World', 'Quantitative reasoning, patterns, and real-world problem solving.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 03:05:48'),
(4, 'GE 101', 'Communication Skills I', 'Effective written and oral communication in academic and professional contexts.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 03:05:48'),
(5, 'GE 102', 'Communication Skills II', 'Advanced academic writing and research communication.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 03:05:48'),
(6, 'GE 103', 'The Filipino Experience', 'Philippine history, identity, and contemporary social realities.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 03:05:48'),
(7, 'GE 104', 'Philosophy of the Human Person', 'Philosophical inquiry into the human person, freedom, and meaning.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 03:05:48'),
(8, 'GE 105', 'Science, Technology, and Society', 'Interaction of science, technology, and society across history.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 03:05:48'),
(9, 'NSTP 101', 'National Service Training Program', 'Civic consciousness and community engagement for first-year students.', 3, 1, '2026-08-31 03:05:48', '2026-08-31 03:05:48'),
(10, 'IT401', 'Systems Integration', 'descriptions', 3, 1, '2026-08-31 06:37:51', '2026-08-31 06:37:51');

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `school_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `semester` enum('First Semester','Second Semester','Summer') NOT NULL DEFAULT 'First Semester',
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_id`, `subject_id`, `section_id`, `school_year_id`, `semester`, `status`, `created_at`, `updated_at`) VALUES
(9, 15, 10, 10, 10, 'First Semester', 'active', '2026-08-31 06:38:28', '2026-08-31 06:38:28'),
(11, 20, 10, 16, 10, 'First Semester', 'active', '2026-08-31 08:04:26', '2026-08-31 08:04:26');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `student_id` varchar(255) DEFAULT NULL,
  `course` varchar(255) DEFAULT NULL,
  `year_level` varchar(255) DEFAULT NULL,
  `block` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `email`, `phone`, `student_id`, `course`, `year_level`, `block`, `role`, `is_active`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(4, 'Ramon', 'Villareal', 'admin@ckcgingoog.edu.ph', '09123456789', NULL, NULL, NULL, NULL, 'admin', 1, NULL, '$2y$12$038kzwkTw.M2WTEA9GTozu8uit7eYNPf/7SDD1r60ppFGYWdnqHMi', 'WIuR804rcSNcNLtXote0dbqZwFSJ6INIABWZXVCUDX9Pnz3zAxYudlZmvGza', '2026-08-31 03:05:49', '2026-08-31 22:13:11'),
(15, 'Adrian', 'Peligrino', 'ap@ckcgingoog.edu.ph', NULL, NULL, NULL, NULL, NULL, 'teacher', 1, NULL, '$2y$12$Hen6V1LOY6DUnOBhHntPPOCK1YR.xXRiHYsbEiQsyAgsNoBRxrSnK', NULL, '2026-08-31 03:14:06', '2026-08-31 03:14:06'),
(18, 'Joshua', 'Atis', 'jatis@ckcgingoog.edu.ph', '09924895282', 'C23-0033', 'BS Information Technology', '4th Year', NULL, 'student', 1, NULL, '$2y$12$DK.dcQuuKaQFZQWYCEnJWuHugz.UsfwDe4lTeYkD4gwTqbKpr/wTm', 't8LJeKU4XPvuh4ZUK4Khad6xyt5kR3Zdr6Wo88oBkbOixeygsFXjnvj9htSz', '2026-08-31 05:57:37', '2026-08-31 22:26:34'),
(19, 'Jessie Mar', 'Bescayno', 'bescayno@ckcgingoog.edu.ph', NULL, 'C23-0001', 'BS Information Technology', '4th Year', NULL, 'student', 1, NULL, '$2y$12$31Zjg8FGNKS0V4hauX9jAe3s20A629l4OuHoExkU9R1jk7oTe/3pe', NULL, '2026-08-31 06:02:03', '2026-08-31 22:06:27'),
(20, 'Carlito', 'Cabasag', 'cabasag@ckcgingoog.edu.ph', NULL, NULL, NULL, NULL, NULL, 'teacher', 1, NULL, '$2y$12$rIlvEAnz1mbQJowPtB6XMun54k9tS1KDeknoiMvh0IUGwmaaPcbqy', NULL, '2026-08-31 07:12:44', '2026-08-31 07:12:44'),
(21, 'Cris', 'Martin Tirariray', 'cm@ckcgingoog.edu.ph', NULL, 'C23-1233', 'BS Information Technology', '4th Year', 'B', 'student', 1, NULL, '$2y$12$rTKa9ydRmTEIl8FA0wWx9.lrVY3UGJQN3LEdjfCfB.3gU0kuohJGy', NULL, '2026-08-31 14:43:57', '2026-08-31 14:43:57'),
(22, 'Prof. Juan', 'Dela Cruz', 'juan.delacruz@ckcgingoog.edu.ph', NULL, NULL, NULL, NULL, NULL, 'teacher', 1, NULL, '$2y$12$OC6uvvPXIYv.0BRVG/19quu/nwZ5YfGucCAIcGy2xMz2nBxsSCH2m', NULL, '2026-08-31 21:55:50', '2026-08-31 21:55:50'),
(23, 'Prof. Maria', 'Santos', 'maria.santos@ckcgingoog.edu.ph', NULL, NULL, NULL, NULL, NULL, 'teacher', 1, NULL, '$2y$12$1IrBe6aDYFZfcJ40JkaE0.2y6rhLBulxtSyKE1KoaWhzKr8IZU6NS', NULL, '2026-08-31 21:55:50', '2026-08-31 21:55:50'),
(24, 'Prof. Pedro', 'Reyes', 'pedro.reyes@ckcgingoog.edu.ph', NULL, NULL, NULL, NULL, NULL, 'teacher', 1, NULL, '$2y$12$r9a6cTbylHpCNs/QzDDwue0fbOb/OsQg4l9M3MaQNjjkKUxp2RJIi', NULL, '2026-08-31 21:55:50', '2026-08-31 21:55:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollments_student_id_section_id_school_year_id_unique` (`student_id`,`section_id`,`school_year_id`),
  ADD KEY `enrollments_section_id_foreign` (`section_id`),
  ADD KEY `enrollments_school_year_id_foreign` (`school_year_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grades_teacher_assignment_student_unique` (`teacher_assignment_id`,`student_id`),
  ADD KEY `grades_subject_id_foreign` (`subject_id`),
  ADD KEY `grades_teacher_id_foreign` (`teacher_id`),
  ADD KEY `grades_student_id_foreign` (`student_id`);

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
-- Indexes for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_reads_user_id_notification_id_unique` (`user_id`,`notification_id`),
  ADD KEY `notification_reads_user_id_index` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `school_years`
--
ALTER TABLE `school_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sections_adviser_id_foreign` (`adviser_id`),
  ADD KEY `sections_school_year_id_foreign` (`school_year_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_code_unique` (`code`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ts_teacher_subject_section_sy_unique` (`teacher_id`,`subject_id`,`section_id`,`school_year_id`),
  ADD KEY `teacher_subjects_subject_id_foreign` (`subject_id`),
  ADD KEY `teacher_subjects_section_id_foreign` (`section_id`),
  ADD KEY `teacher_subjects_school_year_id_foreign` (`school_year_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_student_id_unique` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `notification_reads`
--
ALTER TABLE `notification_reads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `school_years`
--
ALTER TABLE `school_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_teacher_assignment_id_foreign` FOREIGN KEY (`teacher_assignment_id`) REFERENCES `teacher_subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD CONSTRAINT `notification_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_adviser_id_foreign` FOREIGN KEY (`adviser_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sections_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_school_year_id_foreign` FOREIGN KEY (`school_year_id`) REFERENCES `school_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `teacher_subjects_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
