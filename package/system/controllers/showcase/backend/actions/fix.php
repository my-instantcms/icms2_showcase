<?php

class actionShowcaseFix extends cmsAction {

    public function run($action = false){
		
		if (!$action){ cmsCore::error404(); }
		
		switch ($action) {
			case 'showcase_item':
				if ($this->ctype_name){
					unlink($this->cms_config->root_path . "templates/default/content/{$this->ctype_name}_item.tpl.php");
					$showcase_item = $this->cms_config->root_path . "templates/default/content/showcase_item.tpl.php";
					if (!file_exists($showcase_item)){
						cmsUser::addSessionMessage('Не найден файл: /templates/default/content/showcase_item.tpl.php', 'error');
					} else {
						$showcase_new = $this->cms_config->root_path . "templates/default/content/{$this->ctype_name}_item.tpl.php";
						if (!copy($showcase_item, $showcase_new)) {
							cmsUser::addSessionMessage('Ошибка при копировании: ' . $showcase_new, 'error');
						}
					}
				}
				break;
			case 'index_showcase':
				if ($this->ctype_name){
					unlink($this->cms_config->root_path . "templates/default/controllers/search/index_{$this->ctype_name}.tpl.php");
					$index_showcase = $this->cms_config->root_path . "templates/default/controllers/search/index_showcase.tpl.php";
					if (!file_exists($index_showcase)){
						cmsUser::addSessionMessage('Не найден файл: templates/default/controllers/search/index_showcase.tpl.php', 'error');
					} else {
						$index_new = $this->cms_config->root_path . "templates/default/controllers/search/index_{$this->ctype_name}.tpl.php";
						if (!copy($index_showcase, $index_new)) {
							cmsUser::addSessionMessage('Ошибка при копировании: ' . $index_new, 'error');
						}
					}
				}
				break;
			case 'paid':
				$paid = $this->model->getItemByField('sc_cart_fields', 'name', 'paid');
				if (!$paid){
					$this->model->db->query("INSERT INTO `{#}sc_cart_fields` (`name`, `title`, `hint`, `type`, `attributes`, `options`, `is_fixed`, `is_pub`, `ordering`) VALUES ('paid', 'Способ оплаты', NULL, 'payment', NULL, NULL, '1', '1', '4');");
				}
				break;
			case 'v_ordering':
				$v_ordering = $this->model->db->isFieldExists('sc_variations', 'ordering');
				if (!$v_ordering){
					$this->model->db->query("ALTER TABLE `{#}sc_variations` ADD `ordering` INT(11) NULL DEFAULT '99';", 0, 1);
				}
				break;
			case 'v_sale':
				$v_sale = $this->model->db->isFieldExists('sc_variations', 'sale');
				if (!$v_sale){
					$this->model->db->query("ALTER TABLE `{#}sc_variations` ADD `sale` FLOAT NULL DEFAULT NULL;", 0, 1);
				}
				break;
			case 'v_seo_keys':
				$v_seo_keys = $this->model->db->isFieldExists('sc_variations', 'seo_keys');
				if (!$v_seo_keys){
					$this->model->db->query("ALTER TABLE `{#}sc_variations` ADD `seo_keys` VARCHAR(250) NULL DEFAULT NULL", 0, 1);
				}
				break;
			case 'v_seo_desc':
				$v_seo_desc = $this->model->db->isFieldExists('sc_variations', 'seo_desc');
				if (!$v_seo_desc){
					$this->model->db->query("ALTER TABLE `{#}sc_variations` ADD `seo_desc` VARCHAR(250) NULL DEFAULT NULL", 0, 1);
				}
				break;
			case 'v_seo_title':
				$v_seo_title = $this->model->db->isFieldExists('sc_variations', 'seo_title');
				if (!$v_seo_title){
					$this->model->db->query("ALTER TABLE `{#}sc_variations` ADD `seo_title` VARCHAR(250) NULL DEFAULT NULL", 0, 1);
				}
				break;
			case 'receipt':
				$receipt = $this->model->db->isFieldExists('sc_pay_systems', 'receipt');
				if (!$receipt){
					$this->model->db->query("ALTER TABLE `{#}sc_pay_systems` ADD `receipt` VARCHAR(100) NULL DEFAULT NULL AFTER `pay_type`", 0, 1);
				}
				break;
			case 'tax':
				$tax = $this->model->db->isFieldExists('sc_pay_systems', 'tax');
				if (!$tax){
					$this->model->db->query("ALTER TABLE `{#}sc_pay_systems` ADD `tax` VARCHAR(100) NULL DEFAULT NULL AFTER `pay_type`", 0, 1);
				}
				break;
			case 'nds':
				$nds = $this->model->db->isFieldExists('sc_pay_systems', 'nds');
				if (!$nds){
					$this->model->db->query("ALTER TABLE `{#}sc_pay_systems` ADD `nds` VARCHAR(100) NULL DEFAULT NULL AFTER `pay_type`", 0, 1);
				}
				break;
			case 'sc_tabs':
				$sc_tabs = $this->model->db->query("SELECT id FROM {#}sc_tabs", false, true) ? 1 : 0;
				if (!$sc_tabs){
					$this->model->db->query("CREATE TABLE IF NOT EXISTS `{#}sc_tabs` (`id` int(11) NOT NULL AUTO_INCREMENT,`title` varchar(50) NOT NULL,`type` varchar(30) DEFAULT 'fields',`style` varchar(30) DEFAULT NULL,`fields` text,`text` text,`parent` varchar(50) DEFAULT NULL,`child` varchar(50) DEFAULT NULL,`ordering` int(11) DEFAULT '99',`is_pub` tinyint(1) DEFAULT '1',PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4;TRUNCATE TABLE `{#}sc_tabs`;INSERT INTO `{#}sc_tabs` (`id`, `title`, `type`, `style`, `fields`, `text`, `parent`, `child`, `ordering`, `is_pub`) VALUES(1, 'Описание', 'fields', NULL, '---\r\ncontent', NULL, NULL, NULL, 1, 1),(2, 'Характеристика', 'fields', NULL, '---\n- color\n- size\n- sc_prop_list\n', NULL, NULL, NULL, 2, 1),(3, 'Теги', 'fields', NULL, '---\n- sc_tag_list\n', NULL, NULL, NULL, 3, 1);", 0, 1);
				}
				break;
			case 'sc_aggregators':
				$sc_aggregators = $this->model->db->query("SELECT id FROM {#}sc_aggregators", false, true) ? 1 : 0;
				if (!$sc_aggregators){
					$this->model->db->query("CREATE TABLE IF NOT EXISTS `{#}sc_aggregators`(`id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(20) NOT NULL,`company` varchar(100) NOT NULL,`email` varchar(60) DEFAULT NULL,`url` varchar(100) NOT NULL,`categories` text,`currency` varchar(20) DEFAULT NULL,`relateds` text,`fields` text,`currencies` text NOT NULL,`adult` tinyint(1) DEFAULT '0',`delivery` tinyint(1) DEFAULT '1',`cost` decimal(11,0) DEFAULT NULL,`days` varchar(20) DEFAULT NULL,`pickup` tinyint(1) DEFAULT '1',`pickup_cost` decimal(11,0) DEFAULT NULL,`pickup_days` varchar(20) DEFAULT NULL,`store` tinyint(1) DEFAULT NULL,`files` text,`is_pub` tinyint(1) DEFAULT '1',PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;", 0, 1);
				}
				break;
			case 'yml':
				$yml = $this->model->getItemByField('scheduler_tasks', 'hook', 'yml');
				if (!$yml){
					$this->model->db->query("INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `date_last_run`, `is_active`, `is_new`) VALUES ('Создание yml карты товаров', 'showcase', 'yml', 1440, NULL, 1, 1);", 0, 1);
				}
				break;
			case 'sale_id':
				$sale_id = $this->model->db->isFieldExists('sc_checkouts', 'sale_id');
				if (!$sale_id){
					$this->model->db->query("ALTER TABLE `{#}sc_checkouts` ADD `sale_id` int(11) NULL DEFAULT NULL AFTER `price`;", 0, 1);
				}
				break;
			case 'sc_sales':
				$sc_sales = $this->model->db->query("SELECT id FROM {#}sc_sales", false, true) ? 1 : 0;
				if (!$sc_sales){
					$this->model->db->query("CREATE TABLE IF NOT EXISTS `{#}sc_sales` (`id` int(11) NOT NULL AUTO_INCREMENT, `title` varchar(60) NOT NULL, `start` decimal(19,2) UNSIGNED DEFAULT NULL, `type` varchar(20) DEFAULT 'type', `sale` decimal(19,2) DEFAULT NULL, `is_pub` tinyint(1) DEFAULT '1', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;", 0, 1);
				}
				break;
			case 'icon':
				$icon = $this->model->db->isFieldExists('sc_tabs', 'icon');
				if (!$icon){
					$this->model->db->query("ALTER TABLE `{#}sc_tabs` ADD `icon` VARCHAR(60) NULL DEFAULT NULL AFTER `title`;", 0, 1);
				}
				break;
			case 'file':
				$file = $this->model->db->isFieldExists('sc_aggregators', 'file');
				if (!$file){
					$this->model->db->query("ALTER TABLE `{#}sc_aggregators` ADD `file` VARCHAR(60) AFTER `id`;", 0, 1);
				}
				break;
		}

		$this->redirectBack();

    }
	
}