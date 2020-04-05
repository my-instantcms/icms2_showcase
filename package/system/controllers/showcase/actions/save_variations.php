<?php

class actionShowcaseSaveVariations extends cmsAction {

    public function run($item_id, $id = false){
		
		if (!$this->request->isAjax()){ cmsCore::error404(); }
		
		$fields = $this->request->getAll();
		$title = !empty($fields['title']) ? $fields['title'] : false;
		$photo_big = !empty($fields['photo_big']) ? $fields['photo_big'] : false;
		$photo_small = !empty($fields['photo_small']) ? $fields['photo_small'] : false;
		$attached = !empty($fields['attached']) ? $fields['attached'] : false;
		$price = !empty($fields['price']) ? $fields['price'] : false;
		$sale = !empty($fields['sale']) ? $fields['sale'] : false;
		$in = !empty($fields['in']) ? $fields['in'] : false;
		if (!$title || !$price || $in < 0){ return $this->cms_template->renderJSON(array('error' => 'Заполните поля')); }
		unset($fields['title'], $fields['photo_big'], $fields['photo_small'], $fields['attached'], $fields['price'], $fields['sale'], $fields['in']);
		
		if ($fields){
			foreach ($fields as $field_name => $value){
				if (!$this->model->db->isFieldExists('sc_variations', $field_name)){
					$field = cmsCore::getModel('content')->
						filterEqual('i.name', $field_name)->
						getContentFields($this->ctype_name);
					if ($field){
						$sql = "ALTER TABLE {#}sc_variations ADD `{$field_name}` {$field[$field_name]['handler']->getSQL()}";
						$this->model->db->query($sql);
					}
				}
			}
		}
		
		if ($photo_big && $photo_small){
			$photos = array(
				'small' => $photo_small,
				'big' => $photo_big
			);
		}
		
		$data = array(
			'ctype_name' => $this->ctype_name,
			'item_id' => $item_id,
			'title' => $title,
			'photo' => isset($photos) ? $photos : null,
			'attached' => $attached,
			'price' => $price,
			'sale' => $sale,
			'in' => $in,
		) + $fields;

		$result = $id ? $this->model->updData('sc_variations', $id, $data) : $this->model->saveData('sc_variations', $data);
		if ($result){
			
			$is_manager = (in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);
			if ($is_manager && !empty($this->options['log'])) {
				$author = '<a href="' . href_to('users', $this->cms_user->id) . '" target="_blank">' . $this->cms_user->nickname . '</a>';
				$title = '<b data-toggle="tooltip" data-placement="top" title="' . $title . '">вариант</b>';
				$this->model->saveData('sc_logs', array(
					'style' => ($id ? 'success' : 'info'),
					'icon' => 'glyphicon glyphicon-' . ($id ? 'cog' : 'plus'),
					'text' => $author . ($id ? ' изменил ' : ' добавил ') . $title . ' товара'
				));
			}
			
			if ($id){
				$artikul = $this->getArtikulById($item_id, $id);
				$this->model->updData('sc_variations', $id, array('artikul' => $artikul));
			} else {
				$artikul = $this->getArtikulById($item_id, $result);
				$this->model->updData('sc_variations', $result, array('artikul' => $artikul));
			}
			
			$sc_fields = cmsCore::getModel('content')->
					orderBy('i.ordering')->
					filterLike('i.type', 'sc%')->
					getContentFields($this->ctype_name);
			$html = '';
			if (!$id) { $html .= '<li id="sc_variant_' . $result . '">'; }
			
			$html .= '<div class="sfv_photo">';
				if ($data['photo']){
					$html .= html_image($photos, 'small', $data['title']);
				} else {
					$html .= '<img src="/templates\default/controllers/showcase/img/nophoto.png" />';		
				}
			$html .= '</div><div class="sfv_title"><b>' . $data['title'] . '</b> ';
			$html .= ' <a href="' . href_to('showcase', 'form_variations', array($item_id, ($id ? $id : $result))) . '" class="ajax-modal" data-sc-tip="' . LANG_EDIT . '"><i class="fa fa-pencil-square-o"></i></a> <a class="sfv_trash" data-sc-tip="' . LANG_DELETE . '" onclick="sc_deleteVariation(this, ' . ($id ? $id : $result) . ')"><i class="fa fa-trash"></i></a></div><div class="sfv_meta">';
			
			if (!empty($fields)){
				foreach ($fields as $key => $val){
					if (!empty($sc_fields[$key])){
						$vals = $sc_fields[$key]['handler']->getListItems(1);
						$html .= $sc_fields[$key]['title'] . ': ' . (!empty($vals[$val]) ? $vals[$val] : '') . ', ';
					}
				}
			}
			$html .= 'Цена: ' . $price . ' ' . (!empty($this->options['currency']) ? $this->options['currency'] : LANG_CURRENCY) . ', ';
			$html .= 'В наличии: ' . ($in ? $in : 0);
			$html .= '<input type="hidden" name="variants[' . ($id ? $id : $result) . ']" value="' . ($id ? $id : $result) . '" /></div>';
			if (!$id) { $html .= '</li>'; }
			return $this->cms_template->renderJSON(array('error' => false, 'html' => $html, 'do' => ($id ? 'edit' : 'add')));
		}
		
        return $this->cms_template->renderJSON(array('error' => 'Неизвестная ошибка'));

    }

}
