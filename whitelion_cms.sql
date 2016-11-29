-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Час створення: Вер 15 2016 р., 20:23
-- Версія сервера: 5.7.9
-- Версія PHP: 7.0.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `whitelion.cms`
--

-- --------------------------------------------------------

--
-- Структура таблиці `wl_aliases`
--

DROP TABLE IF EXISTS `wl_aliases`;
CREATE TABLE IF NOT EXISTS `wl_aliases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` text NOT NULL COMMENT 'основне посилання',
  `service` int(11) DEFAULT '0',
  `table` text,
  `admin_ico` text,
  `admin_order` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_aliases`
--

INSERT INTO `wl_aliases` (`id`, `alias`, `service`, `table`, `admin_ico`, `admin_order`) VALUES
(1, 'main', 0, NULL, NULL, NULL),
(2, 'search', 0, NULL, NULL, NULL),
(3, 'profile', 0, NULL, NULL, NULL),
(4, 'login', 0, NULL, NULL, NULL),
(5, 'signup', 0, NULL, NULL, NULL),
(6, 'reset', 0, NULL, NULL, NULL),
(7, 'subscribe', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_aliases_cooperation`
--

DROP TABLE IF EXISTS `wl_aliases_cooperation`;
CREATE TABLE IF NOT EXISTS `wl_aliases_cooperation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias1` int(11) NOT NULL,
  `alias2` int(11) NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `alias1` (`alias1`),
  KEY `alias2` (`alias2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_audio`
--

DROP TABLE IF EXISTS `wl_audio`;
CREATE TABLE IF NOT EXISTS `wl_audio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `name` text NOT NULL,
  `text` text NOT NULL,
  `extension` text NOT NULL,
  `author` int(11) NOT NULL,
  `date_add` int(11) NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`,`content`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_fields`
--

DROP TABLE IF EXISTS `wl_fields`;
CREATE TABLE IF NOT EXISTS `wl_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form` int(11) NOT NULL,
  `name` text NOT NULL,
  `position` int(11) DEFAULT '0',
  `input_type` int(11) NOT NULL,
  `required` tinyint(1) DEFAULT '0',
  `title` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_fields_options`
--

DROP TABLE IF EXISTS `wl_fields_options`;
CREATE TABLE IF NOT EXISTS `wl_fields_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `field` int(11) NOT NULL,
  `value` text,
  `title` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_forms`
--

DROP TABLE IF EXISTS `wl_forms`;
CREATE TABLE IF NOT EXISTS `wl_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `captcha` tinyint(1) DEFAULT '0',
  `help` text,
  `table` text,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-GET, 2-POST',
  `type_data` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-fields, 2-values',
  `send_mail` tinyint(1) DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL,
  `success_data` text,
  `send_sms` tinyint(1) NOT NULL DEFAULT '0',
  `sms_text` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_images`
--

DROP TABLE IF EXISTS `wl_images`;
CREATE TABLE IF NOT EXISTS `wl_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `file_name` text,
  `title` text,
  `author` int(11) NOT NULL,
  `date_add` int(11) NOT NULL,
  `main` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `content` (`content`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_images_sizes`
--

DROP TABLE IF EXISTS `wl_images_sizes`;
CREATE TABLE IF NOT EXISTS `wl_images_sizes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `active` tinyint(1) DEFAULT NULL,
  `name` text,
  `prefix` tinytext,
  `type` tinyint(1) NOT NULL COMMENT '1 resize, 2 preview',
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_images_sizes`
--

INSERT INTO `wl_images_sizes` (`id`, `alias`, `active`, `name`, `prefix`, `type`, `width`, `height`) VALUES
(1, 0, 1, 'Значення по замовчуванню. Оригінал', NULL, 1, 1500, 1500),
(2, 0, 1, 'Значення по замовчуванню. Панель керування', 'admin', 2, 150, 150),
(3, 0, 1, 'Значення по замовчуванню. Header для соц. мереж', 'header', 2, 600, 315);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_input_types`
--

DROP TABLE IF EXISTS `wl_input_types`;
CREATE TABLE IF NOT EXISTS `wl_input_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `options` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_input_types`
--

INSERT INTO `wl_input_types` (`id`, `name`, `options`) VALUES
(1, 'text', 0),
(2, 'email', 0),
(3, 'textarea', 0),
(4, 'photo', 0),
(5, 'date', 0),
(6, 'time', 0),
(7, 'datetime', 0),
(8, 'checkbox', 1),
(9, 'radio', 1),
(10, 'select', 1),
(11, 'number', 0),
(12, 'url', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_language_values`
--

DROP TABLE IF EXISTS `wl_language_values`;
CREATE TABLE IF NOT EXISTS `wl_language_values` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_language_words`
--

DROP TABLE IF EXISTS `wl_language_words`;
CREATE TABLE IF NOT EXISTS `wl_language_words` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `word` text,
  `alias` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_active`
--

DROP TABLE IF EXISTS `wl_mail_active`;
CREATE TABLE IF NOT EXISTS `wl_mail_active` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) NOT NULL,
  `form` int(11) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_history`
--

DROP TABLE IF EXISTS `wl_mail_history`;
CREATE TABLE IF NOT EXISTS `wl_mail_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) NOT NULL,
  `date` int(11) NOT NULL,
  `title` text,
  `text` text,
  `from` text,
  `to` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_templates`
--

DROP TABLE IF EXISTS `wl_mail_templates`;
CREATE TABLE IF NOT EXISTS `wl_mail_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `from` text,
  `to` text,
  `multilanguage` tinyint(1) DEFAULT NULL,
  `savetohistory` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_mail_templats_data`
--

DROP TABLE IF EXISTS `wl_mail_templats_data`;
CREATE TABLE IF NOT EXISTS `wl_mail_templats_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `title` text,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_ntkd`
--

DROP TABLE IF EXISTS `wl_ntkd`;
CREATE TABLE IF NOT EXISTS `wl_ntkd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `language` varchar(2) DEFAULT NULL,
  `name` text,
  `title` text,
  `description` text,
  `keywords` text,
  `text` text,
  `list` text,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `content` (`content`),
  KEY `language` (`language`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_ntkd`
--

INSERT INTO `wl_ntkd` (`id`, `alias`, `content`, `language`, `name`, `title`, `description`, `keywords`, `text`, `list`) VALUES
(1, 1, 0, NULL, 'whitelion.cms', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_options`
--

DROP TABLE IF EXISTS `wl_options`;
CREATE TABLE IF NOT EXISTS `wl_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` int(11) NOT NULL,
  `alias` int(11) NOT NULL,
  `name` text NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_options`
--

INSERT INTO `wl_options` (`id`, `service`, `alias`, `name`, `value`) VALUES
(1, 0, 0, 'paginator_per_page', '20'),
(2, 1, 0, 'folder', 'static_page'),
(32, 3, 0, 'useGroups', '1'),
(33, 3, 0, 'articleMultiGroup', '0'),
(34, 3, 0, 'useAvailability', '0'),
(35, 3, 0, 'folder', 'library'),
(36, 3, 0, 'articleOrder', 'position DESC'),
(37, 3, 0, 'groupOrder', 'position ASC');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_services`
--

DROP TABLE IF EXISTS `wl_services`;
CREATE TABLE IF NOT EXISTS `wl_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT 'службова назва (папки)',
  `title` text NOT NULL COMMENT 'публічна назва',
  `description` text NOT NULL,
  `table` text NOT NULL COMMENT 'службова таблиця',
  `group` tinytext NOT NULL,
  `multi_alias` tinyint(1) NOT NULL,
  `version` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_services`
--

INSERT INTO `wl_services` (`id`, `name`, `title`, `description`, `table`, `group`, `multi_alias`, `version`) VALUES
(1, 'static_pages', 'Статичні сторінки', '', 's_static_page', 'page', 1, '2'),
(3, 'library', 'Бібліотека статей (Блог)', 'Бібліотека статей із підтримкою категорій. Мультимовна.', 's_library', 'page', 1, '2.5');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_sitemap`
--

DROP TABLE IF EXISTS `wl_sitemap`;
CREATE TABLE IF NOT EXISTS `wl_sitemap` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link` text NOT NULL,
  `alias` smallint(6) DEFAULT NULL,
  `content` int(11) DEFAULT NULL,
  `language` char(2) DEFAULT NULL,
  `code` smallint(5) UNSIGNED DEFAULT NULL COMMENT '200 cache ok; 201 no cache, 301 redirect, 404',
  `data` blob,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`,`content`,`language`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_sitemap`
--

INSERT INTO `wl_sitemap` (`id`, `link`, `alias`, `content`, `language`, `code`, `data`, `time`) VALUES
(1, 'main', 1, 0, NULL, 200, NULL, 1471428532),
(2, 'search', 2, 0, NULL, 201, NULL, 0),
(3, 'profile', 3, 0, NULL, 201, NULL, 0),
(4, 'login', 4, 0, NULL, 201, NULL, 0),
(5, 'signup', 5, 0, NULL, 200, NULL, 0),
(6, 'reset', 6, 0, NULL, 201, NULL, 0),
(7, 'subscribe', 0, 0, NULL, 200, NULL, 0),
(8, 'logout', 0, 0, NULL, 201, NULL, 0),
(10, 'audio/9/0/dagadana-tango-.mp3', 0, 0, '', 200, '', 1473781671),
(11, 'login/show', 0, 0, '', 200, '', 1473839202),
(14, 'adminadmin/about', 0, 0, '', 200, '', 1473875522),
(15, 'reset/go', 0, 0, '', 200, '', 1473946368),
(16, 'profile/edit', 0, 0, '', 201, '', 1473954190);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_statistic_pages`
--

DROP TABLE IF EXISTS `wl_statistic_pages`;
CREATE TABLE IF NOT EXISTS `wl_statistic_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) DEFAULT NULL,
  `content` int(11) DEFAULT NULL,
  `language` varchar(2) DEFAULT NULL,
  `day` int(10) UNSIGNED NOT NULL,
  `unique` int(10) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`),
  KEY `content` (`content`),
  KEY `language` (`language`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_statistic_pages`
--

INSERT INTO `wl_statistic_pages` (`id`, `alias`, `content`, `language`, `day`, `unique`, `views`) VALUES
(1, 1, 0, NULL, 1471910400, 1, 2),
(2, 5, 0, NULL, 1471910400, 1, 1),
(3, 0, 8, NULL, 1471910400, 1, 1),
(4, 4, 0, NULL, 1471910400, 1, 1),
(5, 1, 0, NULL, 1473897600, 3, 4),
(6, 4, 0, NULL, 1473897600, 3, 12),
(7, 0, 8, NULL, 1473897600, 1, 1),
(8, 6, 0, NULL, 1473897600, 1, 3),
(9, 0, 15, NULL, 1473897600, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_statistic_views`
--

DROP TABLE IF EXISTS `wl_statistic_views`;
CREATE TABLE IF NOT EXISTS `wl_statistic_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `day` int(10) UNSIGNED NOT NULL,
  `cookie` int(10) UNSIGNED NOT NULL,
  `unique` int(10) UNSIGNED NOT NULL,
  `views` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `day` (`day`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_statistic_views`
--

INSERT INTO `wl_statistic_views` (`id`, `day`, `cookie`, `unique`, `views`) VALUES
(1, 1471910400, 0, 1, 5),
(2, 1473897600, 1, 4, 21);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_users`
--

DROP TABLE IF EXISTS `wl_users`;
CREATE TABLE IF NOT EXISTS `wl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` text,
  `email` text NOT NULL,
  `name` text,
  `photo` text,
  `type` smallint(2) NOT NULL DEFAULT '4',
  `status` tinyint(1) NOT NULL DEFAULT '2',
  `registered` int(11) DEFAULT '0',
  `last_login` int(11) NOT NULL,
  `auth_id` text,
  `password` text,
  `reset_key` text,
  `reset_expires` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_users`
--

INSERT INTO `wl_users` (`id`, `alias`, `email`, `name`, `photo`, `type`, `status`, `registered`, `last_login`, `auth_id`, `password`, `reset_key`, `reset_expires`) VALUES
(1, 'admin', 'levso7@gmail.com', 'admin', NULL, 1, 1, 1469650761, 1473950874, 'ad0d49c62c26c8025f579820ac07d780', 'c0e5c5c47f28b4d132bc9f894c192cfc27716f3d', 'd86ea39642f9766f2ad27a635bff26a8', 1473953508);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_info`
--

DROP TABLE IF EXISTS `wl_user_info`;
CREATE TABLE IF NOT EXISTS `wl_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `field` text NOT NULL,
  `value` text,
  `date` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_permissions`
--

DROP TABLE IF EXISTS `wl_user_permissions`;
CREATE TABLE IF NOT EXISTS `wl_user_permissions` (
  `user` int(11) NOT NULL,
  `permission` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register`
--

DROP TABLE IF EXISTS `wl_user_register`;
CREATE TABLE IF NOT EXISTS `wl_user_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `do` tinyint(4) NOT NULL,
  `user` int(11) NOT NULL,
  `additionally` text,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_register`
--

INSERT INTO `wl_user_register` (`id`, `date`, `do`, `user`, `additionally`) VALUES
(1, 1469650761, 1, 1, NULL),
(2, 1473453532, 13, 1, '1. static_pages (2)'),
(3, 1473453611, 14, 1, '1. static_pages (2)'),
(4, 1473453639, 13, 1, '2. static_pages (2)'),
(5, 1473453661, 14, 1, '2. static_pages (2)'),
(6, 1473453664, 13, 1, '3. static_pages (2)'),
(7, 1473454064, 14, 1, '3. static_pages (2)'),
(8, 1473454185, 13, 1, '1. static_pages (2)'),
(9, 1473519819, 11, 1, 'about (8)'),
(10, 1473521215, 11, 1, 'about2 (9)'),
(11, 1473844120, 12, 1, '9. about2. static_pages (1)'),
(12, 1473845296, 11, 1, 'contact (10)'),
(13, 1473848427, 12, 1, '10. contact. static_pages (1)'),
(14, 1473867259, 12, 1, '8. about. static_pages (1)'),
(15, 1473867272, 11, 1, 'about (11)'),
(16, 1473946309, 3, 1, ''),
(17, 1473946380, 4, 1, '0742dd44efe0250055472606d9951c788ee2e62d'),
(18, 1473953194, 13, 1, '2. shopshowcase (2.2)'),
(19, 1473953266, 11, 1, 'shop (12)'),
(20, 1473954265, 13, 1, '3. library (2.5)'),
(21, 1473954756, 11, 1, 'library (13)'),
(22, 1473954935, 12, 1, '12. shop. shopshowcase (2)'),
(23, 1473954938, 14, 1, '2. shopshowcase (2.2)'),
(24, 1473969183, 12, 1, '13. library. library (3)'),
(25, 1473969283, 12, 1, '11. about. static_pages (1)'),
(26, 1473969457, 11, 1, 'about (14)'),
(27, 1473969490, 12, 1, '14. about. static_pages (1)'),
(28, 1473969517, 11, 1, 'blog (15)'),
(29, 1473969949, 12, 1, '15. blog. library (3)'),
(30, 1473970128, 11, 1, 'contact (16)'),
(31, 1473970133, 12, 1, '16. contact. library (3)'),
(32, 1473970198, 11, 1, 'hrupa (17)'),
(33, 1473970945, 12, 1, '17. hrupa. library (3)');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register_do`
--

DROP TABLE IF EXISTS `wl_user_register_do`;
CREATE TABLE IF NOT EXISTS `wl_user_register_do` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `public` tinyint(1) NOT NULL,
  `title` text NOT NULL,
  `title_public` text NOT NULL,
  `help_additionall` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_register_do`
--

INSERT INTO `wl_user_register_do` (`id`, `name`, `public`, `title`, `title_public`, `help_additionall`) VALUES
(1, 'signup', 1, 'Реєстрація нового користувача', 'Реєстрація користувача', ''),
(2, 'confirmed', 1, 'Підтвердження реєстрації користувача', 'Підтвердження реєстрації', ''),
(3, 'reset_sent', 0, 'Відновлення паролю. Вислано повідомлення із кодом відновлення.', '', ''),
(4, 'reset', 1, 'Відновлення паролю. Пароль змінено. Старий пароль у полі Додатково.', 'Зміна паролю користувачем', 'Попередній пароль у sha1'),
(5, 'profile_data', 0, 'Змінено особисті дані', '', 'field(id) - ід поля, value(text) - попередні дані'),
(6, 'login_bad', 0, 'Невірна спроба авторизації з ІР', '', 'ІР адреса'),
(7, 'profile_type', 1, 'Зміна типу користувача', 'Зміна типу користувача', 'user(id) - хто змінив, old_type(id) - попередній тип'),
(8, 'subscribe', 0, 'Підписався на оновлення', '', ''),
(9, 'reset_admin', 1, 'Відновлення паролю. Пароль змінено. Старий пароль у полі Додатково.', 'Зміна паролю адміністрацією', 'Зміна паролю адміністрацією. Пароль змінено. Старий пароль у полі Додатково.'),
(10, 'user_delete', 0, 'Видалив профіль користувача', 'Видалив профіль користувача', 'Id. Email. User name. Type. Date register'),
(11, 'alias_add', 0, 'Додано головну адресу', 'Додано головну адресу', 'Адреса посилання'),
(12, 'alias_delete', 0, 'Видалена головна адреса', 'Видалена головна адреса', 'Ід. Адреса. Сервіс.'),
(13, 'service_install', 0, 'Install service', 'Install service', 'Id. Service name (version)'),
(14, 'service_uninstall', 0, 'Uninstall service', 'Uninstall service', 'Id. Service name (version)');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_status`
--

DROP TABLE IF EXISTS `wl_user_status`;
CREATE TABLE IF NOT EXISTS `wl_user_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `title` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_status`
--

INSERT INTO `wl_user_status` (`id`, `name`, `title`) VALUES
(1, 'confirmed', 'Підтверджений'),
(2, 'registered', 'Новозареєстрований'),
(3, 'banned', 'Заблокований');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_types`
--

DROP TABLE IF EXISTS `wl_user_types`;
CREATE TABLE IF NOT EXISTS `wl_user_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `title` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Дамп даних таблиці `wl_user_types`
--

INSERT INTO `wl_user_types` (`id`, `name`, `title`, `active`) VALUES
(1, 'admin', 'Адміністратор', 1),
(2, 'manager', 'Менеджер', 1),
(3, 'reserved', 'Резерв', 1),
(4, 'single', 'Користувач', 1),
(5, 'subscribe', 'Підписник', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_video`
--

DROP TABLE IF EXISTS `wl_video`;
CREATE TABLE IF NOT EXISTS `wl_video` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `author` int(11) NOT NULL,
  `date_add` int(11) NOT NULL,
  `site` text COMMENT 'youtube, vimeo',
  `link` text,
  `active` int(1) DEFAULT '1' COMMENT '0 - видалене',
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `wl_aliases`
--
ALTER TABLE `wl_aliases` ADD FULLTEXT KEY `alias` (`alias`);

--
-- Індекси таблиці `wl_sitemap`
--
ALTER TABLE `wl_sitemap` ADD FULLTEXT KEY `link` (`link`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
