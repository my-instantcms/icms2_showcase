<?php

class actionShowcaseExport extends cmsAction {

	public function run() {

		$fields = $this->model->
			useCache('content.fields.'.$this->ctype_name)->
			selectOnly('i.id, i.name, i.title, i.type, i.values, i.options')->
			filterNotEqual('i.name', 'user')->
			get('con_' . $this->ctype_name . '_fields', function($field){
				$field['options'] = cmsModel::yamlToArray($field['options']);
				$field['values'] = cmsModel::yamlToArray($field['values']);
				return $field;
			}, 'name');

		$columns = array();
		
		$result = $this->model->db->query("SHOW COLUMNS FROM `{#}con_{$this->ctype_name}`");

        while($col = $this->model->db->fetchAssoc($result)){
			if (!empty($fields[$col['Field']])){ continue; }
			if ($col['Field'] == 'id'){ continue; }
            $columns[$col['Field']] = $col['Field'];
        }

		$default_fields = array(
			'id' => 'ID',
			'artikul' => 'Артикул',
			'category_id' => 'Категория',
			'sc_props' => '[*Свойства*]'
		);
		$fields_list = ($default_fields + array_collection_to_list($fields, 'name', 'title'));
		$fields_list = ($fields_list + $columns);
		
		unset($fields_list['variants']);
		
		$cats = $this->model->getCategoriesTree($this->ctype_name);
		$cats_list = array('' => '');
		if ($cats){
			foreach($cats as $cat){
				if ($cat['ns_level'] > 1){
					$cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
				}
				$cats_list[$cat['id']] = $cat['title'];

			}
		}
		
		
		return $this->cms_template->render('backend/import/export', array(
			'fields' 		=> $fields,
			'columns' 		=> $columns,
			'fields_list' 	=> $fields_list,
			'cats_list' 	=> $cats_list
		));

	}

}