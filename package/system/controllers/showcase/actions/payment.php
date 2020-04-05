<?php

class actionShowcasePayment extends cmsAction {

    public function run($order_id = false){
		
		if (!$order_id){ cmsCore::error404(); }
		
		$payment = !empty($this->options['payment']) ? $this->options['payment'] : 'off';
		if (!$payment || $payment == 'off'){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Оплата выключена'));
		}
		
		$billing = false;
		
		if ($payment == 'system'){
			
			$transaction = $this->model->filterEqual('i.order_id', $order_id)->getData('sc_transactions', false, true);
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'history', 'Очередная попытка транзакции');
			} else {
				$tData = array(
					'order_id' => $order_id,
					'system_id' => 0,
					'history' => array('Начало транзакции'),
				);
				$save = $this->model->saveData('sc_transactions', $tData);
				if ($save){
					$transaction = $this->model->getData('sc_transactions', $save);
				}
			}

			$order = $this->model->getData('sc_checkouts', $order_id);
			if (!$order){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Заказ не найден');
				}
				return $this->cms_template->render('payment', array('error' => 'Заказ не найден'));
			}

			if ($order['paid'] == 2){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Заказ уже оплачен');
				}
				return $this->cms_template->render('payment', array('error' => 'Заказ уже оплачен'));
			}
			
			$fields = !empty($order['fields']) ? cmsModel::yamlToArray($order['fields']) : false;
			if (!$fields){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Поля не заполнены');
				}
				return $this->cms_template->render('payment', array('error' => 'Поля не заполнены'));
			}

			if (isset($fields['payment_system']) && $fields['payment_system'] == 0){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Выбран способ оплаты "Наличные"');
				}
				return $this->cms_template->render('payment', array('error' => 'Выбран способ оплаты "Наличные"'));
			} else if (empty($fields['payment_system'])){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не выбрана');
				}
				return $this->cms_template->render('payment', array('error' => 'Система оплаты не выбрана'));
			}

			if (isset($fields['payment_system']) && $fields['payment_system'] == 999){
				$system = $this->model->getCheckPaySystem();
			} else {
				$system = $this->model->
					selectOnly('i.*, p.file_view, p.file_redirect')->
					joinLeft('sc_pay_gateways', 'p', 'p.name=i.gateway_name')->
					filterEqual('i.is_pub', 1)->
					getData('sc_pay_systems', $fields['payment_system']);
			}

			if (!$system){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не найдена или отключена');
				}
				return $this->cms_template->render('payment', array('error' => 'Система оплаты не найдена или отключена'));
			}
			
			if ($transaction){
				$this->model->updData('sc_transactions', $transaction['id'], array('system_id' => $system['id']));
			}
			
			if (!empty($system['file_redirect'])){
				$this->runAction($system['file_redirect'], array($order_id, $system['id']));
				return;
			}

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
			
			if (!$order['user_id']){
				if (!empty($fields['email'])){
					$hash = hash("adler32", $order['id'] . $fields['email']);
				}
			}
			
			$tpl = !empty($system['file_view']) ? $system['file_view'] : 'payment';

			return $this->cms_template->render($tpl, array(
				'order' => $order,
				'system' => $system,
				'transaction' => $transaction,
				'hash' => isset($hash) ? '?access=' . $hash : '',
				'error' => false
			));
			
			
		} else if ($payment == 'billing'){
			$billing = cmsCore::isControllerExists('billing') ? cmsCore::getController('billing') : false;
			if (!$billing){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Компонент биллинг не найден'));
			}
		}

		$order = $this->model->getData('sc_checkouts', $order_id);
		if (!$order){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Заказ не найден'));
		}

		if ($order['paid'] == 2){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Заказ уже оплачен'));
		}
		
		if ($this->cms_user->balance < (int)$order['price']){
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Недостаточна средств на счету'));
		}
		
		if ($billing){
			$billing->decrementUserBalance($this->cms_user->id, $order['price'], array(
				'text' => 'Оплата заказа №' . $order['id'],
				'url' => href_to('showcase', 'orders', array($order['id'], $order['status']))
			));
			$this->model->updData('sc_checkouts', $order_id, array('paid' => 2));
			$this->sendPayNotices($order['id'], $order['price'], $order['status']);
			return $this->cms_template->renderJSON(array('error' => false, 'message' => 'Заказ успешно оплачен'));
		}
		
		return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Неизвестная ошибка'));

    }

}
