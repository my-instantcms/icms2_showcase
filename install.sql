CREATE TABLE IF NOT EXISTS `{#}sc_cart_delivery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `hint` varchar(250) DEFAULT NULL,
  `type` varchar(30) DEFAULT 'courier',
  `pickup_address` varchar(160) DEFAULT NULL,
  `pickup_map` varchar(60) DEFAULT NULL,
  `price` float DEFAULT NULL,
  `ordering` int(3) DEFAULT '99',
  `is_pub` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{#}sc_cart_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  `title` varchar(50) NOT NULL,
  `hint` varchar(150) DEFAULT NULL,
  `type` varchar(40) NOT NULL,
  `attributes` text,
  `options` text,
  `is_fixed` tinyint(1) DEFAULT '0',
  `is_pub` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT '99',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

TRUNCATE TABLE `{#}sc_cart_fields`;
INSERT INTO `{#}sc_cart_fields` (`id`, `name`, `title`, `hint`, `type`, `attributes`, `options`, `is_fixed`, `is_pub`, `ordering`) VALUES
(1, 'name', 'Как к Вам обращаться?', 'Ф.И.О', 'string', 'placeholder | Имя\r\nmaxlength | 50', NULL, 1, 1, 1),
(2, 'email', 'Электронная почта', NULL, 'string', 'type | email', NULL, 1, 1, 2),
(3, 'agreement', 'Согласие на обработку данных', NULL, 'checkbox', 'checked |', 'label | Согласен на обработку <a href="/pages" target="_blank">Персональных данных</a>\r\ntitle_off | 1', 1, 1, 5),
(4, 'tel', 'Телефон', NULL, 'telephone', NULL, 'only | "ru", "ua", "by", "kz", "kg"\r\npreferred |  "ru", "ua"\r\ninitial | ru', 0, 1, 3),
(5, 'paid', 'Способ оплаты', NULL, 'payment', NULL, NULL, 1, 1, 4);

CREATE TABLE IF NOT EXISTS `{#}sc_checkouts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods` text NOT NULL,
  `price` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `shop_id` int(11) DEFAULT NULL,
  `fields` text,
  `delivery` text,
  `coupon` varchar(250) DEFAULT NULL,
  `paid` varchar(40) DEFAULT NULL,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(1) DEFAULT '1',
  `extra` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{#}sc_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `color` varchar(30) DEFAULT '#ffffff',
  `colored` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

TRUNCATE TABLE `{#}sc_colors`;
INSERT INTO `{#}sc_colors` (`id`, `title`, `color`, `colored`) VALUES
(1, 'Белый', '#ffffff', NULL),
(2, 'Черный', '#000000', NULL),
(3, 'Красный', '#ff0000', NULL),
(4, 'Зеленый', '#00ff00', NULL),
(5, 'Синий', '#0000ff', NULL);

CREATE TABLE IF NOT EXISTS `{#}sc_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `style` varchar(20) DEFAULT NULL,
  `icon` varchar(100) DEFAULT 'glyphicon glyphicon-bullhorn',
  `text` text NOT NULL,
  `old` text,
  `new` text,
  `date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{#}sc_pay_gateways` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `file_action` varchar(50) DEFAULT NULL,
  `file_form` varchar(50) DEFAULT NULL,
  `file_view` varchar(50) DEFAULT NULL,
  `file_success` varchar(50) DEFAULT NULL,
  `file_redirect` varchar(50) DEFAULT NULL,
  `is_pub` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

TRUNCATE TABLE `{#}sc_pay_gateways`;
INSERT INTO `{#}sc_pay_gateways` (`name`, `title`, `file_action`, `file_form`, `file_view`, `file_success`, `file_redirect`, `is_pub`) VALUES
('yandex', 'Яндекс Деньги', 'gateway_yandex', 'gateway_yandex', 'payment_yandex', 'success_yandex', NULL, 1),
('webmoney', 'Webmoney', 'gateway_webmoney', 'gateway_webmoney', 'payment_webmoney', 'success_webmoney', NULL, 1);

CREATE TABLE IF NOT EXISTS `{#}sc_pay_systems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gateway_name` varchar(50) DEFAULT NULL,
  `title` varchar(50) NOT NULL,
  `hint` varchar(100) DEFAULT NULL,
  `icon` text,
  `wallet_id` varchar(200) NOT NULL,
  `currency` varchar(100) DEFAULT NULL,
  `secret_key` varchar(250) DEFAULT NULL,
  `pay_type` varchar(100) DEFAULT NULL,
  `tax` varchar(100) DEFAULT NULL,
  `nds` varchar(100) DEFAULT NULL,
  `receipt` varchar(50) DEFAULT NULL,
  `conversion` varchar(200) DEFAULT NULL,
  `redirect_success` varchar(160) DEFAULT NULL,
  `redirect_fail` varchar(160) DEFAULT NULL,
  `is_test` tinyint(1) DEFAULT '0',
  `extra` text,
  `is_pub` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{#}sc_steps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL,
  `href` varchar(60) DEFAULT NULL,
  `hook` varchar(60) NOT NULL,
  `is_pub` tinyint(1) DEFAULT '1',
  `ordering` int(11) DEFAULT '3',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

TRUNCATE TABLE `{#}sc_steps`;
INSERT INTO `{#}sc_steps` (`id`, `title`, `href`, `hook`, `is_pub`, `ordering`) VALUES
(1, 'Доставка', 'cart_delivery', 'delivery', 1, 2),
(2, 'Поля контакты покупателя', 'cart_fields', 'fields', 1, 3);

CREATE TABLE IF NOT EXISTS `{#}sc_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `type` varchar(30) DEFAULT 'fields',
  `style` varchar(30) DEFAULT NULL,
  `fields` text,
  `text` text,
  `parent` varchar(50) DEFAULT NULL,
  `child` varchar(50) DEFAULT NULL,
  `ordering` int(11) DEFAULT '99',
  `is_pub` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;

TRUNCATE TABLE `{#}sc_tabs`;
INSERT INTO `{#}sc_tabs` (`id`, `title`, `type`, `style`, `fields`, `text`, `parent`, `child`, `ordering`, `is_pub`) VALUES
(1, 'Описание', 'fields', NULL, '---\r\ncontent', NULL, NULL, NULL, 1, 1),
(2, 'Характеристика', 'fields', NULL, '---\n- color\n- size\n- sc_prop_list\n', NULL, NULL, NULL, 2, 1),
(3, 'Теги', 'fields', NULL, '---\n- sc_tag_list\n', NULL, NULL, NULL, 3, 1);

CREATE TABLE IF NOT EXISTS `{#}sc_todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(10) DEFAULT '1',
  `title` varchar(60) DEFAULT NULL,
  `defaultStyle` varchar(30) DEFAULT 'lobilist-default',
  `ordering` int(11) DEFAULT '99',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2;

TRUNCATE TABLE `{#}sc_todo`;
INSERT INTO `{#}sc_todo` (`id`, `user_id`, `title`, `defaultStyle`, `ordering`) VALUES
(1, '1', 'Список задач', 'lobilist-warning', 1);

CREATE TABLE IF NOT EXISTS `{#}sc_todo_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `dueDate` varchar(20) DEFAULT NULL,
  `done` tinyint(2) DEFAULT '0',
  `listId` int(11) NOT NULL,
  `ordering` int(11) unsigned DEFAULT '99',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `{#}sc_transactions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `system_id` int(11) NOT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  `history` text,
  `errors` text,
  `response` text,
  `date_pub` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

CREATE TABLE IF NOT EXISTS `{#}sc_variations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ctype_name` varchar(60) NOT NULL,
  `item_id` int(11) NOT NULL,
  `title` varchar(250) NOT NULL,
  `photo` text,
  `attached` tinyint(1) DEFAULT '0',
  `price` float DEFAULT NULL,
  `in` int(11) DEFAULT NULL,
  `artikul` varchar(20) DEFAULT NULL,
  `ordering` INT(11) NULL DEFAULT '99',
  `size` varchar(160) DEFAULT NULL,
  `sale` float DEFAULT NULL,
  `seo_keys` varchar(250) DEFAULT NULL,
  `seo_desc` varchar(250) DEFAULT NULL,
  `seo_title` varchar(250) DEFAULT NULL,
  `color` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

DELETE FROM `{#}users_tabs` WHERE `controller` = 'showcase';
INSERT INTO `{#}users_tabs` (`title`, `controller`, `name`, `is_active`, `ordering`, `groups_view`, `groups_hide`, `show_only_owner`) VALUES
('Мои заказы', 'showcase', 'orders', 1, 4, NULL, NULL, 1);

DELETE FROM `{#}scheduler_tasks` WHERE `controller` = 'showcase';
INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `date_last_run`, `is_active`, `is_new`) VALUES 
('Обновление курса валют в платежных системах', 'showcase', 'course', 1440, NULL, 1, 1);

DELETE FROM `{#}widgets` WHERE `controller` = 'showcase';
INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`) VALUES
('showcase', 'cart', 'Корзина', 'My-InstantCMS.Ru', 'http://my-instantcms.ru/', '1.0.0'),
('showcase', 'categories', 'Категории', 'My-instantCMS.Ru', 'http://my-instantcms.ru/', '1.0.0');