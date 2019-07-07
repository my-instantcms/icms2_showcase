<?php

class actionShowcaseCartTransactionsView extends cmsAction {
	
	public $page_name = 'cart_transactions_view';

	public function run($id = false) {
		
		if (!$id) { cmsCore::error404(); }
		
		$transaction = $this->model->
			select('p.title', 'sys_name')->
			joinLeft('sc_pay_systems', 'p', 'p.id=i.system_id')->
			getData('sc_transactions', $id);
		if (!$transaction) { cmsCore::error404(); }
		
		$titles = array(
			'id' => '№ транзакции',
			'order_id' => '№ заказа',
			'sys_name' => 'Платежная система',
			'price' => 'Сумма',
			'history' => 'История',
			'errors' => 'Ошибки',
			'response' => 'Ответ платежей системы',
			'date_pub' => 'Дата',
		);

		return $this->cms_template->render('backend/' . $this->page_name, array(
			'transaction' => $transaction,
			'titles' => $titles,
		));

	}

}