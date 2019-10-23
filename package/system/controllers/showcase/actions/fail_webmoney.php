<?php

class actionShowcaseFailWebmoney extends cmsAction {
	
	public $gateway_name = 'webmoney';

    public function run(){
		
		$data = json_encode($_POST);
		$fp = fopen('fail.txt', 'a+');
		fwrite($fp, $data . "\n");
		fclose($fp);
		
		$order_id = false;
		$transaction = false;
		$errors = false;
		if (!empty($_POST['LMI_PAYMENT_NO'])){
			$order_id = $_POST['LMI_PAYMENT_NO'];
		}
		
		if ($order_id){
			$transaction = $order_id ? $this->model->filterEqual('i.order_id', $order_id)->getData('sc_transactions', false, true) : false;
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'history', 'Получен ответ неудачного платежа');
			} else {
				$tData = array(
					'order_id' => $order_id ? $order_id : 0,
					'system_id' => 0,
					'history' => array('Получен ответ неудачного платежа'),
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
				'response' => !empty($transaction['response']) ? $transaction['response'] . "\n" . json_encode($_POST) : json_encode($_POST)
			));
		}
		
		$system = $this->model->
			filterEqual('i.is_pub', 1)->
			filterEqual('i.gateway_name', $this->gateway_name)->
			getData('sc_pay_systems', false, true);
		if (!$system){
			if ($transaction){
				$transaction = $this->scUpdateTransaction($transaction, 'errors', 'Система оплаты не найдена или отключена');
				$errors['system'] = 'Система оплаты не найдена или отключена';
			}
		}
		
		if ($transaction && empty($transaction['system_id']) && !empty($system['id'])){
			$this->model->updData('sc_transactions', $transaction['id'], array('system_id' => $system['id']));
		}
		
		if ($transaction && !$errors){
			$transaction = $this->scUpdateTransaction($transaction, 'history', 'Операция прошла неудачно');
		}
		
		cmsUser::addSessionMessage('Оплата не произведена', 'error');
		if ($errors){
			foreach ($errors as $type => $error){
				cmsUser::addSessionMessage($error, 'error');
			}
		}
		
		if (!$order['user_id']){
			$cart_fields = !empty($order['fields']) ? cmsModel::yamlToArray($order['fields']) : false;
			if (!empty($cart_fields['email'])){
				$access = hash("adler32", $order['id'] . $cart_fields['email']);
			}
		}

		$this->redirectTo('showcase', 'orders', array($order['id'], $order['status'] . (isset($access) ? '?access=' . $access : '')));

    }

}
