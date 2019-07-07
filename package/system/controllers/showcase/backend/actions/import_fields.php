<?php

class actionShowcaseImportFields extends cmsAction {

	public function run() {
		
		$data = cmsUser::isSessionSet('sc_import_data') ? cmsUser::sessionGet('sc_import_data') : false;

		if (!$data) {
			cmsUser::addSessionMessage('Выполните шаг 1', 'error');
			$this->redirectToAction('import');
		}
		
		unset($data['position']);
		if (cmsUser::isSessionSet('sc_import_data')){ cmsUser::sessionUnset('sc_import_data'); }
		cmsUser::sessionSet('sc_import_data', $data);

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

		$item = fgetcsv( $handle, 0, $data['sep']);
		if (false === $item) {
			cmsUser::addSessionMessage('Не удалось читать содержимое', 'error');
			$this->redirectToAction('import');
		}

		fclose($handle);
		
		$fields = $this->model->
			useCache('content.fields.'.$this->ctype_name)->
			selectOnly('i.id, i.name, i.title, i.type, i.values, i.options')->
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
		
		$field_colors = $this->model->
			selectOnly('i.name')->
			filterEqual('i.type', 'sccolor')->
			getData('con_' . $this->ctype_name . '_fields', false, false, false, 'name');

		$field_volume = $this->model->
			selectOnly('i.name')->
			filterEqual('i.type', 'scvolume')->
			getData('con_' . $this->ctype_name . '_fields', false, false, false, 'name');

		return $this->cms_template->render('backend/import/fields', array(
			'data'		=> $data,
			'fields'	=> $fields,
			'columns'	=> $columns,
			'rows'		=> $rows,
			'item'		=> $item,
			'field_colors' => $field_colors,
			'field_volume' => $field_volume
		));

	}

}