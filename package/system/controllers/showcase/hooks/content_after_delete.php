<?php

class onShowcaseContentAfterDelete extends cmsAction {

    public function run($data){

		if (!empty($data['item']['variants'])){
			$variants = cmsModel::yamlToArray($data['item']['variants']);
			if ($variants){
				$variations = $this->model->
						useCache("showcase.sc_variations")->
						filterEqual('i.ctype_name', $data['ctype_name'])->
						filterEqual('i.item_id', $data['item']['id'])->
						filterIn('i.id', $variants)->
						get('sc_variations');
				if ($variations){
					foreach($variations as $variation){
						$result = $this->model->deleteData('sc_variations', $variation['id']);
						if ($result && !empty($variation['photo']) && !$variation['attached']){
							$photo = cmsModel::yamlToArray($variation['photo']);
							if ($photo){
								foreach($photo as $image_url){
									files_delete_file($image_url, 2);
								}
							}
						}
					}
				}
			}
		}
	
        $this->model->
			filterEqual('i.ctype_name', $data['ctype_name'])->
			deleteData('sc_variations', $data['item']['id'], 'item_id');
			
		$is_manager = (in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);

        if ($is_manager && !empty($this->options['log'])) {
			$author = '<a href="' . href_to('users', $this->cms_user->id) . '" target="_blank">' . $this->cms_user->nickname . '</a>';
			$title = '<b data-toggle="tooltip" data-placement="top" title="' . $data['item']['title'] . '">материал</b>';
			$this->model->saveData('sc_logs', array(
				'style' => 'danger',
				'icon' => 'glyphicon glyphicon-trash',
				'text' => $author . ' удалил ' . $title
			));
		}

        return $data;

    }

}
