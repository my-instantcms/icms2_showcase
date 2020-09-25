<?php

class actionShowcaseGenerate extends cmsAction {

	public function run() {
		
		if (!$this->ctype_name){ dump('Сначала выбирайте тип контента в качестве Магазина'); }
		$ctype = $this->model->useCache('content.types')->getItemByField('content_types', 'name', $this->ctype_name);
		if (!$ctype){ dump('Тип контента не найден'); }
		
		$ctype['options'] = is_array($ctype['options']) ? $ctype['options'] : cmsModel::yamlToArray($ctype['options']);
		$ctype['options']['list_style'] = array('showcase');

		$this->model->update('content_types', $ctype['id'], array('options' => $ctype['options']));

		$showcase_item = $this->cms_config->root_path . "templates/default/content/{$this->ctype_name}_item.tpl.php";
		if (!file_exists($showcase_item)){
			$ctype_file = 'showcase';
			if (!empty($ctype['options']['old_ctype'])){
				if ($ctype['options']['old_ctype'] != $this->ctype_name){
					$ctype_file = $ctype['options']['old_ctype'];
				}
			}
			$showcase_item = $this->cms_config->root_path . "templates/default/content/{$ctype_file}_item.tpl.php";
			if (!file_exists($showcase_item)){
				cmsUser::addSessionMessage('Не найден файл: /templates/default/content/showcase_item.tpl.php', 'error');
			} else {
				$showcase_new = $this->cms_config->root_path . "templates/default/content/{$this->ctype_name}_item.tpl.php";
				if (!copy($showcase_item, $showcase_new)) {
					cmsUser::addSessionMessage('Ошибка при копировании: ' . $showcase_new, 'error');
				}
			}
		}
		
		$index_showcase = $this->cms_config->root_path . "templates/default/controllers/search/index_{$this->ctype_name}.tpl.php";
		if (!file_exists($index_showcase)){
			$index_file = 'showcase';
			if (!empty($ctype['options']['old_ctype'])){
				if ($ctype['options']['old_ctype'] != $this->ctype_name){
					$index_file = $ctype['options']['old_ctype'];
				}
			}
			$index_showcase = $this->cms_config->root_path . "templates/default/controllers/search/index_{$index_file}.tpl.php";
			if (!file_exists($index_showcase)){
				cmsUser::addSessionMessage('Не найден файл: /templates/default/controllers/search/index_showcase.tpl.php', 'error');
			} else {
				$index_new = $this->cms_config->root_path . "templates/default/controllers/search/index_{$this->ctype_name}.tpl.php";
				if (!copy($index_showcase, $index_new)) {
					cmsUser::addSessionMessage('Ошибка при копировании: ' . $index_new, 'error');
				}
			}
		}

		$errors = '';
		
		if (!$this->model->db->isFieldExists('con_' . $this->ctype_name, 'artikul')){
			$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `artikul` VARCHAR(20) NULL DEFAULT NULL");
			$this->model->db->query("ALTER TABLE `{#}con_{$this->ctype_name}` ADD KEY `artikul` (`artikul`);");
		}
		
		if (!$this->model->db->isFieldExists('con_' . $this->ctype_name . '_cats', 'sc_fa')){
			$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name}_cats ADD `sc_fa` VARCHAR(60) NULL DEFAULT NULL");
		}
		
		if (!$this->model->db->isFieldExists('con_' . $this->ctype_name . '_cats', 'sc_icon')){
			$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name}_cats ADD `sc_icon` VARCHAR(100) NULL DEFAULT NULL");
		}
		
		if (!$this->model->db->isFieldExists('con_' . $this->ctype_name . '_cats', 'sc_color')){
			$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name}_cats ADD `sc_color` VARCHAR(40) NULL DEFAULT NULL");
		}
		
		$date_pub = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'name', 'date_pub');
			
		if ($date_pub && $date_pub['is_in_filter'] == 1){
			$this->model->db->query("UPDATE `{#}con_{$this->ctype_name}_fields` SET `is_in_filter` = NULL WHERE `id` = {$date_pub['id']};");
		}
		
		$photo = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'name', 'photo');
			
		if ($photo){
			if ($photo['type'] != 'images'){
				$this->model->db->query("UPDATE `{#}con_{$this->ctype_name}_fields` SET `title` = 'Фотографии', `type` = 'images', `options` = '---\nsize_teaser: big\nsize_full: big\nsize_small: small\nsizes:\n  - normal\n  - micro\n  - small\n  - big\n  - original\nallow_import_link: 1\nfirst_image_emphasize: 1\nmax_photos:\ncontext_list:\n  - 0\nrelation_id: 0\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: left\nwrap_width: 55%\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n' WHERE `id` = {$photo['id']};");
			}
		} else {
			$photo = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'photo', 'Фотографии', NULL, 4, NULL, 'images', 1, 1, NULL, NULL, 1, NULL, NULL, NULL, '---\nsize_teaser: big\nsize_full: big\nsize_small: small\nsizes:\n  - normal\n  - micro\n  - small\n  - big\n  - original\nallow_import_link: 1\nfirst_image_emphasize: 1\nmax_photos:\ncontext_list:\n  - 0\nrelation_id: 0\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: left\nwrap_width: 55%\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');", false, true);
			if ($photo){
				$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `photo` text");
			} else {
				$errors .= 'photo, ';
			}
		}
		
		$variants = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'type', 'scvariations');
		if (!$variants){
			$variants = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'variants', 'Варианты товара', NULL, 10, NULL, 'scvariations', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\ncontext_list:\n  - 0\nrelation_id: 0\nsc_position: 0\nlabel_in_list: left\nlabel_in_item: left\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
			if ($variants){
				$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `variants` text");
			} else {
				$errors .= 'variants, ';
			}
		}
		
		$color = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'name', 'color');
		if (!$color){
			$color = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'color', 'Цвет', NULL, 9, NULL, 'sccolor', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\nis_checkbox_multiple: null\nlist_class: multiple_tags_list\ncontext_list:\n  - 0\nrelation_id: 0\nsc_position: 4\nlabel_in_list: left\nlabel_in_item: left\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
			if ($color){
				$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `color` varchar(100) NULL DEFAULT NULL");
				$this->model->db->query("ALTER TABLE `{#}con_{$this->ctype_name}` ADD KEY `color` (`color`);");
				if (!$this->model->db->isFieldExists('sc_variations', 'color')){
					$this->model->db->query("ALTER TABLE {#}sc_variations ADD `color` varchar(100) NULL DEFAULT NULL");
				}
			} else {
				$errors .= 'color, ';
			}
		}
		
		$in_stock = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'name', 'in_stock');
		if (!$in_stock){
			$in_stock = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'in_stock', 'В наличии', NULL, 8, NULL, 'number', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\ndecimal_int: 7\ndecimal_s: 0\nthousands_sep:\nis_abs: null\nfilter_range: null\nunits: штук\ncontext_list:\n  - 0\nrelation_id: 0\nsc_position: 0\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
			if ($in_stock){
				$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `in_stock` int(11) DEFAULT NULL");
			} else {
				$errors .= 'in_stock, ';
			}
		}
		
		$size = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'name', 'size');
		if (!$size){
			$size = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'size', 'Размер', NULL, 9, NULL, 'scvolume', NULL, 1, 1, NULL, NULL, NULL, NULL, 'Не указан\r\n1\r\n2\r\n3\r\n4', '---\nis_checkbox_multiple: null\nlist_class: multiple_tags_list\ncontext_list:\n  - 0\nrelation_id: 0\nsc_position: 4\nlabel_in_list: left\nlabel_in_item: left\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
			if ($size){
				$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `size` varchar(160) DEFAULT NULL");
				$this->model->db->query("ALTER TABLE `{#}con_{$this->ctype_name}` ADD KEY `size` (`size`);");
				if (!$this->model->db->isFieldExists('sc_variations', 'size')){
					$this->model->db->query("ALTER TABLE {#}sc_variations ADD `size` varchar(160) DEFAULT NULL");
				}
			} else {
				$errors .= 'size, ';
			}
		}
		
		$price = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'name', 'price');
		if (!$price){
			$price = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'price', 'Цена', NULL, 6, NULL, 'scprice', 1, 1, 1, NULL, NULL, NULL, NULL, NULL, '---\nfilter_range: 1\ncontext_list:\n  - 0\nrelation_id: 0\nsc_position: 0\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: auto\nwrap_width:\nis_required: 1\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
			if ($price){
				$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `price` DECIMAL(19,2) UNSIGNED NULL DEFAULT NULL");
				$this->model->db->query("ALTER TABLE `{#}con_{$this->ctype_name}` ADD KEY `price` (`price`);");
			} else {
				$errors .= 'price, ';
			}
		}
		
		$sale = $this->model->
			useCache('content.fields.' . $this->ctype_name)->
			getItemByField('con_' . $this->ctype_name . '_fields', 'name', 'sale');
		if (!$sale){
			$sale = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'sale', 'Цена со скидкой', NULL, 7, NULL, 'scprice', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\nfilter_range: null\ncontext_list:\n  - 0\nrelation_id: 0\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
			if ($sale){
				$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `sale` DECIMAL(19,2) UNSIGNED NULL DEFAULT NULL");
			} else {
				$errors .= 'sale, ';
			}
		}
		
		$bookmarks = cmsCore::isControllerExists('bookmarks') ? cmsCore::getController('bookmarks') : false;
		if ($bookmarks){
			$fav = $this->model->
				useCache('content.fields.' . $this->ctype_name)->
				getItemByField('con_' . $this->ctype_name . '_fields', 'type', 'bookmarks');
			if (!$fav){
				$fav = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'fav', 'В закладки', NULL, 10, NULL, 'bookmarks', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nlist_tpl: >\n  controllers/showcase/tpl/bookmarks_teaser\nview_tpl: >\n  controllers/showcase/tpl/bookmarks_teaser\ncontext_list:\n  - 0\nrelation_id: 0\nsc_position: 1\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
				if ($fav){
					$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `fav` int(11) DEFAULT '0'");
				} else {
					$errors .= 'fav, ';
				}
			}
		}
		
		$recommends = cmsCore::isControllerExists('recommends') ? cmsCore::getController('recommends') : false;
		if ($recommends){
			$revs = $this->model->
				useCache('content.fields.' . $this->ctype_name)->
				getItemByField('con_' . $this->ctype_name . '_fields', 'type', 'recommendstars');
			if (!$revs){
				$revs = $this->model->db->query("INSERT INTO `{#}con_{$this->ctype_name}_fields` (`ctype_id`, `name`, `title`, `hint`, `ordering`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_edit`, `filter_view`) VALUES ({$ctype['id']}, 'revs', 'Отзывы', NULL, 11, NULL, 'recommendstars', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, '---\nlist_tpl: controllers/showcase/tpl/reviews_teaser\nview_tpl: controllers/recommends/informer_fragment\nfilter_tpl: controllers/recommends/fields/filter\ncontext_list:\n  - 0\nrelation_id: 0\nsc_position: 2\nlabel_in_list: none\nlabel_in_item: none\nwrap_type: auto\nwrap_width:\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_unique: null\nprofile_value:\n', '---\n- 0\n', '---\n- 0\n', '---\n- 0\n');");
				if ($revs){
					$this->model->db->query("ALTER TABLE {#}con_{$this->ctype_name} ADD `revs` float DEFAULT '0'");
				} else {
					$errors .= 'revs, ';
				}
			}
		}
		
		if (!$this->model->db->isFieldExists('sc_variations', 'color')){
			$this->model->db->query("ALTER TABLE {#}sc_variations ADD `color` varchar(100) NULL DEFAULT NULL");
		}
		
		if (!$this->model->db->isFieldExists('sc_variations', 'size')){
			$this->model->db->query("ALTER TABLE {#}sc_variations ADD `size` varchar(160) DEFAULT NULL");
		}
		
		cmsCache::getInstance()->clean('content.list.' . $this->ctype_name);
        cmsCache::getInstance()->clean('content.item.' . $this->ctype_name);
        cmsCache::getInstance()->clean('content.fields.' . $this->ctype_name);
		cmsCache::getInstance()->clean("showcase.sc_variations");
		cmsCache::getInstance()->clean("content.types");
		
		if ($errors){
			cmsUser::addSessionMessage('Не удалось создать поля: ' . $errors, 'error');
		} else {
			cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');
		}
		$this->redirectBack();

	}

}