<?php

class actionShowcasePrint extends cmsAction {

    public function run($id = false){
		
		if (!$id || !is_numeric($id)){ cmsCore::error404(); }
		
		$is_manager = ($this->cms_user->id && in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);
		if (!$is_manager){ dump(LANG_ACCESS_DENIED); }
			
		$order = $this->model->getData('sc_checkouts', $id);
		if (!$order){ cmsCore::error404(); }
			
			if (!empty($order['fields'])){
				$order['fields'] = cmsModel::yamlToArray($order['fields']);
				$cart_fields = $this->model->
					selectOnly('i.name, i.title, i.options, i.type')->
					filterEqual('i.is_pub', 1)->
					orderBy('i.ordering', 'asc')->
					getData('sc_cart_fields', false, false, function($item){
						$item['options'] = !empty($item['options']) ? string_explode_list($item['options']) : false;
						return $item;
					}, 'name');
			}
			
			$fields = cmsCore::getModel('content')->
				orderBy('i.ordering')->
				filterLike('i.type', 'sc%')->
				getContentFields($this->ctype_name);
		
			$fields = cmsEventsManager::hook('sc_get_fields', $fields);

			if (!empty($order['goods'])){
				$goods = cmsModel::yamlToArray($order['goods']);
				if ($goods){
					foreach($goods as $index => $good){
						$item_id = $index;
						$variant_id = false;
						if (stripos($index, 'v') !== false){
							list($item_id, $variant_id) = explode('v', $index);
						}
						if ($variant_id){
							$variant = $this->model->getData('sc_variations', $variant_id);
							if ($variant){
								unset($variant['id'], $variant['ctype_name'], $variant['id'], $variant['item_id'], $variant['price'], $variant['in']);
								$good['variant'] = $variant;
							}
						}
						if (!empty($good['ctype_name']) && $item_id){
							$item = $this->model->
								useCache("content.item.{$good['ctype_name']}")->
								selectOnly('i.id, i.title, i.slug, i.artikul')->
								getItemById('con_' . $good['ctype_name'], $item_id);
							if ($item){
								unset($good['item_id']);
								$item['title'] = !empty($good['variant']['title']) ? $good['variant']['title'] : $item['title'];
								$order['items'][$index] = $item + $good;
							}
						}
					}
				}
				unset($order['goods']);
			}
			
			if (!empty($this->options['payment']) && $this->options['payment'] == 'system' && !empty($order['fields']['payment_system'])){
				$system = $this->model->
					filterEqual('i.is_pub', 1)->
					getData('sc_pay_systems', $order['fields']['payment_system']);
			}

			$html = $this->cms_template->render('print', array(
				'order' => $order,
				'cart_fields' => isset($cart_fields) ? $cart_fields : false,
				'system' => isset($system) ? $system : false,
				'fields' => $fields,
				'is_manager' => $is_manager,
				'status' => $this->getStatuses(),
			), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));
			
			echo $html;
			die();
		
    }

}
