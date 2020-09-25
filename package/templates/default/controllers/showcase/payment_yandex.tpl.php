<?php

	$this->setPageTitle('Переход на страницу оплаты');
	$this->addBreadcrumb('Переход на страницу оплаты');
	
	// цена товара
	$price = $order['price'];
	
	// Комиссия Яндекса, %
	$a = 0.005;

	// Учесть комиссию Яндекса
	$price = $price + max(0.01, $price * $a / 100);

?>
<?php if ($error){ ?>
	<h1><?php html(LANG_ERROR); ?></h1>
	<p><?php html($error); ?></p>
<?php } else { ?>

	<div style="text-align:center">
		<img src="/templates/default/controllers/showcase/img/ajax-loader.gif" alt="Подождите..." />
	</div>
	
	<form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml" id="yd_pay_form">
		<input type="hidden" name="receiver" value="<?php html($system['wallet_id']); ?>">
		<input type="hidden" name="formcomment" value="Оплата заказа: №<?php echo !empty($order['id']) ? $order['id'] : 'Неизвестно'; ?> на сайте <?php html($this->controller->cms_config->sitename); ?>">
		<input type="hidden" name="short-dest" value="Оплата заказа: №<?php echo !empty($order['id']) ? $order['id'] : 'Неизвестно'; ?> на сайте <?php html($this->controller->cms_config->sitename); ?>">
		<input type="hidden" name="label" value="<?php html($order['id']); ?>">
		<input type="hidden" name="quickpay-form" value="shop">
		<input type="hidden" name="targets" value="№ транзакции: <?php echo !empty($transaction['id']) ? $transaction['id'] : $order['id']; ?>">
		<input type="hidden" name="sum" value="<?php echo $price; ?>" data-type="number">
		<input type="hidden" name="comment" value="Оплата заказа: №<?php echo !empty($order['id']) ? $order['id'] : 'Неизвестно'; ?> на сайте <?php html($this->controller->cms_config->sitename); ?>">
		<input type="hidden" name="successURL" value="<?php echo href_to_abs('showcase', 'orders', array($order['id'], ($order['status'] ? $order['status'] : 1))) . $hash; ?>">
		<input type="hidden" name="need-fio" value="false">
		<input type="hidden" name="need-email" value="false"> 
		<input type="hidden" name="need-phone" value="false">
		<input type="hidden" name="need-address" value="false">
		<input type="hidden" name="paymentType" value="PC">
		<input type="submit" value="Оплатить" style="display:none">
	</form>
	<?php ob_start(); ?>
	<script>
		$(document).ready(function() {
			$('#yd_pay_form').submit();
		});
	</script>
	<?php $this->addBottom(ob_get_clean()); ?>
<?php } ?>