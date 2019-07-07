<?php

class actionShowcaseImportData extends cmsAction {

	public function run() {
		
		$content_model = cmsCore::getModel('content');
		$ctype = $content_model->getContentTypeByName($this->ctype_name);
        if (!$ctype) { cmsCore::error404(); }
		
		$fields = $content_model->orderBy('ordering')->getContentFields($ctype['name']);
		$props = $content_model->getContentProps($ctype['name']);
		$ctype['props'] = $props;
		
		if (!$this->request->has('send_data')){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Нет полей для импорта'));
		}

		$data = cmsUser::isSessionSet('sc_import_data') ? cmsUser::sessionGet('sc_import_data') : false;

		if (!$data) {
			cmsUser::addSessionMessage('Выполните шаг 1', 'error');
			$this->redirectToAction('import');
		}

		if (empty($data['file']['path'])){
			cmsUser::addSessionMessage('Загрузите csv файл', 'error');
			$this->redirectToAction('import');
		}

		$file = $this->cms_config->upload_path . $data['file']['path'];
		if (!$file){
			cmsUser::addSessionMessage('Неправильный путь файла: ' . $file, 'error');
			$this->redirectToAction('import');
		}
		
		$str_file = file_get_contents($file);
		$encode = mb_detect_encoding($str_file, array('Windows-1251', 'UTF-8', 'ASCII'), true);
		if ($encode !== 'UTF-8'){
			$fc = @iconv($encode, 'utf-8', $str_file);
			file_put_contents($file, $fc);
		}
		
		$handle = fopen($file, 'r');
		if (false === $handle) {
			cmsUser::addSessionMessage('Не удалось открыть файл', 'error');
			$this->redirectToAction('import');
		}
		
		$rows = array_map('trim', fgetcsv($handle, 0, $data['sep']));
		
		if (isset($rows[0])) {
			if ('efbbbf' === substr(bin2hex($rows[0]), 0, 6)){
				$rows[0] = substr($rows[0], 3);
			}
		}

		$raw_data = array();
		$position = !empty($data['position']) ? $data['position'] : 0;
		$limit = !empty($data['limit']) ? $data['limit'] : 100;
		$i = 1;
		while(1){
			$item = fgetcsv($handle, 0, $data['sep']);
			if (false !== $item && count($rows) == count($item)) {
				if ($position < $i){
					$raw_data[$i] = array_combine($rows, $item);
				}
				if (($limit + (int)$position) <= $i){
					break;
				}
				$i++;
			} else {
				break;
			}
		}

		fclose($handle);
		
		if (!$raw_data){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Нет данных для импорта'));
		}
		
		$data['position'] = ($position + count($raw_data));
		
		if (cmsUser::isSessionSet('sc_import_data')){ cmsUser::sessionUnset('sc_import_data'); }
		cmsUser::sessionSet('sc_import_data', $data);
		
		$send_data = $this->request->get('send_data', array());
		$added = 0;
		$updated = 0;
		
		$field_colors = $this->model->
			selectOnly('i.name')->
			filterEqual('i.type', 'sccolor')->
			getData('con_' . $this->ctype_name . '_fields', false, false, false, 'name');

		$field_volume = $this->model->
			selectOnly('i.name')->
			filterEqual('i.type', 'scvolume')->
			getData('con_' . $this->ctype_name . '_fields', false, false, false, 'name');

		foreach ($raw_data as $product){
			if (!$product){ continue; }
			$add_data = array();
			$update = false;
			foreach ($product as $name => $value){
				if (!empty($send_data[$name])){
					if ($send_data[$name] == 'id'){
						if ($this->model->selectOnly('i.id')->getGoods($this->ctype_name, $value)){
							if (empty($data['update'])){
								$add_data = false;
								break;
							} else {
								$update = true;
								$add_data[$send_data[$name]] = $value;
							}
						} else {
							$add_data[$send_data[$name]] = $value;
						}
					} else if ($send_data[$name] == 'artikul'){
						if ($this->model->selectOnly('i.id')->filterEqual('i.artikul', $value)->getGoods($this->ctype_name, 0, 1)){
							if (empty($data['update'])){
								$add_data = false;
								break;
							} else {
								$update = true;
								$add_data[$send_data[$name]] = $value;
							}
						} else {
							$add_data[$send_data[$name]] = $value;
						}
					} else if ($send_data[$name] == 'category_id' && !is_numeric($send_data[$name])){
						$add_data[$send_data[$name]] = $this->setCatImport($value, $data);
					} else if ($send_data[$name] == 'sc_props'){
						$add_data['props'] = $this->setPropsImport($value, $data, $ctype, $add_data);
					} else if ($send_data[$name] == 'photo'){
						$add_data[$send_data[$name]] = $this->setPhotoImport($value, $data);
					} else if(!empty($field_colors[$send_data[$name]])){
						$add_data[$send_data[$name]] = $this->setColorsImport($value, $data);
					} else if(!empty($field_volume[$send_data[$name]])){
						$add_data[$send_data[$name]] = $this->setVolumeImport($value, $send_data[$name], $data);
					} else {
						$add_data[$send_data[$name]] = $value;
					}
				}
			}
			if ($add_data){
				if ($update){
					$add_data['user_id'] = $this->cms_user->id;
					$add_data['user_nickname'] = $this->cms_user->nickname;
					$add_data['ctype_name'] = $ctype['name'];
					$add_data['ctype_id']   = $ctype['id'];
					$add_data['ctype_data'] = $ctype;
					if (!empty($add_data['id'])){
						$goods = $this->model->selectOnly('i.id, i.photo')->getGoods($this->ctype_name, $add_data['id']);
						$add_data = array_merge($goods, $add_data);
						$add_data = cmsEventsManager::hook('content_before_update', $add_data);
						$add_data = cmsEventsManager::hook("content_{$ctype['name']}_before_update", $add_data);
						$add_data = $content_model->updateContentItem($ctype, $goods['id'], $add_data, $fields);
						if ($add_data){
							$updated++;
							if (!empty($goods['photo'])){ $this->photoDelete($goods['photo']); }
							cmsEventsManager::hook('content_after_update', $add_data);
							cmsEventsManager::hook("content_{$ctype['name']}_after_update", $add_data);
						}
					} else if (!empty($add_data['artikul'])){
						$goods = $this->model->filterEqual('i.artikul', $add_data['artikul'])->getGoods($this->ctype_name, 0, 1);
						$add_data = array_merge($goods, $add_data);
						$add_data = cmsEventsManager::hook('content_before_update', $add_data);
						$add_data = cmsEventsManager::hook("content_{$ctype['name']}_before_update", $add_data);
						$add_data = $content_model->updateContentItem($ctype, $goods['id'], $add_data, $fields);
						if ($add_data){
							$add_data['user_id'] = $this->cms_user->id;
							$add_data['user_nickname'] = $this->cms_user->nickname;
							$add_data['ctype_name'] = $ctype['name'];
							$add_data['ctype_id']   = $ctype['id'];
							$add_data['ctype_data'] = $ctype;
							$updated++;
							if (!empty($goods['photo'])){ $this->photoDelete($goods['photo']); }
							cmsEventsManager::hook('content_after_update', $add_data);
							cmsEventsManager::hook("content_{$ctype['name']}_after_update", $add_data);
						}
					}
				} else {
					$add_data['user_id'] = $this->cms_user->id;
					$add_data['user_nickname'] = $this->cms_user->nickname;
					$add_data['ctype_name'] = $ctype['name'];
					$add_data['ctype_id']   = $ctype['id'];
					$add_data['ctype_data'] = $ctype;
					$add_data = cmsEventsManager::hook('content_before_add', $add_data);
					$add_data = cmsEventsManager::hook("content_{$this->ctype_name}_before_add", $add_data);
					$add_data['title'] = !empty($add_data['title']) ? $add_data['title'] : $added;
					$add_data = $content_model->addContentItem($ctype, $add_data, $fields);
					if ($add_data){
						$added++;
						$add_data['user_id'] = $this->cms_user->id;
						$add_data['user_nickname'] = $this->cms_user->nickname;
						$add_data['ctype_name'] = $ctype['name'];
						$add_data['ctype_id']   = $ctype['id'];
						$add_data['ctype_data'] = $ctype;
						$add_data = cmsEventsManager::hook('content_after_add', $add_data);
						$add_data = cmsEventsManager::hook("content_{$this->ctype_name}_after_add", $add_data);
						cmsEventsManager::hook('content_after_add_approve', array('ctype_name' => $this->ctype_name, 'item' => $add_data));
						cmsEventsManager::hook("content_{$this->ctype_name}_after_add_approve", $add_data);
					}
				}
			}
		}

		return $this->cms_template->renderJSON(array('error' => false, 'added' => $added, 'updated' => $updated));

	}
	
	public function setCatImport($value, $data) {
		$cat_sep = !empty($data['cat_sep']) ? $data['cat_sep'] : "///";
		if(preg_match("#{$cat_sep}#", $value)){
			$path = preg_split("#{$cat_sep}#", $value);
			if ($path){
				$i = 0;
				$parent_id = 1;
				foreach ($path as $key => $val){
					$i++;
					if (!$val){ continue; }
					$cat = $this->model->selectOnly('i.id')->getItemByField('con_' . $this->ctype_name . '_cats', 'title', $val);
					if (!empty($cat['id'])) {
						if (count($path) > $i){
							$parent_id = $cat['id'];
						} else {
							return $cat['id'];
						}
					} else {
						if (!empty($data['cat_create'])){
							$add = $this->model->addCategory($this->ctype_name, array(
								'parent_id' => $parent_id,
								'title' => $val
							));
							if (count($path) > $i){
								$parent_id = !empty($add['id']) ? $add['id'] : 1;
							} else {
								return !empty($add['id']) ? $add['id'] : 1;
							}
						} else if (!empty($data['cat_move'])){
							return $data['cat_move'];
						} else {
							return !empty($value) ? $value : 1;
						} 
					}
				}
			}
		}
		return !empty($value) ? $value : 1;
	}
	
	public function setPropsImport($value, $data, $ctype, $add_data) {
		if (empty($ctype['props'])){ return array(); }
		$properties = array();
		$prop_sep = !empty($data['props_sep']) ? $data['props_sep'] : "///";
		if(preg_match("#{$prop_sep}#", $value)){
			$props = preg_split("#{$prop_sep}#", $value);
			if ($props){
				if (!empty($add_data['category_id'])){
					if (!is_numeric($add_data['category_id'])){
						$add_data['category_id'] = $this->setCatImport($add_data['category_id'], $data);
					}
					$ctype['props'] = $this->model->
						selectOnly('i.id, p.title, p.type, p.values, p.id as prop_id')->
						join('con_' . $ctype['name'] . '_props', 'p', 'p.id = i.prop_id')->
						filterEqual('i.cat_id', $add_data['category_id'])->
						orderBy('i.ordering')->
						get('con_' . $ctype['name'] . '_props_bind');
					if (empty($ctype['props'])){ return array(); }
				}
				foreach ($props as $key => $val){
					if (!$val){ continue; }
					if (mb_stripos($val, ':') !== FALSE){
						$str = explode(':', $val);
						if (!empty($str[0])){
							$str[0] = ltrim(preg_replace("/\([^)]+\)\s/", "", $str[0]));
						}
						if (!empty($str[1])){
							$str[1] = ltrim($str[1]);
						}
						if (!empty($str[0]) && !empty($str[1])){
							foreach ($ctype['props'] as $p){
								if ($p['title'] == $str[0]){
									$properties[(!empty($p['prop_id']) ? $p['prop_id'] : $p['id'])] = $str[1];
								}
							}
						}
					}
				}
			}
		} else if(mb_stripos($value, ':') !== FALSE){
			$str = explode(':', $value);
			if (!empty($str[0])){
				$str[0] = ltrim(preg_replace("/\([^)]+\)\s/", "", $str[0]));
			}
			if (!empty($str[1])){
				$str[1] = ltrim($str[1]);
			}
			if (!empty($str[0]) && !empty($str[1])){
				foreach ($ctype['props'] as $p){
					if ($p['title'] == $str[0]){
						$properties[(!empty($p['prop_id']) ? $p['prop_id'] : $p['id'])] = $str[1];
					}
				}
			}
		}
		return $properties;
	}
	
	public function setPhotoImport($value, $data) {
		$img_sep = !empty($data['img_sep']) ? $data['img_sep'] : ",";
		if(preg_match("#{$img_sep}#", $value)){
			$path = preg_split("#{$img_sep}#", $value);
			if ($path){
				$photos = array();
				foreach ($path as $key => $val){
					if (@file_get_contents($val, 0, NULL, 0, 1)) {
						$photos[] = $this->photoUpload($val);
					}
				}
				return $photos;
			}
		} else {
			if (file_exists($value)) {
				return array($this->photoUpload($value));
			} else {
				return null;
			}
		}
	}

	public function photoDelete($imgs) {
		
		if (!empty($imgs)) {
			if (!is_array($imgs)){ $imgs = cmsModel::yamlToArray($imgs); }
			foreach($imgs as $images){
				foreach($images as $image_rel_path){
					files_delete_file($image_rel_path, 2);
				}
			}
        }
	}

	public function photoUpload($img) {
		
		$_POST['photo'] = $img;
		$result = $this->cms_uploader->enableRemoteUpload()->uploadFromLink('photo', 'jpg,jpeg,png,gif,bmp,webp');
		$img = false;
		if($result['success']){
			$img = true;
			if(!$this->cms_uploader->isImage($result['path'])){$img = false;}
		}
		if(!$img || isset($result['error']) && $result['error']){
			if(!empty($result['path'])){
				$this->cms_uploader->remove($result['path']);
			}
		} else {
			$sizes = array('micro', 'small', 'normal', 'big', 'original');
			$result['paths'] = array();
			$presets = cmsCore::getModel('images')->getPresets();
			$result['paths'] = array();
			if (in_array('original', $sizes, true)){
				$result['paths']['original'] = $result['url'];
			}
			foreach($presets as $p){
				if (is_array($sizes) && !in_array($p['name'], $sizes, true)){continue;}
				$path = $this->cms_uploader->resizeImage($result['path'], array(
					'width'     => $p['width'],
					'height'    => $p['height'],
					'is_square' => $p['is_square'],
					'quality'   => (($p['is_watermark'] && $p['wm_image']) ? 100 : $p['quality'])
				));
				if (!$path) { continue; }
				$result['paths'][$p['name']] = $path;
			}
			$img = $result['paths'];
		}
		
		return $img;
		
	}
	
	public function setColorsImport($value, $data) {
		$color_sep = "///";
		if(preg_match("#{$color_sep}#", $value)){
			$path = preg_split("#{$color_sep}#", $value);
			if ($path){
				$values = array();
				$value = '';
				$colors = $this->model->selectOnly('i.id, i.title')->getData('sc_colors', 0, 0, function($item){
					return $item['title'];
				});
				if ($colors){
					foreach($path as $i => $v){
						if (false !== $key = array_search($v, $colors)) {
							$values[] = $key;
						}
					}
				}
				if (!empty($values)){
					if ($colors){
						foreach($colors as $key => $title){
							$value .= in_array($key, $values) ? '1' : '0';
						}
					}
				}
				return $value;
			}
		} else {
			$values = array();
			$colors = $this->model->selectOnly('i.id, i.title')->getData('sc_colors', 0, 0, function($item){
				return $item['title'];
			});
			if ($colors){
				if (false !== $key = array_search($value, $colors)) {
					$values[] = $key;
				}
			}
			$value = '';
			if (!empty($values)){
				if ($colors){
					foreach($colors as $key => $title){
						$value .= in_array($key, $values) ? '1' : '0';
					}
				}
			}
			return $value;
		}
	}
	
	public function setVolumeImport($value, $name, $data) {
		$vol_sep = "///";
		if(preg_match("#{$vol_sep}#", $value)){
			$path = preg_split("#{$vol_sep}#", $value);
			if ($path){
				$value = '';
				$field = $this->model->
					selectOnly('i.id, i.values')->
					filterEqual('i.name', $name)->
					getData('con_' . $this->ctype_name . '_fields', false, true);
				if (empty($field['values'])){ return $value; }
				$volumes = cmsModel::yamlToArray($field['values']);
				$values = array();
				if ($volumes){
					foreach($path as $i => $v){
						if (false !== $key = array_search($v, $volumes)) {
							$values[] = $key;
						}
					}
				}
				if (!empty($values)){
					if ($volumes){
						foreach($volumes as $key => $title){
							$value .= in_array($key, $values) ? '1' : '0';
						}
					}
				}
				return $value;
			}
		} else {
			$field = $this->model->
				selectOnly('i.id, i.values')->
				filterEqual('i.name', $name)->
				getData('con_' . $this->ctype_name . '_fields', false, true);
			if (empty($field['values'])){ return $value; }
			$volumes = cmsModel::yamlToArray($field['values']);
			$values = array();
			if ($volumes){
				if (false !== $key = array_search($value, $volumes)) {
					$values[] = $key;
				}
			}
			$value = '';
			if (!empty($values)){
				if ($volumes){
					foreach($volumes as $key => $title){
						$value .= in_array($key, $values) ? '1' : '0';
					}
				}
			}
			return $value;
		}
	}

}