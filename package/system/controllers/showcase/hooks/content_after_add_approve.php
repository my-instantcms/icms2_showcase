<?php

class onShowcaseContentAfterAddApprove extends cmsAction {

    public function run($data){
			
		$ctype_name = $data['ctype_name'];
		$item = $data['item'];

		if ($this->ctype_name == $ctype_name){

			if (empty($item['artikul'])){
				$artikul = $this->getArtikulById($item['id'], false);
				if (!$this->model->db->isFieldExists('con_' . $ctype_name, 'artikul')){
					$this->model->db->query("ALTER TABLE {#}con_{$ctype_name} ADD `artikul` VARCHAR(20) NULL DEFAULT NULL");
				}
				$this->model->update('con_' . $ctype_name, $item['id'], array('artikul' => $artikul));
				$item['artikul'] = $artikul;
				cmsCache::getInstance()->clean('content.list.' . $ctype_name);
				cmsCache::getInstance()->clean('content.item.' . $ctype_name);
			}
			
			if (!empty($item['variants'])){
				$variants = cmsModel::yamlToArray($item['variants']);
				if ($variants){
					$variations = $this->model->
							useCache("showcase.sc_variations")->
							filterIn('i.id', $variants)->
							get('sc_variations');
					if ($variations){
						foreach($variations as $variation){
							$arrays = array(
								'item_id' => $item['id'],
								'artikul' => $this->getArtikulById($item['id'], $variation['id'])
							);
							$this->model->updData('sc_variations', $variation['id'], $arrays);
						}
					}
				}
			}
			
			if (!empty($this->options['log'])){
				$author = '<a href="' . href_to('users', $item['user_id']) . '" target="_blank">' . (!empty($item['user_nickname']) ? $item['user_nickname'] : $item['user_id']) . '</a>';
				$title = '<a href="' . href_to($ctype_name, $item['slug'] . '.html') . '" target="_blank" data-toggle="tooltip" data-placement="top" title="' . $item['title'] . '">товар</a>';
				$this->model->saveData('sc_logs', array(
					'style' => 'info',
					'icon' => 'glyphicon glyphicon-plus',
					'text' => $author . ' добавил ' . $title
				));
			}

		}

		return array('ctype_name' => $ctype_name, 'item' => $item);

    }

}
