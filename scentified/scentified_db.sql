-- =========================================================
-- SCENTIFIED DATABASE
-- =========================================================

CREATE DATABASE IF NOT EXISTS `scentified_db`;

USE `scentified_db`;


-- =========================================================
-- DATABASE SETTINGS
-- =========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

SET time_zone = "+00:00";

SET NAMES utf8mb4;

START TRANSACTION;


-- =========================================================
-- DROP EXISTING TABLES
-- =========================================================

DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `contact_messages`;
DROP TABLE IF EXISTS `users`;


-- =========================================================
-- 1. USERS
-- =========================================================

CREATE TABLE `users` (
    `user_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `username` VARCHAR(50) NOT NULL,

    `first_name` VARCHAR(50) NOT NULL,

    `last_name` VARCHAR(50) NOT NULL,

    `email` VARCHAR(100) NOT NULL,

    `password_hash` VARCHAR(255) NOT NULL,

    `mobile_number` VARCHAR(20) DEFAULT NULL,

    `address` TEXT DEFAULT NULL,

    `date_of_birth` DATE DEFAULT NULL,

    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`user_id`),

    UNIQUE KEY `username` (`username`),

    UNIQUE KEY `email` (`email`)
    
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- 2. PRODUCTS
-- =========================================================

CREATE TABLE `products` (
    `product_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `name` VARCHAR(100) NOT NULL,

    `description` TEXT NOT NULL,

    `price` DECIMAL(10,2) NOT NULL,

    `img_url` VARCHAR(255) NOT NULL,

    `stock_qty` INT(11) NOT NULL DEFAULT 0,

    PRIMARY KEY (`product_id`)
    
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- 3. ORDERS
-- =========================================================

CREATE TABLE `orders` (
    `order_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `user_id` INT(11) UNSIGNED NOT NULL,

    `total_amount` DECIMAL(10,2) NOT NULL,

    `shipping_fee` DECIMAL(10,2) NOT NULL,

    `payment_method` VARCHAR(50) NOT NULL,

    `order_status` VARCHAR(50) DEFAULT 'Pending',

    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`order_id`),

    KEY `user_id` (`user_id`),

    CONSTRAINT `orders_ibfk_1`
        FOREIGN KEY (`user_id`)
        REFERENCES `users` (`user_id`)
        
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- 4. ORDER ITEMS
-- =========================================================

CREATE TABLE `order_items` (
    `order_item_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,

    `order_id` INT(11) UNSIGNED NOT NULL,

    `product_id` INT(11) UNSIGNED NOT NULL,

    `quantity` INT(11) NOT NULL,

    `price` DECIMAL(10,2) NOT NULL,

    PRIMARY KEY (`order_item_id`),

    KEY `order_id` (`order_id`),

    KEY `product_id` (`product_id`),

    CONSTRAINT `order_items_ibfk_1`
        FOREIGN KEY (`order_id`)
        REFERENCES `orders` (`order_id`),

    CONSTRAINT `order_items_ibfk_2`
        FOREIGN KEY (`product_id`)
        REFERENCES `products` (`product_id`)
        
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- 5. CONTACT MESSAGES
-- =========================================================

CREATE TABLE `contact_messages` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,

    `name` VARCHAR(100) NOT NULL,

    `email` VARCHAR(100) NOT NULL,

    `subject` VARCHAR(150) DEFAULT NULL,

    `message` TEXT NOT NULL,

    `submitted_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`)
    
) ENGINE=InnoDB
DEFAULT CHARSET=utf8mb4
COLLATE=utf8mb4_general_ci;


-- =========================================================
-- SAMPLE USERS
-- =========================================================

INSERT INTO `users`
(
    `user_id`,
    `username`,
    `first_name`,
    `last_name`,
    `email`,
    `password_hash`,
    `mobile_number`,
    `address`,
    `date_of_birth`,
    `created_at`
)
VALUES

(
    1,
    'John',
    'John',
    'JohnSmith',
    'johnsmith@gmail.com',
    '$2y$10$At4S2lkMVatdHlHHl9a6kuJUh4sRh4UGqL.VFfQcgSdx7vGRohZSu',
    '11',
    '',
    '2006-08-28',
    '2025-11-29 05:49:01'
),

(
    2,
    '',
    '',
    '',
    'admin@scentified.com',
    '$2y$10$0BiPULQO0OVObNSSGljeq.q8HOF60qGgOigqwlHol4YsN1jUUraN6',
    NULL,
    NULL,
    NULL,
    '2025-12-02 03:45:12'
),

(
    3,
    'John',
    'John',
    'Doe',
    'john@gmail.com',
    '$2y$10$1n055l.UJib83EYEgR0/o./abckEoYfLqEyR2ngi0YvmPZ96fhkMW',
    '0987654321',
    'N/A',
    '2006-08-28',
    '2025-12-02 12:36:24'
);


-- =========================================================
-- SAMPLE PRODUCTS
-- =========================================================

INSERT INTO `products`
(
    `product_id`,
    `name`,
    `description`,
    `price`,
    `img_url`,
    `stock_qty`
)
VALUES

(
    1,
    'Aura Mystique',
    'An enchanting blend of rare florals and precious spices',
    750.00,
    '2.png',
    50
),

(
    2,
    'Midnight Essence',
    'Deep, mysterious notes of dark amber and oud',
    780.00,
    '3.png',
    45
),

(
    3,
    'Citrus Whisper',
    'Bright citrus with delicate floral undertones',
    700.00,
    '4.png',
    60
),

(
    4,
    'Velvet Bloom',
    'Luxurious rose and jasmine with creamy musk',
    720.00,
    '5.png',
    40
),

(
    5,
    'Ocean Serenity',
    'Fresh aquatic essence with subtle woody notes',
    760.00,
    '6.png',
    55
);


-- =========================================================
-- SAMPLE ORDERS
-- =========================================================

INSERT INTO `orders`
(
    `order_id`,
    `user_id`,
    `total_amount`,
    `shipping_fee`,
    `payment_method`,
    `order_status`,
    `created_at`
)
VALUES

(1, 1, 850.00, 100.00, 'cod', 'Delivered', '2025-12-02 02:58:59'),

(2, 1, 880.00, 100.00, 'Cash on Delivery', 'Delivered', '2025-12-02 03:11:53'),

(3, 1, 880.00, 100.00, 'Cash on Delivery', 'Delivered', '2025-12-02 03:15:42'),

(4, 1, 850.00, 100.00, 'Cash on Delivery', 'Delivered', '2025-12-02 03:17:45'),

(5, 1, 1630.00, 100.00, 'Cash on Delivery', 'Delivered', '2025-12-02 03:34:17'),

(6, 1, 4480.00, 100.00, 'Cash on Delivery', 'Delivered', '2025-12-02 09:57:18'),

(7, 1, 6930.00, 100.00, 'Cash on Delivery', 'Delivered', '2025-12-02 10:41:26'),

(8, 1, 3110.00, 100.00, 'Cash on Delivery', 'Pending', '2025-12-02 12:23:01'),

(9, 3, 850.00, 100.00, 'Cash on Delivery', 'Pending', '2025-12-02 12:38:00'),

(10, 3, 850.00, 100.00, 'Bank Transfer', 'Pending', '2025-12-02 12:42:16'),

(11, 3, 880.00, 100.00, 'Bank Transfer', 'Delivered', '2025-12-02 12:44:06');


-- =========================================================
-- SAMPLE ORDER ITEMS
-- =========================================================

INSERT INTO `order_items`
(
    `order_item_id`,
    `order_id`,
    `product_id`,
    `quantity`,
    `price`
)
VALUES

(1, 1, 1, 1, 750.00),

(2, 3, 2, 1, 780.00),

(3, 4, 1, 1, 750.00),

(4, 5, 1, 1, 750.00),

(5, 5, 2, 1, 780.00),

(6, 6, 1, 2, 750.00),

(7, 6, 5, 1, 760.00),

(8, 6, 4, 1, 720.00),

(9, 6, 3, 2, 700.00),

(10, 7, 1, 1, 750.00),

(11, 7, 5, 8, 760.00),

(12, 8, 2, 1, 780.00),

(13, 8, 1, 1, 750.00),

(14, 8, 5, 1, 760.00),

(15, 8, 4, 1, 720.00),

(16, 11, 2, 1, 780.00);


-- =========================================================
-- AUTO_INCREMENT VALUES
-- =========================================================

ALTER TABLE `users`
    AUTO_INCREMENT = 4;

ALTER TABLE `products`
    AUTO_INCREMENT = 6;

ALTER TABLE `orders`
    AUTO_INCREMENT = 12;

ALTER TABLE `order_items`
    AUTO_INCREMENT = 17;


-- =========================================================
-- FINISH
-- =========================================================

COMMIT;