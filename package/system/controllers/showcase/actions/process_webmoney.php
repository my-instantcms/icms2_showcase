<?php

class actionShowcaseProcessWebmoney extends cmsAction {
	
	public $gateway_name = 'webmoney';

    public function run(){
		
		$data = json_encode($_POST);
		$fp = fopen('log.txt', 'a+');
		fwrite($fp, $data . "\n");
		fclose($fp);
		
		if (empty($_POST)){
			dump('$_POST данные отсутствует');
		}
		
		if (!empty($_POST['LMI_PREREQUEST']) && $_POST['LMI_PREREQUEST'] == 1) {
			$order_id = false;
			$transaction = false;
			$errors = false;
			if (!empty($_POST['LMI_PAYMENT_NO'])){
				$order_id = $_POST['LMI_PAYMENT_NO'];
			}
			if ($order_id){
				$transaction = $order_id ? $this->model->filterEqual('i.order_id', $order_id)->getData('sc_transactions', false, true) : false;
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'history', 'Первый ответ от Result URL');
				} else {
					$tData = array(
						'order_id' => $order_id ? $order_id : 0,
						'system_id' => 0,
						'history' => array('Первый ответ от Result URL')
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
						echo 'Заказ не найден';
						die();
					}
				}
			} else {
				$tData = array(
					'order_id' => 0,
					'system_id' => 0,
					'errors' => array('$order_id не найден'),
				);
				$save = $this->model->saveData('sc_transactions', $tData);
				if ($save){
					$transaction = $this->model->getData('sc_transactions', $save);
				}
				echo '$order_id не найден';
				die();
			}

			if (!empty($order['fields'])){
				$fields = cmsModel::yamlToArray($order['fields']);
				if (!$fields){
					if ($transaction){
						$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Поля не заполнены');
					}
					echo 'Поля не заполнены';
					die();
				}
				
				if (isset($fields['payment_system']) && $fields['payment_system'] == 0){
					if ($transaction){
						$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Выбран способ оплаты "Наличные"');
					}
					echo 'Выбран способ оплаты "Наличные"';
					die();
				} else if (empty($fields['payment_system'])){
					if ($transaction){
						$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не выбрана');
					}
					echo 'Система оплаты не выбрана';
					die();
				}
			}
			$system = !empty($fields['payment_system']) ? $this->model->
				filterEqual('i.is_pub', 1)->
				getData('sc_pay_systems', $fields['payment_system']) : false;
			if (!$system){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не найдена или отключена');
					echo 'Система оплаты не найдена или отключена';
					die();
				}
			}
			if (empty($system['wallet_id']) || trim($_POST['LMI_PAYEE_PURSE']) != $system['wallet_id']) {
				echo 'Кошелек получателя не совпадает';
				die();
			}
			/***Проверить сумму платежа | Проверить кошелек продавца****/
			echo 'YES';
			die();
		}
		
		if (!empty($_POST['LMI_MODE']) && $_POST['LMI_MODE'] == 1){
			$messenger = cmsCore::getController('messages');
			$messenger->addRecipient(1)->sendNoticePM(array(
				'content' => 'Тест оплаты: цена = ' . $_POST['LMI_PAYMENT_AMOUNT'] . ', дата = ' . (!empty($_POST['LMI_SYS_TRANS_DATE']) ? $_POST['LMI_SYS_TRANS_DATE'] : '') . ', № заказа = ' . $_POST['LMI_PAYMENT_NO'] . ', отправитель = ' . $_POST['LMI_PAYER_PURSE']
			));
		}
		
		$order_id = false;
		$transaction = false;
		$errors = false;
		if (!empty($_POST['LMI_PAYMENT_NO'])){
			$order_id = $_POST['LMI_PAYMENT_NO'];
		}
		
		if ($order_id){
			$transaction = $order_id ? $this->model->filterEqual('i.order_id', $order_id)->getData('sc_transactions', false, true) : false;
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'history', 'Получен ответ перед выполнением платежа');
			} else {
				$tData = array(
					'order_id' => $order_id ? $order_id : 0,
					'system_id' => 0,
					'history' => array('Получен ответ перед выполнением платежа'),
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
					$errors['order'] = 'Заказ не найден';
				}
			}
		} else {
			$tData = array(
				'order_id' => 0,
				'system_id' => 0,
				'errors' => array('$order_id не найден'),
			);
			$save = $this->model->saveData('sc_transactions', $tData);
			if ($save){
				$transaction = $this->model->getData('sc_transactions', $save);
			}
			$errors['order_id'] = '$order_id не найден';
		}
		
		if ($transaction){
			$this->model->updData('sc_transactions', $transaction['id'], array(
				'response' => !empty($transaction['response']) ? $transaction['response'] . "\n" . json_encode($_POST) : json_encode($_POST),
				'price' => !empty($_POST['LMI_PAYMENT_AMOUNT']) ? $_POST['LMI_PAYMENT_AMOUNT'] : 0
			));
		}

		if (!empty($order['fields'])){
			$fields = cmsModel::yamlToArray($order['fields']);
			if (!$fields){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Поля не заполнены');
				}
				$errors['fields'] = 'Поля не заполнены';
			}
			
			if (isset($fields['payment_system']) && $fields['payment_system'] == 0){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Выбран способ оплаты "Наличные"');
				}
				$errors['payment_system'] = 'Выбран способ оплаты "Наличные"';
			} else if (empty($fields['payment_system'])){
				if ($transaction){
					$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не выбрана');
				}
				$errors['payment_system'] = 'Система оплаты не выбрана';
			}
		}
		$system = !empty($fields['payment_system']) ? $this->model->
			filterEqual('i.is_pub', 1)->
			getData('sc_pay_systems', $fields['payment_system']) : false;
		if (!$system){
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не найдена или отключена');
				$errors['system'] = 'Система оплаты не найдена или отключена';
			}
		}
		
		if ($transaction && !empty($system['id'])){
			$this->model->updData('sc_transactions', $transaction['id'], array('system_id' => $system['id']));
		}
			
		$hash = hash('sha256', $_POST['LMI_PAYEE_PURSE'].
			$_POST['LMI_PAYMENT_AMOUNT'].
			$_POST['LMI_PAYMENT_NO'].
			$_POST['LMI_MODE'].
			(!empty($_POST['LMI_SYS_INVS_NO']) ? $_POST['LMI_SYS_INVS_NO'] : '').
			(!empty($_POST['LMI_SYS_TRANS_NO']) ? $_POST['LMI_SYS_TRANS_NO'] : '').
			(!empty($_POST['LMI_SYS_TRANS_DATE']) ? $_POST['LMI_SYS_TRANS_DATE'] : '').
			$system['secret_key'].
			$_POST['LMI_PAYER_PURSE'].
			$_POST['LMI_PAYER_WM']);

		if (empty($_POST['LMI_HASH']) || !empty($_POST['LMI_HASH']) && strtoupper($hash) != $_POST['LMI_HASH']) {
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Хэш не совпадает (' . (!empty($_POST['LMI_HASH']) ? $_POST['LMI_HASH'] : '') . ' != ' . strtoupper($hash) . ')');
				$errors['hash'] = 'Хэш не совпадает';
			}
		}

		if (!empty($_POST['LMI_HOLD'])) {
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Оплата с кодом протекции');
				$errors['hold'] = 'Оплата с кодом протекции';
			}
		}
		
		/***Проверить сумму платежа | Проверить кошелек продавца****/
		
		$transaction = $this->scUpdateTransaction($transaction, 'history', 'Проверка данных перед оплатой завершены');
		
		if (!$errors){
			echo 'YES';
		} else {
			dump($errors);
		}

    }

}
