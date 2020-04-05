<?php

	if (!empty($system['gateway_name']) && $system['gateway_name'] == 'check'){
		$this->setPageTitle('Оплата товара по реквизитам');
		if ($this->controller->cms_user->id){
			$this->addBreadcrumb(LANG_USERS, href_to('users'));
			$this->addBreadcrumb($this->controller->cms_user->nickname, href_to('users', $this->controller->cms_user->id));
			$this->addBreadcrumb('Мои заказы', href_to('users', $this->controller->cms_user->id, 'orders'));
			$this->addBreadcrumb('Заказ №' . $order['id'], href_to('showcase', 'orders', array($order['id'], $order['status'])));
		}
		$this->addBreadcrumb('Оплата товара по реквизитам');
	} else {
		$this->setPageTitle('Переход на страницу оплаты');
		$this->addBreadcrumb('Переход на страницу оплаты');
	}
	
?>
<?php if ($error){ ?>
	<h1><?php html(LANG_ERROR); ?></h1>
	<p><?php html($error); ?></p>
<?php } else if ($system['gateway_name'] == 'check'){ ?>
	<?php
		$phrase = array("{order_id}", "{order_price}");
		$healthy = array($order['id'], $order['price']);
		echo !empty($this->controller->options['pay_check_data']) ? str_replace($phrase, $healthy, $this->controller->options['pay_check_data']) : 'Поле "Реквизиты" не заполнены администратором';
	?>
<?php } else { ?>
	<p>Платежная система не до конца настроена</p>
<?php } ?>