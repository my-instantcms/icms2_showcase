<?php

class onShowcaseContentBeforeItem extends cmsAction {

    public function run($data){
			
		list($ctype, $item, $fields) = $data;

		if ($this->ctype_name == $ctype['name']){
			
			if (!empty($item['variants']) && empty($this->options['variants_off'])){
				
				$variants = cmsModel::yamlToArray($item['variants']);
				$item['seted_variant'] = '';
				
				if ($variants){
					$item['variations'] = $this->model->
							useCache("showcase.sc_variations")->
							filterEqual('i.ctype_name', $this->ctype_name)->
							filterEqual('i.item_id', $item['id'])->
							filterIn('i.id', $variants)->
							orderBy('i.ordering', 'ASC')->
							get('sc_variations');
				}
				
				if ($this->cms_core->request->has('variant')){
					$v = $this->cms_core->request->get('variant', '');
					$item['seted_variant'] = $v;
					if ($v && !empty($item['variations'][$v])){
						$item['title'] = !empty($item['variations'][$v]['title']) ? $item['variations'][$v]['title'] : $item['title'];
						$item['price'] = !empty($item['variations'][$v]['price']) ? $item['variations'][$v]['price'] : $item['price'];
						$item['variant_photo'] = !empty($item['variations'][$v]['photo']) ? $item['variations'][$v]['photo'] : 0;
						$item['variant_attached'] = !empty($item['variations'][$v]['attached']) ? 1 : 0;
						$item['variant_id'] = $v;
						$item['sale'] = (!empty($item['variations'][$v]['sale']) && $item['variations'][$v]['sale'] > 0) ? $item['variations'][$v]['sale'] : null;
						$item['variant_in'] = !empty($item['variations'][$v]['in']) ? $item['variations'][$v]['in'] : 'none';
						$item['in_stock'] = !empty($item['variations'][$v]['in']) ? $item['variations'][$v]['in'] : $item['in_stock'];
					}
				}

			}
			
			$ctype['showcase'] = $this->options;
			$ctype['sc_tabs'] = $this->model->
				useCache("showcase.sc_tabs")->
				filterEqual('i.is_pub', 1)->
				orderBy('i.ordering', 'ASC')->
				get('sc_tabs');
			
		}

		return array($ctype, $item, $fields);

    }

}
