<?php

class actionShowcaseSuccessYandex extends cmsAction {
	
	public $gateway_name = 'yandex';

    public function run(){
		
		if (!empty($_POST['operation_id']) && $_POST['operation_id'] == 'test-notification'){
			$messenger = cmsCore::getController('messages');
			$messenger->addRecipient(1)->sendNoticePM(array(
				'content' => 'Тест уведомления: цена = ' . $_POST['amount'] . ', дата = ' . $_POST['datetime'] . ', код протекции = ' . $_POST['codepro'] . ', отправитель = ' . $_POST['sender'] . ', валюта = ' . $_POST['currency']
			));
		}
		
		$order_id = false;
		$transaction = false;
		$errors = false;
		if (!empty($_POST['label'])){
			$order_id = $_POST['label'];
		}
		
		if ($order_id){
			$transaction = $order_id ? $this->model->filterEqual('i.order_id', $order_id)->getData('sc_transactions', false, true) : false;
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'history', 'Получен ответ от платежной системы');
			} else {
				$tData = array(
					'order_id' => $order_id ? $order_id : 0,
					'system_id' => 0,
					'history' => array('Получен ответ от платежной системы'),
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
					$errors = true;
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
			$errors = true;
		}
		
		if ($transaction){
			$this->model->updData('sc_transactions', $transaction['id'], array(
				'response' => !empty($transaction['response']) ? $transaction['response'] . "\n" . json_encode($_POST) : json_encode($_POST),
				'price' => !empty($_POST['amount']) ? $_POST['amount'] : 0
			));
		}
		
		$system = $this->model->
			filterEqual('i.is_pub', 1)->
			filterEqual('i.gateway_name', $this->gateway_name)->
			getData('sc_pay_systems', false, true);
		if (!$system){
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не найдена или отключена');
				$errors = true;
			}
		}
		
		if ($transaction && !empty($system['id'])){
			$this->model->updData('sc_transactions', $transaction['id'], array('system_id' => $system['id']));
		}
		
		$hash = sha1($_POST['notification_type'] . '&' .
			$_POST['operation_id'] . '&'.
			$_POST['amount'] . '&' .
			$_POST['currency'] . '&' .
			$_POST['datetime'] . '&' .
			$_POST['sender'] . '&' .
			$_POST['codepro'] . '&' .
			$system['secret_key'] . '&' .
			$_POST['label']);

		if (empty($_POST['sha1_hash']) || $_POST['sha1_hash'] != $hash) {
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Хэш не совпадает (' . $_POST['sha1_hash'] . ' != ' . $hash . ')');
				$errors = true;
			}
		}

		if (empty($_POST['codepro']) || $_POST['codepro'] === true) {
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Оплата с кодом протекции');
				$errors = true;
			}
		}

		if (!empty($_POST['unaccepted']) && $_POST['unaccepted'] === true) {
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Перевод не зачислен на счет продавца');
				$errors = true;
			}
		}
		
		if (!empty($_POST['currency']) && !empty($system['currency']) && $_POST['currency'] != $system['currency']) {
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Валюта не совпадает');
				$errors = true;
			}
		}
		
		$price = mb_stripos($order['price'], '.') ? (float)$order['price'] : (float)$order['price'] . '.00';
		$amount = mb_stripos($_POST['amount'], '.') ? (float)$_POST['amount'] : (float)$_POST['amount'] . '.00';

		if (empty($_POST['amount']) || !empty($_POST['amount']) && !empty($order['price']) && $amount != $price) {
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Не корректная цена ' . $amount . ' != ' . $price);
				$errors = true;
			}
		}
		
		if ($order_id && !$errors){
			$this->model->updData('sc_checkouts', $order_id, array('paid' => 2));
			cmsEventsManager::hook('sc_success_paid', array($order, $system, $transaction));
		}
		if (!empty($order)){
			$this->sendPayNotices($order['id'], $order['price'], $order['status']);
		}
		
		if ($transaction && !$errors){
			$transaction = $this->scUpdateTransaction($transaction, 'history', 'Операция прошла успешно');
		}

    }

}
