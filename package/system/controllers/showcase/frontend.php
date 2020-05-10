<?php 
class showcase extends cmsFrontend {
	
	public $useOptions = true;
	public $ctype_name = false;
	public $managers = array(1);
	public $status = array(
		1 => 'Принят',
		2 => 'Обрабатывается',
		3 => 'Доставляется',
		4 => 'Получен',
		5 => 'Отменен'
	);
	
	public function __construct($request) {
        parent::__construct($request);
        $this->ctype_name = !empty($this->options['ctype_name']) ? $this->options['ctype_name'] : false;
        $this->managers = !empty($this->options['managers']) ? $this->options['managers'] : array(1);
    }
	
	public function routeAction($action_name) {

		if ($this->ctype_name && $this->ctype_name != 'showcase'){ return $action_name; }

		if (!$this->isActionExists($action_name)){
			$this->cms_core->uri_controller = 'content';
			$this->cms_core->runController();
			$this->current_params = array();
			return 'exit';
		}

		return $action_name;

	}	
	
	public function actionExit(){ return; }
	
	public function renderCartData($return_data = array()){

		if (!$this->ctype_name){
			if ($this->request->isAjax()){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'not ctype'));
			} else {
				return array() + $return_data;
			}
		}
		
		$session_name = 'sc-' . $this->ctype_name;

		if (!cmsUser::isSessionSet($session_name)){
			if ($this->request->isAjax()){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'empty session'));
			} else {
				return array() + $return_data;
			}
		}
		
		// получаем данные из корзины, которые хранятся в сессии каждого юзера
		$items = cmsUser::sessionGet($session_name);
        if (!$items) { 
			if ($this->request->isAjax()){
				return array(
					'items' => array(),
					'fields' => array(),
					'summ' => 0,
					'count' => 0,
					'ctype_name' => $this->ctype_name
				) + $return_data;
			} else {
				return array('items' => $items) + $return_data;
			}
		}
		$items = array_reverse($items, true);
		
		$model = cmsCore::getModel('content');
		
		// получаем собственные поля с префиксом sc
		$fields = $model->
			orderBy('i.ordering')->
			filterLike('i.type', 'sc%')->
			getContentFields($this->ctype_name);
		
		if (!$fields){ 
			if ($this->request->isAjax()){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'empty fields'));
			} else {
				return array('items' => $items) + $return_data;
			}
		}
		
		$fields = cmsEventsManager::hook('sc_get_fields', $fields);
		if (!$fields){ 
			if ($this->request->isAjax()){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'empty fields'));
			} else {
				return array('items' => $items) + $return_data;
			}
		}

		$summ = 0;
		$count = 0;

		foreach ($items as $id => $item){
			$item_id = $id;
			$variant_id = false;
			if (stripos($id, 'v') !== false){
				list($item_id, $variant_id) = explode('v', $id);
			}
			// получаем товар по ID из сессии
			$goods = $model->
				useCache("content.item.{$this->ctype_name}")->
				getItemById('con_' . $this->ctype_name, $item_id);
			if ($goods){
				$goods['price'] = !empty($goods['sale']) ? $goods['sale'] : $goods['price'];
				if (!empty($goods['variants']) && $variant_id){
					// если есть варианты товара, делаем из них массив
					$variants = cmsModel::yamlToArray($goods['variants']);
					$variant = $this->model->getData('sc_variations', $variant_id);
					if ($variant && $variants && in_array($variant_id, $variants) && (int)$variant['in'] > 0){
						$items[$id]['variant'] = $variant;
						$variant['price'] = !empty($variant['sale']) ? $variant['sale'] : $variant['price'];
						$items[$id]['price'] =!empty($variant['price']) ? $variant['price'] : $goods['price'];
						$goods['title'] = !empty($variant['title']) ? $variant['title'] : $goods['title'];
						$goods['price'] = !empty($variant['price']) ? $variant['price'] : $goods['price'];
						if (!empty($variant['photo'])){
							$photo = cmsModel::yamlToArray($variant['photo']);
							$photo['micro'] = $photo['small'];
							$goods['photo'] = $photo;
						}
						if (empty($this->options['off_inctock']) && (int)$item['qty'] > (int)$variant['in']) { $items[$id]['qty'] = $variant['in']; }
					} else {
						unset($items[$id]);
						continue;
					}
				} else {
					$items[$id]['price'] = (float)$goods['price'];
					if (empty($this->options['off_inctock']) && (int)$items[$id]['qty'] > (int)$goods['in_stock']) {
						$items[$id]['qty'] = $goods['in_stock'];
					}
				}
				$items[$id]['goods'] = $goods;
				if (!empty($items[$id]['price'])){
					// если количество товаров больше 1, тогда умножаем цену на количество
					if (isset($items[$id]['qty']) && (int)$items[$id]['qty'] > 1){
						$items[$id]['price'] = ((float)$items[$id]['price'] * (int)$items[$id]['qty']);
					}
					$summ = $summ + (float)$items[$id]['price'];
				}
				if (isset($items[$id]['qty']) && (int)$items[$id]['qty'] > 1){
					$count = ($count + (int)$items[$id]['qty']);
				} else {
					$count++;
				}
			} else {
				$items[$id] = $item;
				if (!empty($item['price'])){
					$summ = $summ + (float)$item['price'];
				}
				$count++;
			}

		}
		
		$sales = $this->model->filterEqual('i.is_pub', 1)->orderBy('i.start', 'ASC')->getData('sc_sales');
		$current_sale = false;
		$old_summ = $summ;
		if ($sales){
			foreach ($sales as $sale){
				if ($summ >= $sale['start']){
					$current_sale = array('id' => $sale['id']) + $sale;
				}
			}
			if ($current_sale){
				if ($current_sale['type'] == 'prosent'){
					$prosent = ($summ / 100 * (float)$current_sale['sale']);
					$summ = ($summ - $prosent);
				} else {
					$summ = ($summ - (float)$current_sale['sale']);
				}
			}
		}
		
		return array(
			'items' => $items,
			'fields' => $fields,
			'summ' => $summ,
			'sale' => $current_sale ? $current_sale + array('old_summ' => $old_summ) : false,
			'count' => $count,
			'ctype_name' => $this->ctype_name
		) + $return_data;
		
	}
	
	public function getStatuses($id = false){
		return $id ? (!empty($this->status[$id]) ? $this->status[$id] : false) : $this->status;
	}

	public function getCartFieldsType(){
		$types = array(
			'string' => 'Строковое поле',
			'telephone' => 'Номер телефона',
			'select' => 'Список',
			'checkbox' => 'Флаг',
		);
		return $types;
	}

	public function getFormFields($field, $values){
		$file = $this->cms_template->getTplFilePath('controllers/showcase/fields/cart_fields/' . $field['type'] . '.tpl.php', false);
		$tpl = $file ? $field['type'] : 'default';
		return $this->cms_template->renderControllerChild('showcase/fields/cart_fields', $tpl, array(
			'field' => $field,
			'values' => $values
		));
	}

	public function getStepList($only_enabled = true){

		if ($only_enabled){
			$this->model->filterEqual('i.is_pub', 1);
		}
		
		$step = array(0 => array(
			'id' => 0,
			'title' => 'Список товаров корзины',
			'tpl' => 'cart_big',
			'is_pub' => 1,
			'ordering' => 1
		));

		$steps = $this->model->orderBy('i.ordering', 'asc')->getData('sc_steps');

		return $steps ? $step + $steps : $step;

	}
	
	public function getNextStep($current = 0){
		$steps = array_values($this->getStepList(1));
		foreach($steps as $id => $step){
			if ($step['id'] == $current){
				$next = !empty($steps[($id + 1)]) ? $steps[($id + 1)] : array('id' => 'checkout');
				return array(
					'current' => $step,
					'next' => $next
				);
			}
		}
		return array(
			'step' => !empty($steps[$current]) ? $steps[$current] : array('id' => 'checkout'),
			'current' => !empty($steps[$current]) ? $steps[$current] : array('id' => 'checkout'),
		);
	}
	
	public function getArtikulById($item_id, $variant_id = false){
		
		$code = $variant_id ? $item_id . 'v' . $variant_id : $item_id;

		$artikul = strtoupper(hash('adler32', '00000' . $code));
		if (strlen((string)$item_id) == 2){
			$artikul = strtoupper(hash('adler32', '0000' . $code));
		} else if(strlen((string)$item_id) == 3){
			$artikul = strtoupper(hash('adler32', '000' . $code));
		} else if(strlen((string)$item_id) == 4){
			$artikul = strtoupper(hash('adler32', '00' . $code));
		} else if(strlen((string)$item_id) == 5){
			$artikul = strtoupper(hash('adler32', '0' . $code));
		} else if(strlen((string)$item_id) == 6){
			$artikul = strtoupper(hash('adler32', $code));
		}
		
		return $artikul;
		
	}
	
	public function getPriceFormat($price, $iso = false, $return_units = true){
		$units = !empty($this->options['currency']) ? $this->options['currency'] : LANG_CURRENCY;
		if ($iso){
			$currency_iso = !empty($this->options['currency_iso']) ? $this->options['currency_iso'] : 'RUB';
			$units = '<sub itemprop="priceCurrency" content="' . $currency_iso . '"> ' . $units . '</sub>';
		}
		$price_format = !empty($this->options['price_format']) ? $this->options['price_format'] : 1;
		if ($price_format == 1){
			return number_format($price, 0, '', ' ' ) . ' ' . ($return_units ? $units : '');
		} else {
			return $price . ' ' . ($return_units ? $units : '');
		}
	}

	public function sendPayNotices($order_id = false, $summ = 0, $status = false){

		$messenger = cmsCore::getController('messages');
				
		if ($this->managers){
			$text = 'Новая оплата заказа ждет обработки';
			foreach ($this->managers as $idx => $manager){
				if (!$this->model->filterEqual('i.user_id', $manager)->filterEqual('i.content', $text)->getItem('users_notices')){
					$messenger->addRecipient($manager);
				}
			}
			$messenger->sendNoticePM(array(
				'content' => $text,
				'actions' => array(
					'view' => array(
						'title' => LANG_SHOW,
						'href'  => href_to('showcase', 'orders', array($order_id, $status))
					)
				)
			));
		}
		
		if (!empty($this->options['email'])){
			$emails = trim($this->options['email']);
			if (mb_stripos($emails, ',') !== FALSE){
				$emails = explode(',', $emails);
			}
			if ($emails){
				$mail_data = array(
					'order_id' => $order_id,
					'summ' => $summ . ' ' . (!empty($this->options['currency']) ? $this->options['currency'] : LANG_CURRENCY),
					'url' => href_to_abs('showcase', 'orders', array($order_id, $status)),
				);
				if (is_array($emails)){
					foreach ($emails as $email){
						if (!$email){ continue; }
						$to = array('email' => $email, 'name' => 'Администратор');
						$messenger->sendEmail($to, array('name' => 'sc_order_pay'), $mail_data);
					}
				} else {
					$to = array('email' => $emails, 'name' => 'Администратор');
					$messenger->sendEmail($to, array('name' => 'sc_order_pay'), $mail_data);
				}
			}
		}

	}

	public function scUpdateTransaction($transaction, $field, $text){
		if (!empty($transaction[$field])){
			if (!is_array($transaction[$field])){
				$transaction[$field] = cmsModel::yamlToArray($transaction[$field]);
			}
		} else {
			$transaction[$field] = array();
		}
		$transaction[$field][] = $text;
		$this->model->updData('sc_transactions', $transaction['id'], array($field => $transaction[$field]));
		return $transaction;
	}
    
}