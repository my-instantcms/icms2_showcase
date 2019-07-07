<?php

class actionShowcaseFormVariations extends cmsAction {

    public function run($item_id = false, $id = false, $delete = false){
		
		if ($delete){
			$variation = $this->model->
				useCache("showcase.sc_variations")->
				filterEqual('i.id', $delete)->
				getItem('sc_variations');
			if ($variation){
				$result = $this->model->deleteData('sc_variations', $delete);
				if ($result){
					$is_manager = (in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);
					if ($is_manager && !empty($this->options['log'])) {
						$author = '<a href="' . href_to('users', $this->cms_user->id) . '" target="_blank">' . $this->cms_user->nickname . '</a>';
						$title = '<b data-toggle="tooltip" data-placement="top" title="' . $variation['title'] . '">вариант</b>';
						$this->model->saveData('sc_logs', array(
							'style' => 'danger',
							'icon' => 'glyphicon glyphicon-trash',
							'text' => $author . ' удалил ' . $title . ' товара'
						));
					}
					if (!empty($variation['photo']) && !$variation['attached']){
						$photo = cmsModel::yamlToArray($variation['photo']);
						if ($photo){
							foreach($photo as $image_url){
								files_delete_file($image_url, 2);
							}
						}
					}
				}
			}
			return $this->cms_template->renderJSON(array('error' => false));
		}
		
		if ($id){
			$variations = $this->model->
				useCache("showcase.sc_variations")->
				filterEqual('i.id', $id)->
				getItem('sc_variations');
		}
		
		if (!$this->request->isAjax()){ cmsCore::error404(); }
		
		$item = $item_id ? $this->model->getItemById('con_' . $this->ctype_name, $item_id) : false;
		if (!$item){ 
			$item = array(
				'photo' => '',
				'title' => '',
				'price' => ''
			);
		}
		
		$fields = cmsCore::getModel('content')->
					orderBy('i.ordering')->
					filterLike('i.type', 'sc%')->
					getContentFields($this->ctype_name);

		$fields = cmsEventsManager::hook('sc_get_fields', $fields);
		if (!$fields){ cmsCore::error404(); }
		
        return $this->cms_template->render('fields/form_variations', array(
			'fields' => $fields,
			'variations' => isset($variations) ? $variations : false,
			'item' => $item,
			'id' => $id,
			'item_id' => $item_id
		));

    }

}
