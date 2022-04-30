SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE IF NOT EXISTS `pixsalle`;
USE `pixsalle`;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users`
(
    `id`        INT                                                     NOT NULL AUTO_INCREMENT,
    `email`     VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
    `password`  VARCHAR(255)                                            NOT NULL,
    `createdAt` DATETIME                                                NOT NULL,
    `updatedAt` DATETIME                                                NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- new tables

ALTER TABLE `users`
    ADD `membership`    enum('cool', 'active')  DEFAULT 'cool';
ALTER TABLE `users`
    ADD `wallet`        FLOAT                   DEFAULT 0;
ALTER TABLE `users`
    ADD `phone`         VARCHAR(255);
ALTER TABLE `users`
    ADD `username`      VARCHAR(255)            NOT NULL UNIQUE;

CREATE TABLE `portfolios`
(
    `name`      VARCHAR(255),
    `user_id`   INT,
    PRIMARY KEY (`name`),
    FOREIGN KEY (`user_id`) REFERENCES users(`id`)
);

CREATE TABLE `albums`
(
    `name`              VARCHAR(255),
    `portfolio_name`    VARCHAR(255),
    PRIMARY KEY (`name`, `portfolio_name`),
    FOREIGN KEY (`portfolio_name`) REFERENCES portfolios(`name`)
);

CREATE TABLE `photos`
(
    `uuid`              VARCHAR(255),
    `extension`         enum('png', 'jpg'),
    PRIMARY KEY (`uuid`)
);

CREATE TABLE `albumphoto`
(
    `album_name`        VARCHAR(255),
    `portfolio_name`    VARCHAR(255),
    `photo_id`          VARCHAR(255),
    PRIMARY KEY (`album_name`, `portfolio_name`, `photo_id`),
    FOREIGN KEY (`album_name`) REFERENCES albums(`name`),
    FOREIGN KEY (`portfolio_name`) REFERENCES portfolios(`name`),
    FOREIGN KEY (`photo_id`) REFERENCES photos(`uuid`)
);

ALTER TABLE `users`
    ADD `profile_picture` VARCHAR(255) REFERENCES photos(`uuid`);