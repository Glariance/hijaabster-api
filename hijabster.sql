/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE TABLE `blog_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `blog_id` bigint(20) unsigned NOT NULL,
  `tag_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `blog_tag_blog_id_foreign` (`blog_id`),
  KEY `blog_tag_tag_id_foreign` (`tag_id`),
  CONSTRAINT `blog_tag_blog_id_foreign` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `blog_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `blogs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `meta_keyword` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('draft','published','archived') NOT NULL DEFAULT 'draft',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `blogs_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brands_name_unique` (`name`),
  UNIQUE KEY `brands_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_name_unique` (`name`),
  UNIQUE KEY `categories_slug_unique` (`slug`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cms_page_section_fields` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cms_page_section_id` bigint(20) unsigned NOT NULL,
  `field_group` varchar(255) DEFAULT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` enum('text','textarea','image','number','boolean','select') NOT NULL,
  `field_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_page_section_fields_cms_page_section_id_foreign` (`cms_page_section_id`),
  CONSTRAINT `cms_page_section_fields_cms_page_section_id_foreign` FOREIGN KEY (`cms_page_section_id`) REFERENCES `cms_page_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=348 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cms_page_sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `cms_page_id` bigint(20) unsigned NOT NULL,
  `section_name` varchar(255) NOT NULL,
  `section_type` enum('single','repeater') NOT NULL DEFAULT 'single',
  `section_sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cms_page_sections_cms_page_id_foreign` (`cms_page_id`),
  CONSTRAINT `cms_page_sections_cms_page_id_foreign` FOREIGN KEY (`cms_page_id`) REFERENCES `cms_pages` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cms_pages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `page_title` varchar(255) NOT NULL,
  `page_slug` varchar(255) NOT NULL,
  `page_meta_title` varchar(255) DEFAULT NULL,
  `page_meta_keyword` text DEFAULT NULL,
  `page_meta_description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cms_pages_page_slug_unique` (`page_slug`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `comments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `comment` text NOT NULL,
  `commentable_type` varchar(255) NOT NULL,
  `commentable_id` bigint(20) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`),
  KEY `comments_parent_id_foreign` (`parent_id`),
  KEY `comments_user_id_foreign` (`user_id`),
  CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `contact_inquiries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `service` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` longtext NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `coupons` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percentage','fixed') NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `minimum_purchase` decimal(10,2) DEFAULT NULL,
  `maximum_discount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `valid_from` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `coupons_code_unique` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `general_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `general_settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `media_type` varchar(255) NOT NULL,
  `mediaable_type` varchar(255) NOT NULL,
  `mediaable_id` bigint(20) unsigned NOT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `media_mediaable_type_mediaable_id_index` (`mediaable_type`,`mediaable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `newsletters` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `is_subscribed` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `newsletters_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_attribute_options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attribute_id` bigint(20) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_attribute_options_attribute_id_foreign` (`attribute_id`),
  CONSTRAINT `product_attribute_options_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `product_attributes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_attributes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `product_variations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `option_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`option_ids`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `attribute_option_index` varchar(191) GENERATED ALWAYS AS (json_unquote(json_extract(`option_ids`,'$[0]'))) STORED,
  PRIMARY KEY (`id`),
  KEY `product_variations_product_id_foreign` (`product_id`),
  CONSTRAINT `product_variations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `base_price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `has_variations` tinyint(1) NOT NULL DEFAULT 0,
  `category_id` bigint(20) unsigned NOT NULL,
  `brand_id` bigint(20) unsigned DEFAULT NULL,
  `coupon_id` bigint(20) unsigned DEFAULT NULL,
  `has_discount` tinyint(1) NOT NULL DEFAULT 0,
  `discount_type` enum('fixed','percentage') DEFAULT NULL,
  `discount_value` decimal(10,2) DEFAULT NULL,
  `created_by` bigint(20) unsigned NOT NULL,
  `featured` tinyint(1) NOT NULL DEFAULT 0,
  `new` tinyint(1) NOT NULL DEFAULT 0,
  `top` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_created_by_foreign` (`created_by`),
  KEY `products_brand_id_foreign` (`brand_id`),
  KEY `products_coupon_id_foreign` (`coupon_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `products_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `products_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `smtp_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mail_driver` varchar(255) DEFAULT NULL,
  `mail_host` varchar(255) DEFAULT NULL,
  `mail_port` varchar(255) DEFAULT NULL,
  `mail_username` varchar(255) DEFAULT NULL,
  `mail_password` varchar(255) DEFAULT NULL,
  `mail_encryption` varchar(255) DEFAULT NULL,
  `mail_from_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tags` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;





INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'A', 'a', '<p>This is testing Brand A.</p>', 1, '2026-01-08 16:39:46', '2026-01-08 16:50:17');
INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, 'B', 'b', '<p>This is testing Brand B.</p>', 1, '2026-01-08 16:42:03', '2026-01-08 16:42:49');






INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Silk Statements', 'silk-statements', '<h3>Weightless drape and luminous finishes for elevated occasions.</h3>', 1, '2026-01-08 16:52:58', '2026-01-08 17:39:29');
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, NULL, 'Cotton Essentials', 'cotton-essentials', '<p>Soft, breathable weaves designed for effortless all-day wear.</p>', 1, '2026-01-08 17:40:26', '2026-01-08 17:40:26');
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(3, NULL, 'Winter Warmth', 'winter-warmth', '<p>Plush wool and cashmere blends crafted to cocoon you in comfort.</p>', 1, '2026-01-08 17:41:30', '2026-01-08 17:41:30');
INSERT INTO `categories` (`id`, `parent_id`, `name`, `slug`, `description`, `status`, `created_at`, `updated_at`) VALUES
(4, NULL, 'Artisan Prints', 'artisan-prints', '<p>Limited-edition motifs inspired by global artistry and heritage.</p>', 1, '2026-01-08 17:52:23', '2026-01-08 17:52:23'),
(5, NULL, 'Pastel Palette', 'pastel-palette', '<p>Dreamy hues that add a whisper of color to every ensemble.</p>', 1, '2026-01-08 17:52:58', '2026-01-08 17:52:58'),
(6, NULL, 'Earthy Textures', 'earthy-textures', '<p>Hand-loomed finishes that celebrate natural fibers and craft.</p>', 1, '2026-01-08 17:53:27', '2026-01-08 17:53:27');

INSERT INTO `cms_page_section_fields` (`id`, `cms_page_section_id`, `field_group`, `field_name`, `field_type`, `field_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'Group_1', 'Title', 'text', 'New Collection: Silk Dreams', '2025-12-23 18:42:08', '2025-12-24 01:22:35');
INSERT INTO `cms_page_section_fields` (`id`, `cms_page_section_id`, `field_group`, `field_name`, `field_type`, `field_value`, `created_at`, `updated_at`) VALUES
(2, 1, 'Group_1', 'Description', 'textarea', '<p>Discover our luxurious silk scarves, perfect for any occasion.</p>', '2025-12-23 18:42:08', '2025-12-24 01:22:35');
INSERT INTO `cms_page_section_fields` (`id`, `cms_page_section_id`, `field_group`, `field_name`, `field_type`, `field_value`, `created_at`, `updated_at`) VALUES
(3, 1, 'Group_1', 'Button Text', 'text', 'Shop Now', '2025-12-23 18:42:08', '2025-12-24 01:22:35');
INSERT INTO `cms_page_section_fields` (`id`, `cms_page_section_id`, `field_group`, `field_name`, `field_type`, `field_value`, `created_at`, `updated_at`) VALUES
(5, 1, 'Group_1', 'Banner Image', 'image', 'cms_fields/1767293635_6956c2c3b3ebe.jpeg', '2025-12-23 19:08:43', '2026-01-01 18:54:02'),
(6, 1, 'Group_2', 'Title', 'text', 'Pastel Perfection', '2026-01-01 21:30:56', '2026-01-01 21:32:20'),
(7, 1, 'Group_2', 'Description', 'textarea', '<p>Embrace soft hues and elegant designs with our pastel scarf range.</p>', '2026-01-01 21:30:56', '2026-01-01 21:44:13'),
(8, 1, 'Group_2', 'Button Text', 'text', 'Shop Now', '2026-01-01 21:30:57', '2026-01-01 21:32:20'),
(9, 1, 'Group_2', 'Banner Image', 'image', 'cms_fields/1767303137_6956e7e13809c.jpeg', '2026-01-01 21:30:57', '2026-01-01 21:32:20'),
(10, 1, 'Group_3', 'Title', 'text', 'Winter Warmth', '2026-01-01 21:30:58', '2026-01-01 21:35:56'),
(11, 1, 'Group_3', 'Description', 'textarea', '<p>Stay cozy and stylish with our new collection of warm scarves.</p>', '2026-01-01 21:30:58', '2026-01-01 21:44:48'),
(12, 1, 'Group_3', 'Button Text', 'text', 'Shop Now', '2026-01-01 21:30:58', '2026-01-01 21:35:56'),
(13, 1, 'Group_3', 'Banner Image', 'image', 'cms_fields/1767303351_6956e8b743101.jpeg', '2026-01-01 21:30:58', '2026-01-01 21:35:56'),
(14, 1, 'Group_4', 'Title', 'text', 'Discover Your Perfect Scarf', '2026-01-01 21:31:00', '2026-01-01 21:47:05'),
(15, 1, 'Group_4', 'Description', 'textarea', '<p>Explore our exquisite collection and find your unique style.</p>', '2026-01-01 21:31:00', '2026-01-01 21:47:05'),
(16, 1, 'Group_4', 'Button Text', 'text', 'Shop Now', '2026-01-01 21:31:00', '2026-01-01 21:47:05'),
(17, 1, 'Group_4', 'Banner Image', 'image', 'cms_fields/1767304019_6956eb5355ca7.jpeg', '2026-01-01 21:31:00', '2026-01-01 21:47:05'),
(18, 1, 'Group_5', 'Title', 'text', 'Elegant Styles for Every Season', '2026-01-01 21:31:01', '2026-01-01 21:48:03'),
(19, 1, 'Group_5', 'Description', 'textarea', '<p>Explore our diverse range of scarves, perfect for any look.</p>', '2026-01-01 21:31:01', '2026-01-01 21:48:03'),
(20, 1, 'Group_5', 'Button Text', 'text', 'Shop Now', '2026-01-01 21:31:01', '2026-01-01 21:48:03'),
(21, 1, 'Group_5', 'Banner Image', 'image', 'cms_fields/1767304056_6956eb78102bf.jpeg', '2026-01-01 21:31:01', '2026-01-01 21:48:03'),
(22, 1, 'Group_6', 'Title', 'text', 'Handcrafted with Love', '2026-01-01 21:31:08', '2026-01-01 21:48:03'),
(23, 1, 'Group_6', 'Description', 'textarea', '<p>Experience the unique touch of our artisan-made scarf collection.</p>', '2026-01-01 21:31:08', '2026-01-01 21:48:03'),
(24, 1, 'Group_6', 'Button Text', 'text', 'Shop Now', '2026-01-01 21:31:08', '2026-01-01 21:48:03'),
(25, 1, 'Group_6', 'Banner Image', 'image', 'cms_fields/1767304081_6956eb91382e0.jpeg', '2026-01-01 21:31:08', '2026-01-01 21:48:03'),
(26, 2, NULL, 'Title', 'text', 'Why Shop With Us', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(27, 2, NULL, 'Heading', 'text', 'An elevated hijab experience tailored for modern women.', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(28, 2, NULL, 'Description', 'textarea', '<p>We obsess over the details so you can focus on feeling confident, polished, and ready for every moment.</p>', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(29, 2, NULL, 'Box1 - Title', 'text', 'Free Worldwide Shipping', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(30, 2, NULL, 'Box1 - Description', 'textarea', '<p>Complimentary delivery on every order with express options at checkout.</p>', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(31, 2, NULL, 'Box2 - Title', 'text', 'Premium Craftsmanship', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(32, 2, NULL, 'Box2 - Description', 'textarea', '<p>Ethically sourced fabrics finished by skilled artisans for a luxe drape.</p>', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(33, 2, NULL, 'Box3 - Title', 'text', 'Style Concierge', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(34, 2, NULL, 'Box3 - Description', 'textarea', '<p>Personalised styling support to help you pair colours, fabrics, and looks.</p>', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(35, 2, NULL, 'Box4 - Title', 'text', 'Secure & Hassle-Free', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(36, 2, NULL, 'Box4 - Description', 'textarea', '<p>30-day returns and protected checkout keep every purchase worry free.</p>', '2026-01-01 21:54:52', '2026-01-01 21:56:18'),
(37, 3, NULL, 'Title', 'text', 'Featured Products', '2026-01-01 22:19:49', '2026-01-01 22:20:04'),
(38, 3, NULL, 'Description', 'textarea', '<p>Explore our curated selection of best-selling scarves and seasonal picks. Each piece is hand-selected for quality, comfort, and timeless style&mdash;perfect for everyday wear and special occasions.</p>', '2026-01-01 22:19:49', '2026-01-01 22:20:04'),
(39, 4, NULL, 'Title', 'text', 'Shop by Category', '2026-01-01 22:22:38', '2026-01-01 22:32:04'),
(43, 4, NULL, 'Description', 'textarea', '<p>Explore curated collections that make it easy to find the perfect scarf for every season and occasion.</p>', '2026-01-01 22:23:57', '2026-01-01 22:32:04'),
(44, 5, NULL, 'Title', 'text', 'Seasonal Promotion', '2026-01-01 22:38:12', '2026-01-01 22:41:35'),
(46, 5, NULL, 'Heading', 'text', 'Limited Time Offer!', '2026-01-01 22:39:28', '2026-01-01 22:41:35'),
(47, 5, NULL, 'Description', 'textarea', '<p>Get&nbsp;20%&nbsp;off all new arrivals. Discover luxurious textures crafted for effortless elegance.</p>', '2026-01-01 22:39:28', '2026-01-01 22:41:35'),
(48, 5, NULL, 'Image', 'image', 'cms_fields/1767308286_6956fbfe13bb9.jpeg', '2026-01-01 22:44:31', '2026-01-01 22:58:06'),
(49, 6, 'Group_1', 'Name', 'text', 'Sarah L.', '2026-01-01 23:13:46', '2026-01-01 23:15:08'),
(50, 6, 'Group_1', 'Designation', 'text', 'Fashion Enthusiast', '2026-01-01 23:13:46', '2026-01-01 23:15:08'),
(51, 6, 'Group_1', 'Description', 'textarea', '<p>Absolutely love the scarves from Scarf e-commerce app! The quality is superb, and the designs are so elegant and unique. I always get compliments when I wear them.</p>', '2026-01-01 23:13:46', '2026-01-01 23:15:08'),
(52, 6, 'Group_1', 'Image', 'image', 'cms_fields/1767309304_6956fff8caf57.png', '2026-01-01 23:13:46', '2026-01-01 23:15:08'),
(53, 6, 'Group_2', 'Name', 'text', 'Jessica R.', '2026-01-01 23:15:47', '2026-01-01 23:17:20'),
(54, 6, 'Group_2', 'Designation', 'text', 'Style Blogger', '2026-01-01 23:15:47', '2026-01-01 23:17:20'),
(55, 6, 'Group_2', 'Description', 'textarea', '<p>These scarves are a game-changer for my wardrobe. They add a touch of sophistication to any outfit. The fabric is incredibly soft and luxurious.</p>', '2026-01-01 23:15:47', '2026-01-01 23:17:20'),
(56, 6, 'Group_2', 'Image', 'image', 'cms_fields/1767309434_6957007a06bd8.jpeg', '2026-01-01 23:15:47', '2026-01-01 23:17:20'),
(57, 6, 'Group_3', 'Name', 'text', 'Emily C.', '2026-01-01 23:15:48', '2026-01-01 23:19:38'),
(58, 6, 'Group_3', 'Designation', 'text', 'Happy Customer', '2026-01-01 23:15:48', '2026-01-01 23:19:38'),
(59, 6, 'Group_3', 'Description', 'textarea', '<p>I&#39;m so impressed with the variety and beauty of these scarves. The colors are vibrant, and they&#39;re perfect for any occasion. Highly recommend!</p>', '2026-01-01 23:15:48', '2026-01-01 23:24:06'),
(60, 6, 'Group_3', 'Image', 'image', 'cms_fields/1767309575_6957010740218.png', '2026-01-01 23:15:48', '2026-01-01 23:19:38'),
(61, 6, 'Group_4', 'Name', 'text', 'Maya H.', '2026-01-01 23:15:49', '2026-01-01 23:20:14'),
(62, 6, 'Group_4', 'Designation', 'text', 'Creative Director', '2026-01-01 23:15:49', '2026-01-01 23:20:14'),
(63, 6, 'Group_4', 'Description', 'textarea', '<p>The craftsmanship is unmatched. Each scarf feels like a wearable piece of art and elevates our photo shoots instantly.</p>', '2026-01-01 23:15:49', '2026-01-01 23:20:14'),
(64, 6, 'Group_4', 'Image', 'image', 'cms_fields/1767309612_6957012c3dc8f.jpeg', '2026-01-01 23:15:49', '2026-01-01 23:20:14'),
(65, 6, 'Group_5', 'Name', 'text', 'Priya S.', '2026-01-01 23:15:51', '2026-01-01 23:20:41'),
(66, 6, 'Group_5', 'Designation', 'text', 'Boutique Owner', '2026-01-01 23:15:51', '2026-01-01 23:20:41'),
(67, 6, 'Group_5', 'Description', 'textarea', '<p>Our customers adore these scarves. They fly off the shelves thanks to the luxurious feel and timeless patterns.</p>', '2026-01-01 23:15:51', '2026-01-01 23:20:41'),
(68, 6, 'Group_5', 'Image', 'image', 'cms_fields/1767309639_69570147dd6dc.jpeg', '2026-01-01 23:15:51', '2026-01-01 23:20:41'),
(69, 6, 'Group_6', 'Name', 'text', 'Nadia K.', '2026-01-01 23:15:52', '2026-01-01 23:23:42'),
(70, 6, 'Group_6', 'Designation', 'text', 'Frequent Traveler', '2026-01-01 23:15:52', '2026-01-01 23:23:42'),
(71, 6, 'Group_6', 'Description', 'textarea', '<p>Lightweight, cozy, and stylish - these scarves are my travel must-have. They fold easily and dress up any airport outfit.</p>', '2026-01-01 23:15:52', '2026-01-01 23:23:42'),
(72, 6, 'Group_6', 'Image', 'image', 'cms_fields/1767309820_695701fc6332b.jpeg', '2026-01-01 23:15:52', '2026-01-01 23:23:42'),
(73, 7, NULL, 'Title', 'text', 'Our Story', '2026-01-01 23:27:38', '2026-01-01 23:28:03'),
(74, 7, NULL, 'Description', 'textarea', '<p>At Scarf e-commerce app, we believe in the power of a beautiful scarf to transform an outfit and express individuality. Our journey began with a passion for exquisite fabrics and unique designs, aiming to bring elegance and style to every woman&#39;s wardrobe.</p>\r\n\r\n<p>We meticulously curate our collections, focusing on quality, comfort, and timeless appeal. Each scarf is a testament to our commitment to craftsmanship and our dedication to helping you find the perfect accessory for every occasion.</p>', '2026-01-01 23:27:38', '2026-01-01 23:28:03'),
(75, 7, NULL, 'Image', 'image', 'cms_fields/1767310082_69570302cdfc3.jpeg', '2026-01-01 23:27:38', '2026-01-01 23:28:03'),
(76, 8, NULL, 'Title', 'text', 'Our Mission', '2026-01-01 23:30:05', '2026-01-01 23:30:24'),
(77, 8, NULL, 'Description', 'textarea', '<p>Our mission is to empower women to express their unique style and confidence through our exquisite collection of scarves. We are dedicated to providing high-quality, ethically sourced, and beautifully designed scarves that inspire elegance and individuality.</p>\r\n\r\n<p>We strive to create a positive impact by supporting sustainable practices and fostering a community where fashion meets purpose.</p>', '2026-01-01 23:30:05', '2026-01-01 23:30:24'),
(78, 8, NULL, 'Image', 'image', 'cms_fields/1767310224_6957039015377.jpeg', '2026-01-01 23:30:05', '2026-01-01 23:30:24'),
(79, 9, NULL, 'Title', 'text', 'Our Vision', '2026-01-01 23:33:09', '2026-01-01 23:33:37'),
(80, 9, NULL, 'Description', 'textarea', '<p>We envision a world where every woman feels empowered and beautiful, with a scarf that reflects her personality and enhances her natural grace. We strive to be the leading destination for premium girls&#39; scarves, known for our exceptional designs, quality, and customer experience.</p>\r\n\r\n<p>Our vision extends to building a global community that celebrates diversity, creativity, and the art of scarf styling.</p>', '2026-01-01 23:33:09', '2026-01-01 23:33:37'),
(81, 9, NULL, 'Image', 'image', 'cms_fields/1767310416_69570450d4ad9.jpeg', '2026-01-01 23:33:09', '2026-01-01 23:33:37'),
(82, 10, NULL, 'Title', 'text', 'Wrapped in meaning', '2026-01-02 01:05:40', '2026-01-02 01:08:34'),
(83, 10, NULL, 'Heading', 'text', 'About Us', '2026-01-02 01:05:40', '2026-01-02 01:08:34'),
(84, 10, NULL, 'Description', 'textarea', '<p>Every scarf we design starts with a story: of the women who will wear it, the artisans who will craft it, and the moments it will elevate. We blend timeless craft with modern design so your daily rituals feel a little more considered, a little more you.</p>', '2026-01-02 01:05:40', '2026-01-02 01:08:34'),
(85, 10, NULL, 'Image', 'image', 'cms_fields/1767316114_69571a925473b.jpeg', '2026-01-02 01:05:40', '2026-01-02 01:08:34'),
(86, 10, NULL, 'Button 1', 'text', 'Explore our story', '2026-01-02 01:05:40', '2026-01-02 01:08:34'),
(87, 10, NULL, 'Button 2', 'text', 'Meet the team', '2026-01-02 01:05:40', '2026-01-02 01:08:34'),
(88, 11, NULL, 'Title', 'text', 'Crafted with intention', '2026-01-02 17:36:31', '2026-01-02 17:36:46'),
(89, 11, NULL, 'Heading', 'text', 'The heart behind every layer', '2026-01-02 17:36:31', '2026-01-02 17:36:46'),
(90, 11, NULL, 'Description', 'textarea', '<p>From loom to wardrobe, our collective of artisans and stylists collaborate so your scarves feel as meaningful as they look. Explore the pillars that guide our work.</p>', '2026-01-02 17:36:31', '2026-01-02 17:36:46'),
(91, 13, 'Group_1', 'Title', 'text', 'Our Story', '2026-01-02 17:46:18', '2026-01-02 17:46:46'),
(92, 13, 'Group_1', 'Heading', 'text', 'Hand-loomed beginnings, modern silhouettes.', '2026-01-02 17:46:18', '2026-01-02 17:46:46'),
(93, 13, 'Group_1', 'Description', 'textarea', '<p>Scarf began at a single artisan table where we explored how color, texture, and heritage patterns could meet contemporary wardrobes.</p>\r\n\r\n<p>Today, we collaborate with designers and craftspeople across the globe, blending tradition with innovation so every scarf feels personal and purposeful.</p>', '2026-01-02 17:46:18', '2026-01-02 17:46:46'),
(94, 13, 'Group_1', 'Image', 'image', 'cms_fields/1767376005_6958048526927.jpeg', '2026-01-02 17:46:18', '2026-01-02 17:46:46'),
(95, 13, 'Group_2', 'Title', 'text', 'Our Mission', '2026-01-02 17:46:52', '2026-01-02 17:49:41'),
(96, 13, 'Group_2', 'Heading', 'text', 'Fashion that uplifts, sourced with care.', '2026-01-02 17:46:52', '2026-01-02 17:49:41'),
(97, 13, 'Group_2', 'Description', 'textarea', '<p>We empower women to express themselves with pieces that feel luxurious, last for years, and honor the people who craft them.</p>\r\n\r\n<p>That means mindful sourcing, small-batch production, and transparent partnerships that keep creativity and ethics equally in focus.</p>', '2026-01-02 17:46:52', '2026-01-02 17:49:41'),
(98, 13, 'Group_2', 'Image', 'image', 'cms_fields/1767376164_695805246e11a.jpeg', '2026-01-02 17:46:52', '2026-01-02 17:49:41'),
(99, 13, 'Group_3', 'Title', 'text', 'Our Vision', '2026-01-02 17:46:53', '2026-01-02 17:49:41'),
(100, 13, 'Group_3', 'Heading', 'text', 'Building a global community of scarf lovers.', '2026-01-02 17:46:53', '2026-01-02 17:49:41'),
(101, 13, 'Group_3', 'Description', 'textarea', '<p>We imagine a future where every wrap tells a story and celebrates the cultures that inspire it.</p>\r\n\r\n<p>From digital styling sessions to limited-edition artist collaborations, we are creating spaces where confidence and craft meet.</p>', '2026-01-02 17:46:53', '2026-01-02 17:49:41'),
(102, 13, 'Group_3', 'Image', 'image', 'cms_fields/1767376179_69580533e010a.jpeg', '2026-01-02 17:46:53', '2026-01-02 17:49:41'),
(103, 14, NULL, 'Title', 'text', 'How a scarf comes to life', '2026-01-02 17:51:52', '2026-01-02 17:59:06'),
(104, 14, NULL, 'Heading', 'text', 'From inspiration boards to your wardrobe', '2026-01-02 17:51:52', '2026-01-02 17:59:06'),
(105, 14, NULL, 'Description', 'textarea', '<p>Our atelier process nurtures every layer of craftsmanship. Each phase keeps artistry, responsible sourcing, and wearer experience in equilibrium so your scarf feels intentional from the first drape.</p>', '2026-01-02 17:51:52', '2026-01-02 17:59:06'),
(106, 15, 'Group_1', 'Title', 'text', 'Concept Studio', '2026-01-02 18:05:01', '2026-01-02 18:05:21'),
(107, 15, 'Group_1', 'Heading', 'text', 'Colorwork & storyboarding sessions', '2026-01-02 18:05:01', '2026-01-02 18:05:21'),
(108, 15, 'Group_1', 'Description', 'textarea', '<p>We begin every collection sketching palettes pulled from travel journals, archival textiles, and community mood boards. Each motif is reviewed alongside wear-testing feedback to ensure modern versatility.</p>', '2026-01-02 18:05:01', '2026-01-02 18:05:21'),
(109, 15, 'Group_2', 'Title', 'text', 'Artisan Collaboration', '2026-01-02 18:05:52', '2026-01-02 18:06:54'),
(110, 15, 'Group_2', 'Heading', 'text', '48 partner ateliers across Morocco, Turkey, and India', '2026-01-02 18:05:52', '2026-01-02 18:06:54'),
(111, 15, 'Group_2', 'Description', 'textarea', '<p>Our artisan partners translate concepts into woven samples. We iterate on drape, hand-feel, and finishing details until the piece represents both the maker&#39;s signature and our brand standards.</p>', '2026-01-02 18:05:52', '2026-01-02 18:06:54'),
(112, 15, 'Group_3', 'Title', 'text', 'Responsible Production', '2026-01-02 18:05:53', '2026-01-02 18:06:54'),
(113, 15, 'Group_3', 'Heading', 'text', 'Small batches, traceable materials', '2026-01-02 18:05:53', '2026-01-02 18:06:54'),
(114, 15, 'Group_3', 'Description', 'textarea', '<p>We produce in limited runs using certified silks, organic cottons, and recycled blends. Batch sizes stay small to reduce waste while allowing for bespoke dye baths and hand-rolled edges.</p>', '2026-01-02 18:05:53', '2026-01-02 18:06:54'),
(115, 15, 'Group_4', 'Title', 'text', 'Stylist Rituals', '2026-01-02 18:05:54', '2026-01-02 18:06:54'),
(116, 15, 'Group_4', 'Heading', 'text', 'Guides, fittings, and concierge care', '2026-01-02 18:05:54', '2026-01-02 18:06:54'),
(117, 15, 'Group_4', 'Description', 'textarea', '<p>Once a scarf arrives, our stylist team develops wear guides, pairing suggestions, and care notes so you feel supported from unboxing to everyday styling.</p>', '2026-01-02 18:05:54', '2026-01-02 18:06:54'),
(118, 16, NULL, 'Title', 'text', 'Milestones we cherish', '2026-01-02 18:16:02', '2026-01-02 18:16:17'),
(119, 16, NULL, 'Heading', 'text', 'A timeline of shared growth', '2026-01-02 18:16:02', '2026-01-02 18:16:17'),
(120, 16, NULL, 'Description', 'textarea', '<p>Our journey is a collective story written alongside makers, stylists, and the community of wearers who bring each scarf to life.</p>', '2026-01-02 18:16:02', '2026-01-02 18:16:17'),
(121, 17, 'Group_1', 'Year', 'text', '2016', '2026-01-02 18:20:05', '2026-01-02 18:20:20'),
(122, 17, 'Group_1', 'Title', 'text', 'First Capsule Launch', '2026-01-02 18:20:05', '2026-01-02 18:20:20'),
(123, 17, 'Group_1', 'Description', 'textarea', '<p>Released a six-piece silk collection woven with our founding atelier in Marrakech.</p>', '2026-01-02 18:20:05', '2026-01-02 18:20:20'),
(124, 17, 'Group_2', 'Year', 'text', '2018', '2026-01-02 18:23:06', '2026-01-02 18:23:48'),
(125, 17, 'Group_2', 'Title', 'text', 'Global Collective', '2026-01-02 18:23:06', '2026-01-02 18:23:48'),
(126, 17, 'Group_2', 'Description', 'textarea', '<p>Partnered with women-led studios in Istanbul and Jaipur to introduce artisan block prints.</p>', '2026-01-02 18:23:06', '2026-01-02 18:23:48'),
(127, 17, 'Group_3', 'Year', 'text', '2021', '2026-01-02 18:23:07', '2026-01-02 18:23:48'),
(128, 17, 'Group_3', 'Title', 'text', 'Digital Styling Studio', '2026-01-02 18:23:07', '2026-01-02 18:23:48'),
(129, 17, 'Group_3', 'Description', 'textarea', '<p>Opened virtual fittings with certified colorists, expanding personal styling to every region.</p>', '2026-01-02 18:23:07', '2026-01-02 18:23:48'),
(130, 17, 'Group_4', 'Year', 'text', '2024', '2026-01-02 18:23:08', '2026-01-02 18:23:48'),
(131, 17, 'Group_4', 'Title', 'text', 'Circular Care Program', '2026-01-02 18:23:08', '2026-01-02 18:23:48'),
(132, 17, 'Group_4', 'Description', 'textarea', '<p>Launched repair and refresh services so beloved scarves stay in rotation for years.</p>', '2026-01-02 18:23:08', '2026-01-02 18:23:48'),
(133, 18, NULL, 'Title', 'text', 'Commitments beyond the loom', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(134, 18, NULL, 'Heading', 'text', 'Promises we stand behind', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(135, 18, NULL, 'Description', 'textarea', '<p>Numbers only matter when they reflect real care. These metrics guide every decision, ensuring the scarves you choose honor people, planet, and personal expression.</p>', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(136, 18, NULL, 'Box1 No', 'text', '82%', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(137, 18, NULL, 'Box1 Title', 'text', 'natural fibers', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(138, 18, NULL, 'Box1 Description', 'textarea', '<p>Across our annual collections are woven in silk, cotton, and wool certified for ethical sourcing.</p>', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(139, 18, NULL, 'Box2 No', 'text', '48', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(140, 18, NULL, 'Box2 Title', 'text', 'artisan partners', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(141, 18, NULL, 'Box2 Description', 'textarea', '<p>Independent ateliers receive fair pay, mentorship, and shared design credit on every release.</p>', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(142, 18, NULL, 'Box3 No', 'text', '12k+', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(143, 18, NULL, 'Box3 Title', 'text', 'styling sessions', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(144, 18, NULL, 'Box3 Description', 'textarea', '<p>One-on-one appointments completed globally, guiding clients through color, drape, and care.</p>', '2026-01-02 18:41:19', '2026-01-02 18:50:33'),
(145, 19, NULL, 'Title', 'text', 'Current Offers & Exclusives', '2026-01-02 19:25:32', '2026-01-02 19:27:00'),
(146, 19, NULL, 'Heading', 'text', 'Promotions Crafted Around You', '2026-01-02 19:25:32', '2026-01-02 19:27:00'),
(147, 19, NULL, 'Description', 'textarea', '<p>Discover seasonal savings, loyalty moments, and limited drops designed to elevate every wrap in your wardrobe.</p>', '2026-01-02 19:25:32', '2026-01-02 19:27:00'),
(148, 19, NULL, 'Image', 'image', 'cms_fields/1767382020_69581c040357f.jpeg', '2026-01-02 19:25:32', '2026-01-02 19:27:00'),
(149, 19, NULL, 'Button 1', 'text', 'Shop the edit', '2026-01-02 19:25:32', '2026-01-02 19:27:00'),
(150, 19, NULL, 'Button 2', 'text', 'Join loyalty', '2026-01-02 19:25:32', '2026-01-02 19:27:00'),
(151, 20, NULL, 'Title', 'text', 'Bundle Savings & Gift Sets', '2026-01-02 23:55:47', '2026-01-02 23:56:03'),
(152, 20, NULL, 'Description', 'textarea', '<p>Mix and match textures, tones, and lengths each bundle includes exclusive savings and curated extras.</p>', '2026-01-02 23:55:47', '2026-01-02 23:56:03'),
(153, 21, NULL, 'Title', 'text', 'Featured Promotions', '2026-01-05 16:47:42', '2026-01-05 16:47:51'),
(154, 21, NULL, 'Description', 'textarea', '<p>Each offer is curated to pair with the season&#39;s textures and your styling rituals. Quantities are limited, so reserve early.</p>', '2026-01-05 16:47:42', '2026-01-05 16:47:51'),
(155, 22, NULL, 'Title', 'text', 'On the Horizon', '2026-01-05 16:51:46', '2026-01-05 16:52:37'),
(156, 22, NULL, 'Heading', 'text', 'Upcoming Drops & Experiences', '2026-01-05 16:51:46', '2026-01-05 16:52:37'),
(157, 22, NULL, 'Description', 'textarea', '<p>Secure insider access to the collaborations and styling services arriving next. Loyalty members receive first invitations.</p>', '2026-01-05 16:51:46', '2026-01-05 16:52:37'),
(158, 22, NULL, 'Button Text', 'text', 'Reserve your spot', '2026-01-05 16:51:46', '2026-01-05 16:52:37'),
(159, 22, NULL, 'Image', 'image', 'cms_fields/1767632036_695beca478183.jpeg', '2026-01-05 16:51:46', '2026-01-05 16:53:56'),
(160, 23, NULL, 'Box 1 Title', 'text', 'Releasing July 12', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(161, 23, NULL, 'Box 1 Heading', 'text', 'Artist Edition: Woven Horizons', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(162, 23, NULL, 'Box 1 Description', 'textarea', '<p>A limited, hand-numbered series co-created with Moroccan loom collective Atelier Amal. Members receive 24-hour early access.</p>', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(163, 23, NULL, 'Box 2 Title', 'text', 'Opening July 22', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(164, 23, NULL, 'Box 2 Heading', 'text', 'Colorist Capsule Consults', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(165, 23, NULL, 'Box 2 Description', 'textarea', '<p>Book a complimentary virtual appointment with our in-house stylists and unlock bespoke color pairings with bonus swatch kits.</p>', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(166, 23, NULL, 'Box 3 Title', 'text', 'Streaming August 3', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(167, 23, NULL, 'Box 3 Heading', 'text', 'Autumn Preview Trunk Show', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(168, 23, NULL, 'Box 3 Description', 'textarea', '<p>Preview the fall palette live, reserve statement pieces before they ship, and collect loyalty double-points during the event.</p>', '2026-01-05 16:56:28', '2026-01-05 16:57:19'),
(169, 24, NULL, 'Title', 'text', 'Redeeming Your Promotion', '2026-01-05 16:58:48', '2026-01-05 16:59:07'),
(170, 24, NULL, 'Description', 'textarea', '<p>Every offer is designed to feel intuitive. Follow these quick steps to secure your incentive and enjoy concierge-level support along the way.</p>', '2026-01-05 16:58:48', '2026-01-05 16:59:07'),
(171, 25, 'Group_1', 'Heading', 'text', 'Select your pieces', '2026-01-05 17:21:12', '2026-01-05 17:22:32');
INSERT INTO `cms_page_section_fields` (`id`, `cms_page_section_id`, `field_group`, `field_name`, `field_type`, `field_value`, `created_at`, `updated_at`) VALUES
(172, 25, 'Group_1', 'Description', 'textarea', '<p>Explore curated edits or filter by material to find the silhouettes that suit your season.</p>', '2026-01-05 17:21:12', '2026-01-05 17:22:32'),
(173, 25, 'Group_2', 'Heading', 'text', 'Apply the incentive', '2026-01-05 17:21:51', '2026-01-05 17:22:32'),
(174, 25, 'Group_2', 'Description', 'textarea', '<p>Add the promotion code at checkout or rely on eligible perks that apply automatically.</p>', '2026-01-05 17:21:51', '2026-01-05 17:22:32'),
(175, 25, 'Group_3', 'Heading', 'text', 'Enjoy the extras', '2026-01-05 17:21:51', '2026-01-05 17:22:32'),
(176, 25, 'Group_3', 'Description', 'textarea', '<p>Receive tracking, care tips, and priority stylist support with every promotional order.</p>', '2026-01-05 17:21:51', '2026-01-05 17:22:33'),
(177, 26, NULL, 'Title', 'text', 'Promotion FAQs', '2026-01-05 17:29:31', '2026-01-05 17:29:40'),
(178, 26, NULL, 'Description', 'textarea', '<p>Need quick clarity? We compiled the essentials so you can shop your offers with confidence.</p>', '2026-01-05 17:29:31', '2026-01-05 17:29:40'),
(179, 27, 'Group_1', 'Question', 'textarea', '<h3>Can I combine multiple promotion codes on one order?</h3>', '2026-01-05 17:31:06', '2026-01-05 17:31:51'),
(180, 27, 'Group_1', 'Answer', 'textarea', '<p>Most promotions are single-use per checkout. Loyalty perks like free express shipping will still apply automatically where eligible.</p>', '2026-01-05 17:31:06', '2026-01-05 17:31:51'),
(181, 27, 'Group_2', 'Question', 'textarea', '<h3>How do I know when a promotion expires?</h3>', '2026-01-05 17:31:22', '2026-01-05 17:31:51'),
(182, 27, 'Group_2', 'Answer', 'textarea', '<p>Each offer lists its end date in the details above. We also send reminder emails 48 hours before a promotion closes.</p>', '2026-01-05 17:31:22', '2026-01-05 17:31:51'),
(183, 27, 'Group_3', 'Question', 'textarea', '<h3>Do promotional purchases qualify for returns or exchanges?</h3>', '2026-01-05 17:31:24', '2026-01-05 17:31:51'),
(184, 27, 'Group_3', 'Answer', 'textarea', '<p>Absolutely. All scarves purchased with promotions follow our standard 30-day return and exchange policy as long as tags remain attached.</p>', '2026-01-05 17:31:24', '2026-01-05 17:31:51'),
(185, 28, NULL, 'Title', 'text', 'Ready for Your Next Signature Layer?', '2026-01-05 17:32:49', '2026-01-05 17:33:11'),
(186, 28, NULL, 'Description', 'textarea', '<p>Build your scarf wardrobe with thoughtful perks, seasonal previews, and guidance from our stylists. Your next wrap is already waiting.</p>', '2026-01-05 17:32:49', '2026-01-05 17:33:11'),
(187, 28, NULL, 'Button Text', 'text', 'Explore promotions in store', '2026-01-05 17:32:49', '2026-01-05 17:33:11'),
(188, 29, NULL, 'Title', 'text', 'Shop by Category', '2026-01-05 17:43:47', '2026-01-05 17:44:41'),
(189, 29, NULL, 'Heading', 'text', 'Curate Your Signature Edit', '2026-01-05 17:43:47', '2026-01-05 17:44:41'),
(190, 29, NULL, 'Description', 'textarea', '<p>Discover silhouettes crafted for every mood&mdash;luxurious silks, breathable cottons, cold-weather layers, and limited-run artisan prints.</p>', '2026-01-05 17:43:47', '2026-01-05 17:44:41'),
(191, 29, NULL, 'Image', 'image', 'cms_fields/1767635081_695bf8893a07b.jpeg', '2026-01-05 17:43:47', '2026-01-05 17:44:41'),
(192, 29, NULL, 'Button 1', 'text', 'Explore All Scarves', '2026-01-05 17:43:47', '2026-01-05 17:44:41'),
(193, 29, NULL, 'Button 2', 'text', 'Browse Categories', '2026-01-05 17:43:47', '2026-01-05 17:44:41'),
(194, 30, NULL, 'Title', 'text', 'Categories at a Glance', '2026-01-05 17:46:41', '2026-01-05 17:46:52'),
(195, 30, NULL, 'Description', 'textarea', '<p>From heirloom-worthy silks to everyday essentials, explore the categories curated by our design studio. Each edit is photographed in-house to highlight drape, texture, and styling versatility.</p>', '2026-01-05 17:46:41', '2026-01-05 17:46:52'),
(196, 31, NULL, 'Title', 'text', 'Curated Spotlights', '2026-01-05 17:49:46', '2026-01-05 17:49:54'),
(197, 31, NULL, 'Description', 'textarea', '<p>Dive deeper into seasonal edits, styling guides, and the artisan stories shaping each collection.</p>', '2026-01-05 17:49:46', '2026-01-05 17:49:54'),
(198, 32, 'Group_1', 'Title', 'text', 'Elevated Neutrals', '2026-01-05 17:51:07', '2026-01-05 17:51:26'),
(199, 32, 'Group_1', 'Description', 'textarea', '<p>Layer versatile neutrals with tonal textures for gallery-ready polish.</p>', '2026-01-05 17:51:07', '2026-01-05 17:51:26'),
(200, 32, 'Group_1', 'Image', 'image', 'cms_fields/1767635485_695bfa1d070b9.jpeg', '2026-01-05 17:51:07', '2026-01-05 17:51:26'),
(201, 32, 'Group_2', 'Title', 'text', 'Colorblock Capsule', '2026-01-05 17:51:58', '2026-01-05 17:54:14'),
(202, 32, 'Group_2', 'Description', 'textarea', '<p>Bold contrasts and graphic stripes for standout city commutes.</p>', '2026-01-05 17:51:58', '2026-01-05 17:54:14'),
(203, 32, 'Group_2', 'Image', 'image', 'cms_fields/1767635567_695bfa6f24db2.png', '2026-01-05 17:51:58', '2026-01-05 17:54:14'),
(204, 32, 'Group_3', 'Title', 'text', 'Weekend Escape', '2026-01-05 17:51:59', '2026-01-05 17:54:14'),
(205, 32, 'Group_3', 'Description', 'textarea', '<p>Lightweight cotton-modal blends that roll neatly into weekender bags.</p>', '2026-01-05 17:51:59', '2026-01-05 17:54:14'),
(206, 32, 'Group_3', 'Image', 'image', 'cms_fields/1767635584_695bfa80a91a6.png', '2026-01-05 17:51:59', '2026-01-05 17:54:14'),
(207, 32, 'Group_4', 'Title', 'text', 'Occasion Edit', '2026-01-05 17:52:00', '2026-01-05 17:54:14'),
(208, 32, 'Group_4', 'Description', 'textarea', '<p>Silk jacquards and hand-finished tassels for celebrations that deserve a luminous entrance.</p>', '2026-01-05 17:52:00', '2026-01-05 17:54:14'),
(209, 32, 'Group_4', 'Image', 'image', 'cms_fields/1767635603_695bfa93e3fb4.jpeg', '2026-01-05 17:52:00', '2026-01-05 17:54:14'),
(210, 32, 'Group_5', 'Title', 'text', 'Studio Stories', '2026-01-05 17:52:02', '2026-01-05 17:54:14'),
(211, 32, 'Group_5', 'Description', 'textarea', '<p>Behind-the-loom snapshots spotlighting artisan techniques and limited drops.</p>', '2026-01-05 17:52:02', '2026-01-05 17:54:14'),
(212, 32, 'Group_5', 'Image', 'image', 'cms_fields/1767635625_695bfaa9a36b6.jpeg', '2026-01-05 17:52:02', '2026-01-05 17:54:14'),
(213, 32, 'Group_6', 'Title', 'text', 'Travel Light', '2026-01-05 17:52:03', '2026-01-05 17:54:14'),
(214, 32, 'Group_6', 'Description', 'textarea', '<p>Packable cotton scarves and wrinkle-resistant blends for curated carry-ons.</p>', '2026-01-05 17:52:03', '2026-01-05 17:54:14'),
(215, 32, 'Group_6', 'Image', 'image', 'cms_fields/1767635649_695bfac11b9de.jpeg', '2026-01-05 17:52:03', '2026-01-05 17:54:14'),
(216, 33, NULL, 'Title', 'text', 'Shop the Edit', '2026-01-06 00:28:38', '2026-01-06 00:29:13'),
(217, 33, NULL, 'Heading', 'text', 'Shop', '2026-01-06 00:28:38', '2026-01-06 00:29:13'),
(218, 33, NULL, 'Description', 'textarea', '<p>Discover refined silhouettes, artisanal finishes, and everyday staples curated for the modern wardrobe.</p>', '2026-01-06 00:28:38', '2026-01-06 00:29:13'),
(219, 33, NULL, 'Image', 'image', 'cms_fields/1767659352_695c5758e5b78.jpeg', '2026-01-06 00:28:38', '2026-01-06 00:29:13'),
(220, 33, NULL, 'Button 1', 'text', 'Explore All Scarves', '2026-01-06 00:28:38', '2026-01-06 00:29:13'),
(221, 33, NULL, 'Button 2', 'text', 'Browse Hijaabs', '2026-01-06 00:28:38', '2026-01-06 00:29:33'),
(222, 34, NULL, 'Title', 'text', 'Shop Our Collection', '2026-01-06 18:51:22', '2026-01-06 18:51:31'),
(223, 34, NULL, 'Description', 'textarea', '<p>Explore curated edits that pair luxurious fabrics with modern silhouettes. Select filters to discover the scarves that speak to your signature style.</p>', '2026-01-06 18:51:22', '2026-01-06 18:51:31'),
(224, 35, NULL, 'Title', 'text', 'Refine Results', '2026-01-06 18:53:34', '2026-01-06 18:54:50'),
(225, 35, NULL, 'Description', 'textarea', '<p>Tailor the collection to match your style, fabric, and color preferences.</p>', '2026-01-06 18:53:34', '2026-01-06 18:54:50'),
(226, 36, NULL, 'Title', 'text', 'We\'re here for every layer', '2026-01-06 19:01:52', '2026-01-06 19:02:21'),
(227, 36, NULL, 'Heading', 'text', 'Contact Our Team', '2026-01-06 19:01:52', '2026-01-06 19:02:21'),
(228, 36, NULL, 'Description', 'textarea', '<p>From bespoke styling sessions to shipping updates, our concierge team is ready to help your scarf wardrobe feel effortless. Reach out and we&#39;ll tailor the support to you.</p>', '2026-01-06 19:01:52', '2026-01-06 19:02:21'),
(229, 36, NULL, 'Image', 'image', 'cms_fields/1767726151_695d5c47c5378.jpeg', '2026-01-06 19:01:52', '2026-01-06 19:02:32'),
(230, 36, NULL, 'Button 1', 'text', 'Send a message', '2026-01-06 19:01:52', '2026-01-06 19:02:21'),
(231, 36, NULL, 'Button 2', 'text', 'Call the atelier', '2026-01-06 19:01:52', '2026-01-06 19:02:21'),
(232, 37, NULL, 'Title', 'text', 'Choose your channel', '2026-01-06 19:03:29', '2026-01-06 19:03:46'),
(233, 37, NULL, 'Heading', 'text', 'Connect with our team', '2026-01-06 19:03:29', '2026-01-06 19:03:46'),
(234, 37, NULL, 'Description', 'textarea', '<p>Whether you need styling advice, order support, or bespoke gifting, reach out through the path that feels right and we will make sure the experience stays effortless.</p>', '2026-01-06 19:03:29', '2026-01-06 19:03:46'),
(235, 39, 'Group_1', 'Title', 'text', 'Styling Concierge', '2026-01-06 19:58:15', '2026-01-06 20:14:45'),
(236, 39, 'Group_1', 'Description', 'textarea', '<p>Book a one-on-one consult with our stylists for color palettes, pairing advice, and capsule planning.</p>', '2026-01-06 19:58:15', '2026-01-06 20:14:46'),
(237, 39, 'Group_1', 'Tagline', 'text', 'Replies within 12 hours', '2026-01-06 19:58:15', '2026-01-06 20:14:46'),
(238, 39, 'Group_1', 'Button', 'text', 'Email the stylists', '2026-01-06 19:58:15', '2026-01-06 20:14:46'),
(239, 39, 'Group_2', 'Title', 'text', 'Client Care', '2026-01-06 20:16:52', '2026-01-06 20:17:49'),
(240, 39, 'Group_2', 'Description', 'textarea', '<p>Questions about orders, returns, or loyalty perks? Our client care team is here every day of the week.</p>', '2026-01-06 20:16:52', '2026-01-06 20:17:49'),
(241, 39, 'Group_2', 'Tagline', 'text', 'Chat available 9am - 7pm ET', '2026-01-06 20:16:52', '2026-01-06 20:17:49'),
(242, 39, 'Group_2', 'Button', 'text', 'Start a live chat', '2026-01-06 20:16:52', '2026-01-06 20:17:49'),
(243, 39, 'Group_3', 'Title', 'text', 'Boutique Line', '2026-01-06 20:16:54', '2026-01-06 20:17:49'),
(244, 39, 'Group_3', 'Description', 'textarea', '<p>Call the flagship studio directly for bespoke gifting, corporate orders, or event styling support.</p>', '2026-01-06 20:16:54', '2026-01-06 20:17:49'),
(245, 39, 'Group_3', 'Tagline', 'text', 'Phone support 10am - 6pm ET', '2026-01-06 20:16:54', '2026-01-06 20:17:49'),
(246, 39, 'Group_3', 'Button', 'text', 'Call +1 (212) 555-1045', '2026-01-06 20:16:54', '2026-01-06 20:17:49'),
(247, 40, NULL, 'Title', 'text', 'Tell us how we can help', '2026-01-06 20:49:06', '2026-01-06 20:49:21'),
(248, 40, NULL, 'Heading', 'text', 'Send a message', '2026-01-06 20:49:06', '2026-01-06 20:49:21'),
(249, 40, NULL, 'Description', 'textarea', '<p>Fill out the form below and we&#39;ll reach out with next steps. Include order numbers or deadlines so we can prioritize accordingly.</p>', '2026-01-06 20:49:06', '2026-01-06 20:49:21'),
(250, 41, NULL, 'Title', 'text', 'Visit the atelier', '2026-01-06 20:50:03', '2026-01-06 20:50:15'),
(251, 41, NULL, 'Description', 'textarea', '<p>We welcome private appointments to explore heritage weaves, preview upcoming collections, and tailor gifting sets. Drop us a note before you arrive so we can prepare your selection.</p>', '2026-01-06 20:50:03', '2026-01-06 20:50:15'),
(252, 42, NULL, 'Title', 'text', 'Quick Answers', '2026-01-06 20:52:39', '2026-01-06 20:52:48'),
(253, 42, NULL, 'Description', 'textarea', '<p>These are the questions we hear most. If you do not see yours, send a note - we love a thoughtful inquiry.</p>', '2026-01-06 20:52:39', '2026-01-06 20:52:48'),
(254, 43, 'Group_1', 'Title', 'text', 'How quickly will someone respond?', '2026-01-06 20:54:38', '2026-01-06 20:54:50'),
(255, 43, 'Group_1', 'Description', 'textarea', '<p>We aim to reply to concierge and client care messages within one business day. During launch periods, allow up to 48 hours; we&#39;ll prioritize urgent order changes.</p>', '2026-01-06 20:54:38', '2026-01-06 20:54:50'),
(256, 43, 'Group_2', 'Title', 'text', 'Can I schedule a virtual fitting?', '2026-01-06 20:55:04', '2026-01-06 20:55:23'),
(257, 43, 'Group_2', 'Description', 'textarea', '<p>Yes. Select &quot;Styling Concierge&quot; in the form and add preferred dates. We will confirm a virtual fitting with a stylist who specializes in your palette and silhouette preferences.</p>', '2026-01-06 20:55:04', '2026-01-06 20:55:23'),
(258, 43, 'Group_3', 'Title', 'text', 'Do you offer corporate gifting support?', '2026-01-06 20:55:05', '2026-01-06 20:55:23'),
(259, 43, 'Group_3', 'Description', 'textarea', '<p>Absolutely. Share quantities, timelines, and any personalization notes in the message field. Our gifting team will craft a tailored proposal with pricing within 24 hours.</p>', '2026-01-06 20:55:05', '2026-01-06 20:55:23'),
(260, 44, NULL, 'Title', 'text', 'Clear standards for every order', '2026-01-06 23:50:54', '2026-01-06 23:52:49'),
(261, 44, NULL, 'Heading', 'text', 'Terms and Conditions', '2026-01-06 23:50:54', '2026-01-06 23:52:49'),
(262, 44, NULL, 'Description', 'textarea', '<p>These terms explain how you can use the Hijaabster site, participate in loyalty perks, and collaborate with our team. Please review them carefully.</p>', '2026-01-06 23:50:54', '2026-01-06 23:52:49'),
(263, 44, NULL, 'Image', 'image', 'cms_fields/1767743583_695da05f07eeb.jpeg', '2026-01-06 23:50:54', '2026-01-06 23:53:03'),
(264, 44, NULL, 'Button 1', 'text', 'View the terms', '2026-01-06 23:50:54', '2026-01-06 23:52:49'),
(265, 44, NULL, 'Button 2', 'text', 'Speak with client care', '2026-01-06 23:50:54', '2026-01-06 23:52:49'),
(266, 45, NULL, 'Title', 'text', 'The essentials of partnering with Hijaabster', '2026-01-07 00:11:01', '2026-01-07 00:12:12'),
(267, 45, NULL, 'Description', 'textarea', '<p>We drafted these terms to keep expectations clear and to celebrate respectful collaboration. As we evolve, we may update this document. When we do, we will post the new date at the top and notify registered members.</p>', '2026-01-07 00:11:01', '2026-01-07 00:12:12'),
(268, 45, NULL, 'Box 1 Title', 'text', 'How updates work', '2026-01-07 00:11:01', '2026-01-07 00:12:12'),
(269, 45, NULL, 'Box 1 Description', 'textarea', '<p>If you continue using the site after an update, it means you accept the revised terms. For major changes, we will reach out via email or in-app messaging in advance so you can review and ask questions.</p>', '2026-01-07 00:11:01', '2026-01-07 00:12:12'),
(272, 46, 'Group_1', 'Title', 'text', 'Accounts and Membership', '2026-01-07 00:15:28', '2026-01-07 00:18:22'),
(273, 46, 'Group_1', 'Description', 'textarea', '<p>Creating an account gives you access to saved styling profiles, order tracking, and loyalty rewards. You agree to provide accurate information and to update it when changes occur so that shipments and communications reach you on time.</p>\r\n\r\n<p>You are responsible for safeguarding your login credentials. If you suspect unauthorized access, please reset your password immediately and contact client care so we can secure your profile.</p>\r\n\r\n<ul>\r\n	<li>Use a unique password and enable multifactor authentication when available</li>\r\n	<li>Do not share account access with others; loyalty benefits are non-transferable</li>\r\n	<li>We may suspend or close accounts that misuse promotions or violate community guidelines</li>\r\n</ul>', '2026-01-07 00:15:28', '2026-01-07 00:18:22'),
(274, 46, 'Group_2', 'Title', 'text', 'Orders, Shipping, and Returns', '2026-01-07 00:18:31', '2026-01-07 00:19:21'),
(275, 46, 'Group_2', 'Description', 'textarea', '<p>All orders are subject to availability. We strive to communicate stock status clearly and will notify you if an item becomes unavailable after checkout.</p>\r\n\r\n<p>Shipping timelines vary by destination and carrier. Once a parcel leaves our studio, tracking details are shared in real time.</p>\r\n\r\n<p>Returns follow the timeline and condition guidelines published on our Returns page. Items must be unworn with original tags to qualify.</p>\r\n\r\n<ul>\r\n	<li>Pre-order pieces ship on the release window noted at checkout</li>\r\n	<li>Express courier options are available for eligible regions at an additional cost</li>\r\n	<li>Refunds are processed to the original payment method within 7-10 business days after inspection</li>\r\n</ul>', '2026-01-07 00:18:31', '2026-01-07 00:19:21'),
(276, 46, 'Group_3', 'Title', 'text', 'Intellectual Property', '2026-01-07 00:18:32', '2026-01-07 00:19:21'),
(277, 46, 'Group_3', 'Description', 'textarea', '<p>The Hijaabster name, brand marks, product photography, copy, and bespoke designs are protected by copyright, trademark, and other applicable intellectual property laws.</p>\r\n\r\n<p>You may not reproduce, redistribute, or create derivative works from our digital or physical assets without prior written consent.</p>\r\n\r\n<ul>\r\n	<li>User-generated content shared with #Hijaabster may be reshared with credit according to our community guidelines</li>\r\n	<li>If you believe your intellectual property has been infringed, contact legal@hijaabster.co with supporting documentation</li>\r\n</ul>', '2026-01-07 00:18:32', '2026-01-07 00:19:21'),
(278, 46, 'Group_4', 'Title', 'text', 'Liability and Disclaimers', '2026-01-07 00:18:33', '2026-01-07 00:19:21'),
(279, 46, 'Group_4', 'Description', 'textarea', '<p>We work diligently to ensure accurate product descriptions, imagery, and sizing information. However, natural variations in hand-dyed textiles may cause slight differences between photographs and delivered items.</p>\r\n\r\n<p>To the fullest extent permitted by law, Hijaabster will not be liable for indirect, incidental, or consequential damages resulting from the use of the site or inability to access services.</p>', '2026-01-07 00:18:33', '2026-01-07 00:19:21'),
(280, 46, 'Group_5', 'Title', 'text', 'Governing Law', '2026-01-07 00:18:34', '2026-01-07 00:19:21'),
(281, 46, 'Group_5', 'Description', 'textarea', '<p>These Terms are governed by the laws of the State of New York, without regard to conflict of law principles. Any disputes will be resolved through binding arbitration in New York City unless both parties agree otherwise in writing.</p>', '2026-01-07 00:18:34', '2026-01-07 00:19:21'),
(282, 47, NULL, 'Title', 'text', 'Need clarification?', '2026-01-07 00:20:29', '2026-01-07 00:20:49'),
(283, 47, NULL, 'Description', 'textarea', '<p>Our client care specialists are ready to walk through any clause, talk timelines, or tailor account settings for your team.</p>', '2026-01-07 00:20:29', '2026-01-07 00:20:49'),
(284, 47, NULL, 'Button 1', 'text', 'Start a conversation', '2026-01-07 00:20:29', '2026-01-07 00:20:49'),
(285, 47, NULL, 'Button 2', 'text', 'Email legal@hijaabster.co', '2026-01-07 00:20:29', '2026-01-07 00:20:49'),
(286, 48, NULL, 'Title', 'text', 'Our commitment to trust', '2026-01-08 01:07:02', '2026-01-08 01:07:43'),
(287, 48, NULL, 'Heading', 'text', 'Privacy Policy', '2026-01-08 01:07:02', '2026-01-08 01:07:43'),
(288, 48, NULL, 'Description', 'textarea', '<p>Your personal data deserves thoughtful stewardship. This policy outlines how we collect, use, and protect your information across the Hijaabster experience.</p>', '2026-01-08 01:07:02', '2026-01-08 01:07:43'),
(289, 48, NULL, 'Image', 'image', 'cms_fields/1767834471_695f036794511.jpeg', '2026-01-08 01:07:02', '2026-01-08 01:07:51'),
(290, 48, NULL, 'Button 1', 'text', 'Read the policy', '2026-01-08 01:07:02', '2026-01-08 01:07:43'),
(291, 48, NULL, 'Button 2', 'text', 'Contact privacy team', '2026-01-08 01:07:02', '2026-01-08 01:07:43'),
(292, 49, NULL, 'Title', 'text', 'A transparent approach to safeguarding your data', '2026-01-08 01:17:13', '2026-01-08 01:17:29'),
(293, 49, NULL, 'Description', 'textarea', '<p>We rely on privacy-by-design principles, meaning data protection is considered at every layer of the product. You can explore the sections below for specifics on collection, usage, sharing, and your rights.</p>', '2026-01-08 01:17:13', '2026-01-08 01:17:29'),
(294, 49, NULL, 'Box 1 Title', 'text', 'Need a copy?', '2026-01-08 01:17:13', '2026-01-08 01:17:29'),
(295, 49, NULL, 'Box 1 Description', 'textarea', '<p>Email&nbsp;<a href=\"mailto:privacy@hijaabster.co\">privacy@hijaabster.co</a>&nbsp;to request a PDF or to exercise any of your data rights. We respond within two business days.</p>', '2026-01-08 01:17:13', '2026-01-08 01:17:29'),
(296, 50, 'Group_1', 'Title', 'text', 'Information We Collect', '2026-01-08 01:18:34', '2026-01-08 01:18:45'),
(297, 50, 'Group_1', 'Description', 'textarea', '<p>We gather personal details that you share directly with us, as well as limited data generated automatically when you explore our store. Every collection point is designed to support your experience and keep your account secure.</p>\r\n\r\n<ul>\r\n	<li>Contact information: name, email, shipping details, and phone number</li>\r\n	<li>Account preferences: saved styles, wishlists, and fit notes</li>\r\n	<li>Order history and payment confirmation (processed securely by our PCI-compliant partners)</li>\r\n	<li>Device and usage insights such as browser type, pages viewed, and referral source</li>\r\n</ul>', '2026-01-08 01:18:34', '2026-01-08 01:18:45'),
(298, 50, 'Group_2', 'Title', 'text', 'How We Use Your Data', '2026-01-08 01:18:52', '2026-01-08 01:19:33'),
(299, 50, 'Group_2', 'Description', 'textarea', '<p>Data allows us to personalize product suggestions, deliver your purchases, and maintain a safe community. We never sell personal information and limit access to trained team members only.</p>\r\n\r\n<ul>\r\n	<li>Fulfil orders, provide shipping updates, and manage returns or exchanges</li>\r\n	<li>Tailor product recommendations, loyalty perks, and styling guidance</li>\r\n	<li>Support customer care conversations by referencing prior requests</li>\r\n	<li>Improve the site through analytics that help us understand navigation patterns</li>\r\n	<li>Protect our brand and community by detecting fraud and unauthorized activity</li>\r\n</ul>', '2026-01-08 01:18:52', '2026-01-08 01:19:33'),
(300, 50, 'Group_3', 'Title', 'text', 'Sharing and Disclosure', '2026-01-08 01:18:52', '2026-01-08 01:19:33'),
(301, 50, 'Group_3', 'Description', 'textarea', '<p>We collaborate with a select group of service providers to deliver a seamless experience. Each partner signs strict agreements to process data only on our instructions and to maintain robust safeguards.</p>\r\n\r\n<ul>\r\n	<li>Shipping and fulfilment partners to deliver your orders</li>\r\n	<li>Payment processors that handle secure transactions</li>\r\n	<li>Email and marketing tools used for opted-in communications</li>\r\n	<li>Analytics platforms that help us review aggregated, non-identifiable usage trends</li>\r\n</ul>', '2026-01-08 01:18:52', '2026-01-08 01:19:33'),
(302, 50, 'Group_4', 'Title', 'text', 'Your Choices and Rights', '2026-01-08 01:18:53', '2026-01-08 01:19:33'),
(303, 50, 'Group_4', 'Description', 'textarea', '<p>You stay in control of how we use your data. Adjust preferences at any time or reach out to our privacy team for specific requests.</p>\r\n\r\n<ul>\r\n	<li>Update or delete account information from your profile dashboard</li>\r\n	<li>Opt out of marketing emails via the unsubscribe link or account settings</li>\r\n	<li>Request a copy of your personal data or ask us to erase it entirely</li>\r\n	<li>Disable cookies in your browser (note this may impact site performance)</li>\r\n</ul>', '2026-01-08 01:18:53', '2026-01-08 01:19:33'),
(304, 51, NULL, 'Title', 'text', 'Questions about privacy?', '2026-01-08 01:20:11', '2026-01-08 01:20:30'),
(305, 51, NULL, 'Description', 'textarea', '<p>We take every inquiry seriously. Let us know how we can support your data preferences, from opt-outs to detailed reports.</p>', '2026-01-08 01:20:11', '2026-01-08 01:20:30'),
(306, 51, NULL, 'Button 1', 'text', 'Message our privacy team', '2026-01-08 01:20:11', '2026-01-08 01:20:30'),
(307, 51, NULL, 'Button 2', 'text', 'Email privacy@hijaabster.co', '2026-01-08 01:20:11', '2026-01-08 01:20:30'),
(308, 52, NULL, 'Title', 'text', 'Accessibility statement', '2026-01-08 01:28:33', '2026-01-08 01:29:10'),
(309, 52, NULL, 'Heading', 'text', 'Inclusive by design', '2026-01-08 01:28:33', '2026-01-08 01:29:10'),
(310, 52, NULL, 'Description', 'textarea', '<p>Hijaabster is built for every body and every way of navigating the world. We design, test, and iterate so you can explore scarves, book styling sessions, and manage your account without friction.</p>', '2026-01-08 01:28:33', '2026-01-08 01:29:10'),
(311, 52, NULL, 'Image', 'image', 'cms_fields/1767835750_695f08669fee5.jpeg', '2026-01-08 01:28:33', '2026-01-08 01:29:10'),
(312, 52, NULL, 'Button 1', 'text', 'Review our commitments', '2026-01-08 01:28:33', '2026-01-08 01:29:10'),
(313, 52, NULL, 'Button 2', 'text', 'Request accommodations', '2026-01-08 01:28:33', '2026-01-08 01:29:10'),
(314, 53, NULL, 'Title', 'text', 'Designing a shopping experience without barriers', '2026-01-08 01:30:11', '2026-01-08 01:30:25'),
(315, 53, NULL, 'Description', 'textarea', '<p>Accessibility is part of our design system, not an afterthought. The sections below highlight the pillars that shape every release, from coded components to concierge support.</p>', '2026-01-08 01:30:11', '2026-01-08 01:30:25'),
(316, 53, NULL, 'Box 1 Title', 'text', 'Need this statement in another format?', '2026-01-08 01:30:11', '2026-01-08 01:30:25'),
(317, 53, NULL, 'Box 1 Description', 'textarea', '<p>Email&nbsp;<a href=\"mailto:access@hijaabster.co\">access@hijaabster.co</a>&nbsp;for large-print, Braille, or translated copies. We fulfill requests within five business days.</p>', '2026-01-08 01:30:11', '2026-01-08 01:30:25'),
(318, 54, 'Group_1', 'Title', 'text', 'Digital Standards We Follow', '2026-01-08 01:38:50', '2026-01-08 01:38:59'),
(319, 54, 'Group_1', 'Description', 'textarea', '<p>Our e-commerce experience is built with semantic HTML, keyboard-friendly navigation, and ARIA best practices so screen reader users can browse without barriers.</p>\r\n\r\n<p>We routinely audit new releases against WCAG 2.2 AA success criteria and update components when guidelines evolve.</p>\r\n\r\n<ul>\r\n	<li>Contrast ratios meet or exceed 4.5:1 for text and interactive elements</li>\r\n	<li>Forms include labels, error messaging, and descriptive help text</li>\r\n	<li>Images use descriptive alt text and avoid decorative content where possible</li>\r\n	<li>Motion, autoplay, and animations can be paused when they appear on the page</li>\r\n</ul>', '2026-01-08 01:38:50', '2026-01-08 01:38:59'),
(320, 54, 'Group_2', 'Title', 'text', 'Accessible Styling Services', '2026-01-08 01:39:05', '2026-01-08 01:39:25'),
(321, 54, 'Group_2', 'Description', 'textarea', '<p>Fashion should feel inclusive. Our concierge programs adapt to your preferred communication style and any assistive technology you use.</p>\r\n\r\n<ul>\r\n	<li>Virtual styling sessions available with captions or live ASL interpreters</li>\r\n	<li>Product descriptions include fabric weight, drape, and closure details for tactile contexts</li>\r\n	<li>Braille product cards and large-print care guides available upon request</li>\r\n	<li>In-studio appointments accommodate mobility devices and sensory considerations</li>\r\n</ul>', '2026-01-08 01:39:05', '2026-01-08 01:39:25'),
(322, 54, 'Group_3', 'Title', 'text', 'Continuous Improvement', '2026-01-08 01:39:07', '2026-01-08 01:39:25'),
(323, 54, 'Group_3', 'Description', 'textarea', '<p>Accessibility is an ongoing partnership. We train our team, invite community feedback, and invest in inclusive tooling to keep improving.</p>\r\n\r\n<ul>\r\n	<li>Quarterly audits by third-party accessibility specialists</li>\r\n	<li>Design system tokens tuned for contrast, spacing, and focus visibility</li>\r\n	<li>Internal education so every launch team understands inclusive best practices</li>\r\n	<li>Bug triage that prioritizes access-related fixes with dedicated SLAs</li>\r\n</ul>', '2026-01-08 01:39:07', '2026-01-08 01:39:25'),
(324, 55, NULL, 'Title', 'text', 'Share your feedback', '2026-01-08 01:40:16', '2026-01-08 01:40:40'),
(325, 55, NULL, 'Description', 'textarea', '<p>If you encounter an access barrier, we want to hear about it. Your insight helps us prioritize updates that make the experience better for everyone.</p>', '2026-01-08 01:40:16', '2026-01-08 01:40:40'),
(326, 55, NULL, 'Button 1', 'text', 'Submit a feedback form', '2026-01-08 01:40:16', '2026-01-08 01:40:40'),
(327, 55, NULL, 'Button 2', 'text', 'Call +1 (212) 555-1045', '2026-01-08 01:40:16', '2026-01-08 01:40:40'),
(328, 55, NULL, 'Button 3', 'text', 'Email access@hijaabster.co', '2026-01-08 01:40:16', '2026-01-08 01:40:40'),
(329, 56, NULL, 'Title', 'text', 'Curate before checkout', '2026-01-09 18:38:43', '2026-01-09 18:39:16'),
(330, 56, NULL, 'Heading', 'text', 'Your Cart', '2026-01-09 18:38:43', '2026-01-09 18:39:16'),
(331, 56, NULL, 'Description', 'textarea', '<p>Review your selections, tailor quantities, and confirm delivery details. Everything stays reserved for the next 30 minutes.</p>', '2026-01-09 18:38:43', '2026-01-09 18:39:16'),
(332, 56, NULL, 'Image', 'image', 'cms_fields/1767983964_69614b5c2146c.jpeg', '2026-01-09 18:38:43', '2026-01-09 18:39:24'),
(333, 56, NULL, 'Button 1', 'text', 'Review items', '2026-01-09 18:38:43', '2026-01-09 18:39:16'),
(334, 56, NULL, 'Button 2', 'text', 'Continue shopping', '2026-01-09 18:38:43', '2026-01-09 18:39:16'),
(335, 57, NULL, 'Title', 'text', 'You may also like', '2026-01-09 18:44:27', '2026-01-09 18:44:37'),
(336, 57, NULL, 'Heading', 'text', 'Complete your ritual', '2026-01-09 18:44:27', '2026-01-09 18:44:37'),
(337, 57, NULL, 'Description', 'textarea', '<p>Finish your capsule with thoughtful add-ons curated to complement the pieces in your cart.</p>', '2026-01-09 18:44:27', '2026-01-09 18:44:37'),
(338, 58, NULL, 'Title', 'text', 'Final touches', '2026-01-09 19:02:24', '2026-01-09 19:02:45'),
(339, 58, NULL, 'Heading', 'text', 'Checkout', '2026-01-09 19:02:24', '2026-01-09 19:02:45'),
(340, 58, NULL, 'Description', 'textarea', '<p>Confirm delivery details, choose your preferred shipping experience, and review the scarves joining your collection.</p>', '2026-01-09 19:02:24', '2026-01-09 19:02:45'),
(341, 58, NULL, 'Image', 'image', 'cms_fields/1767985364_696150d4cb507.jpeg', '2026-01-09 19:02:24', '2026-01-09 19:02:45'),
(342, 58, NULL, 'Button 1', 'text', 'Continue below', '2026-01-09 19:02:24', '2026-01-09 19:02:45'),
(343, 58, NULL, 'Button 2', 'text', 'Back to cart', '2026-01-09 19:02:24', '2026-01-09 19:02:45'),
(344, 59, NULL, 'Title', 'text', 'Need a stylist?', '2026-01-09 19:03:38', '2026-01-09 19:03:52'),
(345, 59, NULL, 'Description', 'textarea', '<p>Our concierge team can recommend complementary pieces, confirm sizing, or schedule a gift delivery on your behalf. Leave a note above or reach out directly.</p>', '2026-01-09 19:03:38', '2026-01-09 19:03:52'),
(346, 59, NULL, 'Button 1', 'text', 'Message concierge', '2026-01-09 19:03:38', '2026-01-09 19:03:52'),
(347, 59, NULL, 'Button 2', 'text', 'Call +1 (212) 555-1045', '2026-01-09 19:03:38', '2026-01-09 19:03:52');

INSERT INTO `cms_page_sections` (`id`, `cms_page_id`, `section_name`, `section_type`, `section_sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, 'Home Banner | 1st Section', 'repeater', 1, '2025-12-23 18:41:12', '2026-01-01 21:51:50');
INSERT INTO `cms_page_sections` (`id`, `cms_page_id`, `section_name`, `section_type`, `section_sort_order`, `created_at`, `updated_at`) VALUES
(2, 1, 'Why Shop | 2nd Section', 'single', 2, '2026-01-01 21:51:29', '2026-01-01 21:51:29');
INSERT INTO `cms_page_sections` (`id`, `cms_page_id`, `section_name`, `section_type`, `section_sort_order`, `created_at`, `updated_at`) VALUES
(3, 1, 'Featured Products | 3rd Section', 'single', 3, '2026-01-01 22:19:15', '2026-01-01 22:19:15');
INSERT INTO `cms_page_sections` (`id`, `cms_page_id`, `section_name`, `section_type`, `section_sort_order`, `created_at`, `updated_at`) VALUES
(4, 1, 'Shop by Category | 5th Section', 'single', 4, '2026-01-01 22:22:21', '2026-01-01 22:22:21'),
(5, 1, 'Limited Offer | 7th Section', 'single', 5, '2026-01-01 22:37:41', '2026-01-01 22:37:41'),
(6, 1, 'Testimonials | 8th Section', 'repeater', 6, '2026-01-01 23:00:12', '2026-01-01 23:00:12'),
(7, 1, 'Our Story | 9th Section', 'single', 7, '2026-01-01 23:26:32', '2026-01-01 23:26:32'),
(8, 1, 'Our Mission | 10th Section', 'single', 8, '2026-01-01 23:29:40', '2026-01-01 23:29:40'),
(9, 1, 'Our Vision | 11th Section', 'single', 9, '2026-01-01 23:32:46', '2026-01-01 23:32:46'),
(10, 2, 'Banner | 1st Section', 'single', 1, '2026-01-02 01:01:20', '2026-01-02 17:35:33'),
(11, 2, 'Crafted | 2nd Section', 'single', 2, '2026-01-02 17:36:08', '2026-01-02 17:36:08'),
(13, 2, 'About us | 3rd Section', 'repeater', 3, '2026-01-02 17:45:32', '2026-01-02 17:45:32'),
(14, 2, 'From inspiration | 4th Section', 'single', 4, '2026-01-02 17:51:00', '2026-01-02 17:51:00'),
(15, 2, 'Inspiration Points | 4th Section', 'repeater', 5, '2026-01-02 18:03:32', '2026-01-02 18:03:32'),
(16, 2, 'Milestones | 5th Section', 'single', 6, '2026-01-02 18:09:09', '2026-01-02 18:09:09'),
(17, 2, 'Timeline | 5th Section', 'repeater', 7, '2026-01-02 18:19:22', '2026-01-02 18:19:22'),
(18, 2, 'Commitments | 6th Section', 'single', 8, '2026-01-02 18:24:36', '2026-01-02 18:24:36'),
(19, 3, 'Banner | 1st Section', 'single', 1, '2026-01-02 18:24:36', '2026-01-02 18:24:36'),
(20, 3, 'Bundle | 2nd Section', 'single', 2, '2026-01-02 23:55:30', '2026-01-02 23:55:30'),
(21, 3, 'Featured Promotions | 3rd Section', 'single', 3, '2026-01-05 16:47:22', '2026-01-05 16:47:22'),
(22, 3, 'Upcoming Drops | 4th Section', 'single', 4, '2026-01-05 16:51:13', '2026-01-05 16:51:13'),
(23, 3, 'Extra Info | 4th Section', 'single', 5, '2026-01-05 16:54:56', '2026-01-05 16:54:56'),
(24, 3, 'Redeeming | 5th Section', 'single', 6, '2026-01-05 16:58:09', '2026-01-05 16:58:09'),
(25, 3, 'Select your pieces | 5th Section', 'repeater', 7, '2026-01-05 17:20:45', '2026-01-05 17:20:45'),
(26, 3, 'Promotion FAQs | 8th Section', 'single', 8, '2026-01-05 17:29:06', '2026-01-05 17:29:13'),
(27, 3, 'Faqs | 8th Section', 'repeater', 9, '2026-01-05 17:30:41', '2026-01-05 17:30:41'),
(28, 3, 'Signature Layer | 9th Section', 'single', 10, '2026-01-05 17:32:17', '2026-01-05 17:32:17'),
(29, 4, 'Banner | 1st Section', 'single', 1, '2026-01-05 17:41:01', '2026-01-05 17:41:01'),
(30, 4, 'Categories Glance | 2nd Section', 'single', 2, '2026-01-05 17:45:56', '2026-01-05 17:45:56'),
(31, 4, 'Curated Spotlights | 3rd Section', 'single', 3, '2026-01-05 17:49:26', '2026-01-05 17:49:26'),
(32, 4, 'Spotlights | 3rd Section', 'repeater', 4, '2026-01-05 17:50:43', '2026-01-05 17:50:43'),
(33, 5, 'Banner | 1st Section', 'single', 1, '2026-01-06 00:27:47', '2026-01-06 00:27:47'),
(34, 5, 'Shop Collection | 2nd Section', 'single', 2, '2026-01-06 18:48:51', '2026-01-06 18:48:51'),
(35, 5, 'Refine Results | Cat Section', 'single', 3, '2026-01-06 18:53:23', '2026-01-06 18:53:23'),
(36, 6, 'Banner | 1st Section', 'single', 1, '2026-01-06 18:56:48', '2026-01-06 18:56:48'),
(37, 6, 'Connect with team | 2nd Section', 'single', 2, '2026-01-06 19:03:07', '2026-01-06 19:03:07'),
(39, 6, 'Box Content | 3rd Section', 'repeater', 3, '2026-01-06 19:56:53', '2026-01-06 19:56:53'),
(40, 6, 'we can help | 4th Section', 'single', 4, '2026-01-06 20:48:42', '2026-01-06 20:48:42'),
(41, 6, 'Visit Atelier | 4th Section', 'single', 5, '2026-01-06 20:49:47', '2026-01-06 20:49:47'),
(42, 6, 'Quick Answers Content | 5th Section', 'single', 6, '2026-01-06 20:52:22', '2026-01-06 20:53:42'),
(43, 6, 'Quick Answers | 6th Section', 'repeater', 7, '2026-01-06 20:54:22', '2026-01-06 20:54:22'),
(44, 7, 'Banner | 1st Section', 'single', 1, '2026-01-06 23:48:49', '2026-01-06 23:48:49'),
(45, 7, 'Partnering | 2nd Section', 'single', 2, '2026-01-06 23:53:37', '2026-01-06 23:53:37'),
(46, 7, 'Box Content | 3rd Section', 'repeater', 3, '2026-01-07 00:13:00', '2026-01-07 00:19:59'),
(47, 7, 'Need clarification | 4th Section', 'single', 4, '2026-01-07 00:19:49', '2026-01-07 00:19:49'),
(48, 8, 'Banner | 1st Section', 'single', 1, '2026-01-08 01:06:26', '2026-01-08 01:06:26'),
(49, 8, 'Safeguarding Data | 2nd Section', 'single', 2, '2026-01-08 01:09:09', '2026-01-08 01:09:09'),
(50, 8, 'Box Content | 3rd Content', 'repeater', 3, '2026-01-08 01:18:00', '2026-01-08 01:18:00'),
(51, 8, 'Questions about privacy | 4th Section', 'single', 4, '2026-01-08 01:19:49', '2026-01-08 01:19:49'),
(52, 9, 'Banner | 1st Section', 'single', 1, '2026-01-08 01:24:35', '2026-01-08 01:24:35'),
(53, 9, 'Shopping Experience | 2nd Section', 'single', 2, '2026-01-08 01:29:52', '2026-01-08 01:29:52'),
(54, 9, 'Box Content | 3rd Section', 'repeater', 3, '2026-01-08 01:38:35', '2026-01-08 01:38:35'),
(55, 9, 'Feedback | 4th Section', 'single', 4, '2026-01-08 01:39:52', '2026-01-08 01:39:52'),
(56, 10, 'Banner | 1st Section', 'single', 1, '2026-01-09 18:38:00', '2026-01-09 18:38:00'),
(57, 10, 'Also Like | 2nd Section', 'single', 2, '2026-01-09 18:44:10', '2026-01-09 18:44:10'),
(58, 11, 'Banner 1st Section', 'single', 1, '2026-01-09 19:02:01', '2026-01-09 19:02:01'),
(59, 11, 'Need a stylist | 2nd Section', 'single', 2, '2026-01-09 19:03:15', '2026-01-09 19:03:15');

INSERT INTO `cms_pages` (`id`, `page_title`, `page_slug`, `page_meta_title`, `page_meta_keyword`, `page_meta_description`, `created_at`, `updated_at`) VALUES
(1, 'Home', 'home', 'Home | Hijaabster', 'hijaab,scarf,abaya', 'Hijaabster home page.', '2025-12-23 17:50:05', '2025-12-23 17:50:05');
INSERT INTO `cms_pages` (`id`, `page_title`, `page_slug`, `page_meta_title`, `page_meta_keyword`, `page_meta_description`, `created_at`, `updated_at`) VALUES
(2, 'About Us', 'about-us', 'about-us', 'about-us', 'about-us', '2026-01-02 00:19:55', '2026-01-02 00:19:55');
INSERT INTO `cms_pages` (`id`, `page_title`, `page_slug`, `page_meta_title`, `page_meta_keyword`, `page_meta_description`, `created_at`, `updated_at`) VALUES
(3, 'Promotions', 'promotions', 'promotions', 'promotions', 'promotions', '2026-01-02 19:12:31', '2026-01-02 19:12:31');
INSERT INTO `cms_pages` (`id`, `page_title`, `page_slug`, `page_meta_title`, `page_meta_keyword`, `page_meta_description`, `created_at`, `updated_at`) VALUES
(4, 'Category', 'category', 'category', 'category', 'category', '2026-01-05 17:40:39', '2026-01-05 17:40:39'),
(5, 'Shop', 'shop', 'shop', 'shop', 'shop', '2026-01-05 23:55:01', '2026-01-05 23:55:01'),
(6, 'Contact Us', 'contact-us', 'contact-us', 'contact-us', 'contact-us', '2026-01-06 18:56:33', '2026-01-06 18:56:33'),
(7, 'Terms and Conditions', 'terms-and-conditions', 'terms-and-conditions', 'terms-and-conditions', 'terms-and-conditions', '2026-01-06 21:11:02', '2026-01-06 21:11:02'),
(8, 'Privacy Policy', 'privacy-policy', 'privacy-policy', 'privacy-policy', 'privacy-policy', '2026-01-07 00:22:05', '2026-01-07 00:22:05'),
(9, 'Accessibility', 'accessibility', 'accessibility', 'accessibility', 'accessibility', '2026-01-08 01:24:20', '2026-01-08 01:24:20'),
(10, 'Cart', 'cart', 'cart', 'cart', 'cart', '2026-01-09 18:37:26', '2026-01-09 18:37:26'),
(11, 'Checkout', 'checkout', 'checkout', 'checkout', 'checkout', '2026-01-09 19:01:41', '2026-01-09 19:01:41');





INSERT INTO `coupons` (`id`, `code`, `name`, `description`, `discount_type`, `discount_value`, `minimum_purchase`, `maximum_discount`, `usage_limit`, `used_count`, `valid_from`, `valid_until`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Lorem', 'ipsum', 'lorem ipsum', 'percentage', '10.00', '5.00', '5000.00', 5, 0, '2026-01-01 18:19:00', '2026-01-22 18:22:00', 1, '2026-01-09 00:18:43', '2026-01-09 00:19:15');




INSERT INTO `general_settings` (`id`, `key`, `type`, `value`, `created_at`, `updated_at`) VALUES
(1, 'Address', 'text', '145 Mercer Street, Suite 4C, New York, NY 10012', '2026-01-06 20:51:06', '2026-01-06 20:51:17');
INSERT INTO `general_settings` (`id`, `key`, `type`, `value`, `created_at`, `updated_at`) VALUES
(2, 'Hours', 'text', 'Monday - Saturday | 10am to 7pm ET', '2026-01-06 20:51:06', '2026-01-06 20:51:17');
INSERT INTO `general_settings` (`id`, `key`, `type`, `value`, `created_at`, `updated_at`) VALUES
(3, 'Email', 'text', '@scarf.collective', '2026-01-06 20:51:06', '2026-01-06 20:51:17');
INSERT INTO `general_settings` (`id`, `key`, `type`, `value`, `created_at`, `updated_at`) VALUES
(4, 'Phone', 'text', '+1 (212) 555-1045', '2026-01-06 20:51:31', '2026-01-06 20:51:40'),
(5, 'Footer Content', 'text', 'Elevate every look with thoughtfully designed scarves crafted for comfort and effortless style.', '2026-01-08 01:41:07', '2026-01-08 01:41:09'),
(6, 'Newsletter', 'text', 'Join our community for styling inspiration, curated drops, and early access to exclusive offers.', '2026-01-09 19:04:08', '2026-01-09 19:04:11');





INSERT INTO `media` (`id`, `path`, `media_type`, `mediaable_type`, `mediaable_id`, `is_featured`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, '/storage/brands/bE2o7LSg8eNHDVG4SqKtv80ywjyUQSzHweOyI3Iw.jpg', 'image', 'App\\Models\\Brand', 1, 1, NULL, '2026-01-08 16:39:48', '2026-01-08 16:39:48');
INSERT INTO `media` (`id`, `path`, `media_type`, `mediaable_type`, `mediaable_id`, `is_featured`, `deleted_at`, `created_at`, `updated_at`) VALUES
(2, '/storage/brands/RelEggGDgr8EVcO0NYZN2v1bLqzQtNN7GeZfi6l5.jpg', 'image', 'App\\Models\\Brand', 2, 1, NULL, '2026-01-08 16:42:03', '2026-01-08 16:42:03');
INSERT INTO `media` (`id`, `path`, `media_type`, `mediaable_type`, `mediaable_id`, `is_featured`, `deleted_at`, `created_at`, `updated_at`) VALUES
(3, '/storage/categories/aLfU95EPZj6cuRCIe2Z0sak1vUYrrTbcdoitgl71.jpg', 'image', 'App\\Models\\Category', 1, 1, NULL, '2026-01-08 16:52:58', '2026-01-08 16:52:58');
INSERT INTO `media` (`id`, `path`, `media_type`, `mediaable_type`, `mediaable_id`, `is_featured`, `deleted_at`, `created_at`, `updated_at`) VALUES
(4, '/storage/categories/X10QgbvzTWj4OWgvyTDvn7DfNwrYICzsdJPbPWpJ.jpg', 'image', 'App\\Models\\Category', 1, 1, NULL, '2026-01-08 17:28:26', '2026-01-08 17:28:26'),
(8, '/storage/categories/rIGXDzqfpcTO9ZS2TncikbHJnQkeg5iW5Zuzogfz.jpg', 'image', 'App\\Models\\Category', 2, 1, NULL, '2026-01-08 17:40:26', '2026-01-08 17:40:26'),
(9, '/storage/categories/C20E5aNTPGvQWrDnBG5jB2yDCmz2wWKVU50t8FtO.jpg', 'image', 'App\\Models\\Category', 2, 0, NULL, '2026-01-08 17:40:26', '2026-01-08 17:40:26'),
(11, '/storage/categories/4iAhzLhsyW0uGKITXX8T5Eo3VHRQRIY6BCLMjUVu.jpg', 'image', 'App\\Models\\Category', 3, 1, NULL, '2026-01-08 17:41:30', '2026-01-08 17:51:49'),
(12, '/storage/categories/DybFVopIWaCIsLlOdUp6rmgJjjz51HqxFRVsgDxB.jpg', 'image', 'App\\Models\\Category', 3, 0, NULL, '2026-01-08 17:44:19', '2026-01-08 17:51:49'),
(13, '/storage/categories/EQ096JF3SHGsqoN2D9juYcnQp7ushugqXG01DcA0.jpg', 'image', 'App\\Models\\Category', 4, 1, NULL, '2026-01-08 17:52:23', '2026-01-08 17:52:23'),
(14, '/storage/categories/VJpStyBMOikaqTUBtb4tpK8yH1FZJj2g6UuyJzrw.jpg', 'image', 'App\\Models\\Category', 4, 0, NULL, '2026-01-08 17:52:23', '2026-01-08 17:52:23'),
(15, '/storage/categories/4b6I3Jcdxg8v6ztAZf8t9jIc1MH8ggQsLOy1TH6V.jpg', 'image', 'App\\Models\\Category', 5, 1, NULL, '2026-01-08 17:52:58', '2026-01-08 17:52:58'),
(16, '/storage/categories/wf361KYX1lJeu3ZLDa3KjKq7IblLI8sW8soBwzMH.jpg', 'image', 'App\\Models\\Category', 5, 0, NULL, '2026-01-08 17:52:58', '2026-01-08 17:52:58'),
(17, '/storage/categories/IA5W0ftueCdMA89kWN5Ms63gi5fFjCWGZMQKWkwQ.jpg', 'image', 'App\\Models\\Category', 6, 1, NULL, '2026-01-08 17:53:27', '2026-01-08 17:53:27'),
(18, '/storage/categories/KfxUxTluOhlruEvHpGtXGtEXc3j5qPhPMQnnyxVf.jpg', 'image', 'App\\Models\\Category', 6, 0, NULL, '2026-01-08 17:53:27', '2026-01-08 17:53:27'),
(19, '/storage/products/hiabQxKTFz6Kgko3mz2zKxgmmBYIDBywrAs3QTG8.jpg', 'image', 'App\\Models\\Product', 1, 1, NULL, '2026-01-08 18:23:57', '2026-01-08 19:25:41'),
(20, '/storage/products/UmhrAZH07L84EuqIW4REMKdqm9bzb9mAHyfveIAG.jpg', 'image', 'App\\Models\\Product', 1, 0, NULL, '2026-01-08 18:23:57', '2026-01-08 19:25:41'),
(23, '/storage/products/UufuWzU7QDmI4BfkLDvOUfuIqg2RJMLQhQAFeZSN.jpg', 'image', 'App\\Models\\Product', 2, 1, NULL, '2026-01-08 22:04:41', '2026-01-08 22:04:41'),
(24, '/storage/products/8hhboHgsgPS39NZ0Rurl3ZHTKPqVuQGgR6jzV21Q.jpg', 'image', 'App\\Models\\Product', 2, 0, NULL, '2026-01-08 22:04:41', '2026-01-08 22:04:41'),
(25, '/storage/products/H8VkrJPW9e4zjgeh2MfvOjcqXX3SzfcG3tr24DaP.jpg', 'image', 'App\\Models\\Product', 3, 1, NULL, '2026-01-08 22:13:13', '2026-01-08 22:13:53'),
(26, '/storage/products/Vh2WqFaRvdqWXfDHJlFbBDFkWZQ1dB5kzrUQSzL2.jpg', 'image', 'App\\Models\\Product', 3, 0, NULL, '2026-01-08 22:13:13', '2026-01-08 22:13:53');

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '0001_01_01_000001_create_cache_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(3, '0001_01_01_000002_create_jobs_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(4, '2025_03_07_174825_create_roles_table', 1),
(5, '2025_03_11_005549_create_smtp_settings_table', 1),
(6, '2025_03_11_005632_create_settings_table', 1),
(7, '2025_03_12_174255_create_general_settings_table', 1),
(8, '2025_03_14_165559_create_cms_pages_table', 1),
(9, '2025_03_14_165622_create_cms_page_sections_table', 1),
(10, '2025_03_14_165724_create_cms_page_section_fields_table', 1),
(11, '2025_03_20_004833_create_newsletters_table', 1),
(12, '2025_03_20_181115_add_soft_delete_column_in_news_letter', 1),
(13, '2025_03_24_225725_create_contact_inquiries_table', 1),
(14, '2025_03_26_185938_create_tags_table', 1),
(15, '2025_03_26_185947_create_blogs_table', 1),
(16, '2025_03_26_185955_create_media_table', 1),
(17, '2025_03_26_191914_create_blog_tag_table', 1),
(18, '2025_03_28_163339_create_comments_table', 1),
(19, '2025_04_28_194922_create_categories_table', 1),
(20, '2025_04_28_194931_create_brands_table', 1),
(21, '2025_04_30_231701_create_product_attributes_table', 1),
(22, '2025_04_30_231719_create_product_attribute_options_table', 1),
(23, '2025_04_30_231739_create_products_table', 1),
(24, '2025_04_30_231747_create_product_variations_table', 1),
(25, '2025_11_14_000001_add_amazon_link_to_products_table', 1),
(26, '2025_11_21_000002_update_amazon_link_length_on_products_table', 1),
(27, '2025_12_22_212600_add_company_and_service_to_contact_inquiries_table', 2),
(28, '2026_01_08_183118_make_brand_id_nullable_in_products_table', 3),
(29, '2026_01_08_221835_create_coupons_table', 4),
(30, '2026_01_08_222115_add_coupon_id_to_products_table', 4);











INSERT INTO `products` (`id`, `name`, `slug`, `description`, `base_price`, `stock`, `has_variations`, `category_id`, `brand_id`, `coupon_id`, `has_discount`, `discount_type`, `discount_value`, `created_by`, `featured`, `new`, `top`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Silk Radiance Hijab', 'silk-radiance-hijab', '<p>Fluid satin-finish silk with hand-rolled hems for evening elegance.</p>', '68.00', 20, 0, 4, NULL, NULL, 1, 'percentage', '5.00', 1, 0, 0, 0, 1, '2026-01-08 18:23:57', '2026-01-08 19:25:41');
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `base_price`, `stock`, `has_variations`, `category_id`, `brand_id`, `coupon_id`, `has_discount`, `discount_type`, `discount_value`, `created_by`, `featured`, `new`, `top`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Cloud Cotton Veil', 'cloud-cotton-veil', '<p>Featherweight cotton blend with a whisper-soft hand feel.</p>', '42.00', 0, 0, 4, NULL, NULL, 0, 'percentage', '0.00', 1, 0, 0, 0, 1, '2026-01-08 22:04:41', '2026-01-08 22:04:41');
INSERT INTO `products` (`id`, `name`, `slug`, `description`, `base_price`, `stock`, `has_variations`, `category_id`, `brand_id`, `coupon_id`, `has_discount`, `discount_type`, `discount_value`, `created_by`, `featured`, `new`, `top`, `status`, `created_at`, `updated_at`) VALUES
(3, 'Nordic Wool Wrap', 'nordic-wool-wrap', '<p>Plush merino weave designed to lock in warmth without bulk.</p>', '50.00', 0, 0, 2, NULL, NULL, 0, 'percentage', '0.00', 1, 0, 0, 0, 1, '2026-01-08 22:13:13', '2026-01-08 22:13:53');

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'user', '2025-12-22 20:52:42', '2025-12-22 20:52:42');
INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(2, 'admin', '2025-12-22 20:52:42', '2025-12-22 20:52:42');










INSERT INTO `users` (`id`, `name`, `email`, `image`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'Admin', 'admin@mail.com', NULL, NULL, '$2y$12$dbReh6slVgiVj/Awrh37OeJUNXrIXKwUGZ5A.t/fTGpXogaSEIvXG', NULL, '2025-12-22 20:52:43', '2025-12-22 20:52:43', 2);



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;