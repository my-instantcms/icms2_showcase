<?php

class actionShowcaseOrderEdit extends cmsAction {

    public function run($id = false){
		
		if (!$id){ cmsCore::error404(); }
		
		if(!$this->cms_user->is_logged){
			cmsUser::goLogin();
		}
		
		$is_manager = ($this->cms_user->id && in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);
			
		$order = $this->model->getData('sc_checkouts', $id);
		if (!$order || !$is_manager){ cmsCore::error404(); }
		
		if (empty($this->options['payment']) || $this->options['payment'] == 'off'){
			unset($order['paid']);
		}
		
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

		$titles = array(
			'id' => '№ заказа',
			'price' => 'Цена',
			'sale_id' => 'Скидка',
			'user_id' => 'Покупатель',
			'fields' => 'Данные',
			'delivery' => 'Доставка',
			'coupon' => 'Купон',
			'paid' => 'Оплата',
			'date' => 'Дата заказа',
			'status' => 'Статус',
			'items' => 'Товары'
		);
		
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
		
		$deliverys = array(0 => array('Курьерская доставка'));
		$delivery = $this->model->
			filterEqual('i.is_pub', 1)->
			orderBy('i.ordering', 'asc')->
			getData('sc_cart_delivery');
		
		if ($delivery){
			foreach ($delivery as $deli){
				if ($deli['type'] != 'courier'){ continue; }
				$deliverys[$deli['id']] = $deli['title'];
			}
			$deliverys[9999] = array('Самовывоз');
			foreach ($delivery as $deli){
				if ($deli['type'] == 'courier'){ continue; }
				$deliverys[$deli['id']] = $deli['title'];
			}
		}

		if (!empty($this->options['payment']) && $this->options['payment'] == 'system' && isset($order['fields']['payment_system'])){
			if ($order['fields']['payment_system'] == 0 && !empty($this->options['system_pay_cash'])){
				$system = $this->model->getCashPaySystem();
			} else if ($order['fields']['payment_system'] == 999 && !empty($this->options['system_pay_check'])){
				$system = $this->model->getCheckPaySystem();
			} else {
				$system = $this->model->
					filterEqual('i.is_pub', 1)->
					getData('sc_pay_systems', $order['fields']['payment_system']);
			}
		}

		if (!empty($order['sale_id'])){
			$sale = $this->model->getData('sc_sales', $order['sale_id']);
		}
		
		$sales = $this->model->selectOnly('i.id, i.title')->orderBy('i.start', 'ASC')->getData('sc_sales');
		$users = $this->model->useCache('users.list')->selectOnly('i.id, i.nickname')->get('{users}');
		
		$goods = $this->model->getGoods($this->ctype_name);
		$variants = $this->model->getData('sc_variations');

		return $this->cms_template->render('order_edit', array(
			'order' => $order,
			'titles' => $titles,
			'cart_fields' => isset($cart_fields) ? $cart_fields : false,
			'system' => isset($system) ? $system : false,
			'fields' => $fields,
			'is_manager' => $is_manager,
			'status' => $this->getStatuses(),
			'sale' => isset($sale) ? $sale : false,
			'deliverys' => $deliverys,
			'sales' => $sales,
			'users' => $users,
			'goods' => $goods,
			'variants' => $variants,
		));

    }

}
