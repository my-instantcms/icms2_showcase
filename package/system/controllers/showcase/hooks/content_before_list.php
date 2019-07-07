<?php

class onShowcaseContentBeforeList extends cmsAction {

    public function run($data){
			
		list($ctype, $items) = $data;
		
		if (!$items || $ctype['name'] != $this->ctype_name){ return $data; }

		/* Определяем откуда идет запрос, виджет или страница списка */
		$first = reset($items);

		/* если виджет, туда добавляем fields */
		if ($this->ctype_name == $ctype['name'] && empty($first['fields'])){
			$ctype['sc_fields'] = cmsCore::getModel('content')->getContentFields($ctype['name']);
		}
		
		if (empty($this->options['variants_off'])){
			foreach($items as $id => $item){
				if (empty($item['variants'])){ continue; }
				$variants = is_array($item['variants']) ? $item['variants'] : cmsModel::yamlToArray($item['variants']);
				if ($item['variants']){
					$items[$id]['variants'] = $this->model->
						useCache("showcase.sc_variations")->
						filterIn('i.id', $variants)->
						orderBy('i.ordering', 'ASC')->
						get('sc_variations');
				}
			}
		}

		return array($ctype, $items);

    }

}
