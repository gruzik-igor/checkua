-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Час створення: Жов 29 2014 р., 19:32
-- Версія сервера: 5.6.17
-- Версія PHP: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База даних: `stezhkam_nemo`
--

-- --------------------------------------------------------

--
-- Структура таблиці `mail_handler`
--

CREATE TABLE IF NOT EXISTS `mail_handler` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `name` text NOT NULL,
  `email` text NOT NULL,
  `phone` text NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_aliases`
--

CREATE TABLE IF NOT EXISTS `wl_aliases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` text NOT NULL COMMENT 'ссилка',
  `service` int(11) NOT NULL DEFAULT '0',
  `table` text NOT NULL,
  `form` int(11) DEFAULT NULL,
  `options` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'наявність опцій',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп даних таблиці `wl_aliases`
--

INSERT INTO `wl_aliases` (`id`, `alias`, `service`, `table`, `form`, `options`, `active`) VALUES
(1, 'main', 0, '', NULL, 0, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_fields`
--

CREATE TABLE IF NOT EXISTS `wl_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form` int(11) NOT NULL,
  `user_type` int(11) NOT NULL DEFAULT '0' COMMENT '0 - all',
  `name` text NOT NULL,
  `position` int(11) NOT NULL DEFAULT '0',
  `input_type` int(11) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT '0',
  `can_change` tinyint(1) NOT NULL DEFAULT '1',
  `title` text NOT NULL,
  `value` text NOT NULL COMMENT 'Можливі значення через |||. Нумерація у формі від 1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп даних таблиці `wl_fields`
--

INSERT INTO `wl_fields` (`id`, `form`, `user_type`, `name`, `position`, `input_type`, `required`, `can_change`, `title`, `value`) VALUES
(1, 1, 0, 'photo', 0, 4, 0, 1, 'Фотографія', ''),
(2, 1, 0, 'birth', 0, 5, 1, 0, 'Дата народження', ''),
(3, 1, 0, 'sex', 0, 9, 1, 0, 'Стать', 'Чоловіча|||Жіноча'),
(4, 1, 0, 'phone', 0, 1, 0, 1, 'Телефон', ''),
(5, 1, 0, 'address', 0, 3, 0, 1, 'Адреса', '');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_forms`
--

CREATE TABLE IF NOT EXISTS `wl_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `captcha` tinyint(1) NOT NULL DEFAULT '0',
  `help` text NOT NULL,
  `table` text NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-GET, 2-POST',
  `type_data` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1-fields, 2-values',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Дамп даних таблиці `wl_forms`
--

INSERT INTO `wl_forms` (`id`, `name`, `captcha`, `help`, `table`, `type`, `type_data`) VALUES
(1, 'user_signup', 1, 'Заповнюється під час реєстрації', '', 1, 1),
(2, 'user_recovery', 1, 'Відновлння паролю', '', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_input_types`
--

CREATE TABLE IF NOT EXISTS `wl_input_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `options` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

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
(11, 'number', 0);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_ntkd`
--

CREATE TABLE IF NOT EXISTS `wl_ntkd` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` int(11) NOT NULL,
  `content` int(11) NOT NULL,
  `language` text NOT NULL,
  `name` text NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `keywords` text NOT NULL,
  `text` text NOT NULL,
  `list` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_options`
--

CREATE TABLE IF NOT EXISTS `wl_options` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` int(11) NOT NULL,
  `alias` int(11) NOT NULL,
  `name` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_services`
--

CREATE TABLE IF NOT EXISTS `wl_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL COMMENT 'службова назва (папки)',
  `title` text NOT NULL COMMENT 'публічна назва',
  `description` text NOT NULL,
  `table` text NOT NULL COMMENT 'службова таблиця',
  `version` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Дамп даних таблиці `wl_services`
--

INSERT INTO `wl_services` (`id`, `name`, `title`, `description`, `table`, `version`, `active`) VALUES
(1, 'static_pages', 'Статичні сторінки', '', '', '1.0', 1);

-- --------------------------------------------------------

--
-- Структура таблиці `wl_users`
--

CREATE TABLE IF NOT EXISTS `wl_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` text NOT NULL,
  `name` text NOT NULL,
  `photo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Чи наявне фото у профілі',
  `type` smallint(4) NOT NULL DEFAULT '4',
  `status` tinyint(1) NOT NULL DEFAULT '2',
  `reset_key` text NOT NULL,
  `reset_expires` int(11) NOT NULL DEFAULT '0',
  `registered` int(11) NOT NULL DEFAULT '0',
  `active` int(11) NOT NULL,
  `auth_id` text NOT NULL,
  `password` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_info`
--

CREATE TABLE IF NOT EXISTS `wl_user_info` (
  `user` int(11) NOT NULL,
  `field` int(11) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_permissions`
--

CREATE TABLE IF NOT EXISTS `wl_user_permissions` (
  `user` int(11) NOT NULL,
  `permission` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register`
--

CREATE TABLE IF NOT EXISTS `wl_user_register` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` int(11) NOT NULL,
  `do` tinyint(4) NOT NULL,
  `user` int(11) NOT NULL,
  `additionally` text,
  PRIMARY KEY (`id`),
  KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_register_do`
--

CREATE TABLE IF NOT EXISTS `wl_user_register_do` (
  `id` smallint(6) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `public` tinyint(1) NOT NULL,
  `title` text NOT NULL,
  `title_public` text NOT NULL,
  `help_additionall` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Дамп даних таблиці `wl_user_register_do`
--

INSERT INTO `wl_user_register_do` (`id`, `name`, `public`, `title`, `title_public`, `help_additionall`) VALUES
(1, 'signup', 1, 'Реєстрація нового користувача', 'Реєстрація користувача', ''),
(2, 'confirmed', 1, 'Підтвердження реєстрації користувача', 'Підтвердження реєстрації', ''),
(3, 'reset_sent', 0, 'Відновлення паролю. Вислано повідомлення із кодом відновлення.', '', ''),
(4, 'reset', 1, 'Відновлення паролю. Пароль змінено. Старий пароль у полі Додатково.', 'Зміна паролю користувачем', 'попередній пароль у md5'),
(5, 'profile_data', 0, 'Змінено особисті дані', '', 'field(id) - ід поля, value(text) - попередні дані'),
(6, 'login_bad', 0, 'Невірна спроба авторизації з ІР', '', 'ІР адреса'),
(7, 'profile_type', 1, 'Зміна типу користувача', 'Зміна типу користувача', 'user(id) - хто змінив, old_type(id) - попередній тип'),
(8, 'subscribe', 0, 'Підписався на оновлення', '', ''),
(9, 'reset_admin', 1, 'Відновлення паролю. Пароль змінено. Старий пароль у полі Додатково.', 'Зміна паролю адміністрацією', 'Зміна паролю адміністрацією. Пароль змінено. Старий пароль у полі Додатково.');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_status`
--

CREATE TABLE IF NOT EXISTS `wl_user_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Дамп даних таблиці `wl_user_status`
--

INSERT INTO `wl_user_status` (`id`, `name`) VALUES
(1, 'confirmed'),
(2, 'registered'),
(3, 'banned');

-- --------------------------------------------------------

--
-- Структура таблиці `wl_user_types`
--

CREATE TABLE IF NOT EXISTS `wl_user_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Дамп даних таблиці `wl_user_types`
--

INSERT INTO `wl_user_types` (`id`, `name`, `active`) VALUES
(1, 'admin', 1),
(2, 'manager', 1),
(3, 'reserved', 0),
(4, 'single', 1),
(5, 'subscribe', 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
