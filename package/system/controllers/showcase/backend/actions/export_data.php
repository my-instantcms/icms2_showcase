<?php

class actionShowcaseExportData extends cmsAction {

	public function run() {
		
		if (!$this->request->has('send_data')){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Нет полей для экспорта'));
		}

		$send_data = $this->request->get('send_data', array());

		if (!$send_data || !$send_data['export_select']){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Нет полей для экспорта'));
		}
		
		$field_colors = $this->model->
			selectOnly('i.name')->
			filterEqual('i.type', 'sccolor')->
			getData('con_' . $this->ctype_name . '_fields', false, false, false, 'name');

		$field_volume = $this->model->
			selectOnly('i.name')->
			filterEqual('i.type', 'scvolume')->
			getData('con_' . $this->ctype_name . '_fields', false, false, false, 'name');

		$selects = 'i.id';
		$titles = array();
		foreach ($send_data['export_select'] as $field){
			if ($field == 'id'){
				$titles[] = $field;
				continue;
			}
			if ($field == 'sc_props'){
				continue;
			}
			$selects .= ', i.' . $field;
			$titles[] = $field;
		}
		
		if(in_array('sc_props', $send_data['export_select'])){
			$titles[] = 'sc_props';
		}

		if (!empty($send_data['cat_id'])){
			$category = $this->model->getCategory($this->ctype_name, $send_data['cat_id']);
			$this->model->filterCategory($this->ctype_name, $category, true);
		}

		$items = $this->model->selectOnly($selects)->get('con_' . $this->ctype_name);
		if ($items){
			$file = $this->cms_config->upload_path . 'export/export.csv';
			$fp = fopen($file, 'w');
			fputcsv($fp, $titles, (!empty($send_data['sep']) ? $send_data['sep'] : ';'));
			foreach($items as $item_id => $item){
				if (!empty($item['id']) && !in_array('id', $titles)){ unset($item['id']); }
				foreach($item as $key => $value){
					if ($key == 'content'){
						$item['content'] = html_minify($value);
					} else if($key == 'category_id'){
						$item['category_id'] = $this->getCatPath($value, $send_data);
					} else if($key == 'photo'){
						$item['photo'] = $this->getPhotoPath($value, $send_data);
					} else if($key == 'variants'){
						if ($value){
							$value = str_replace("---\n", "", $value);
							$item['variants'] = $value ? preg_replace("/\n/m", '\n', $value) : null;
						}
					} else if(!empty($field_colors[$key])){
						$item[$key] = !empty($value) ? $this->getColorPath($value, $key, $send_data) : null;
					} else if(!empty($field_volume[$key])){
						$item[$key] = !empty($value) ? $this->getVolumePath($value, $key, $send_data) : null;
					}
				}
				if(in_array('sc_props', $titles)){
					$item['sc_props'] = $this->getPropsPath($item_id, $send_data);
				}
				fputcsv($fp, $item, (!empty($send_data['sep']) ? $send_data['sep'] : ';'));
			}
			fclose($fp);
		}

		return $this->cms_template->renderJSON(array('error' => false, 'href' => $this->cms_config->upload_root . 'export/export.csv'));

	}
	
	public function getCatPath($value, $send_data){
		$current = $this->model->
			selectOnly('i.id, i.title, i.parent_id, i.ns_level')->
			getItemById('con_' . $this->ctype_name . '_cats', $value);
		$path = array();
		if ($current){
			$path[] = $current['title'];
			while(1){
				$parent = $this->model->
					selectOnly('i.id, i.title, i.parent_id, i.ns_level')->
					getItemById('con_' . $this->ctype_name . '_cats', $current['parent_id']);
				if ($parent){
					$path[] = $parent['title'];
					if ($parent['parent_id'] < 2){ break; }
				} else {
					break;
				}
			}
			$path = array_reverse($path);
			return $path ? implode((!empty($send_data['cat_sep']) ? $send_data['cat_sep'] : '///'), $path) : $value;
		}
		return !empty($path) ? $path : '';
	}
	
	public function getPhotoPath($value, $send_data){
		$images = $value ? cmsModel::yamlToArray($value) : false;
		$path = array();
		if ($images){
			foreach($images as $img){
				if (!empty($img['original'])){
					$path[] = $this->cms_config->upload_host_abs . '/' . $img['original'];
				} else {
					$path[] = $this->cms_config->upload_host_abs . '/' . $img['big'];
				}
			}
			return $path ? implode((!empty($send_data['img_sep']) ? $send_data['img_sep'] : ','), $path) : $value;
		}
		return !empty($path) ? $path : '';
	}

	public function getPropsPath($item_id, $send_data){
		$props = $this->model->
			selectOnly('i.*, p.title')->
			join('con_' . $this->ctype_name . '_props', 'p', 'p.id = i.prop_id')->
			filterEqual('i.item_id', $item_id)->
			get('con_' . $this->ctype_name . '_props_values');
		$path = '';
		if ($props){
			foreach ($props as $prop){
				if (empty($prop['title']) || empty($prop['value'])){ continue; }
				$path .= $prop['title'] . ':' . $prop['value'] . (!empty($send_data['props_sep']) ? $send_data['props_sep'] : '///');
			}
		}
		return !empty($path) ? rtrim($path, (!empty($send_data['props_sep']) ? $send_data['props_sep'] : '///')) : '';
	}
	
	public function getColorPath($value, $name, $send_data){
		$items = $this->model->selectOnly('i.id, i.title')->getData('sc_colors');
		$colors = array();
		if ($items){
			$pos = 0;
			foreach ($items as $item){
				if (substr($value, $pos, 1) == 1){
					$colors[$item['id']] = $item['title'];
				}
				$pos++;
				if ($pos+1 > strlen($value)) { break; }
			}
		}
		return !empty($colors) ? implode("///", $colors) : null;
	}
	
	public function getVolumePath($value, $name, $send_data){
		$field = $this->model->
			selectOnly('i.id, i.values')->
			filterEqual('i.name', $name)->
			getData('con_' . $this->ctype_name . '_fields', false, true);
		if (empty($field['values'])){ return null; }
		$items = cmsModel::yamlToArray($field['values']);
		$values = array();
		if ($items){
			$pos = 0;
			foreach ($items as $key => $item){
				if (substr($value, $pos, 1) == 1){
					$values[$key] = $item;
				}
				$pos++;
				if ($pos+1 > strlen($value)) { break; }
			}
		}
		return !empty($values) ? implode("///", $values) : null;
	}

}