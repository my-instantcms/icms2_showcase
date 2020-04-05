<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addBreadcrumb('Работа с корзиной');
	$this->setPageTitle('Работа с корзиной');

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<div class="sc_admin_cart">
			<a class="sc_ac_btns" href="<?php echo $this->href_to('steps'); ?>">
				<i class="glyphicon glyphicon-transfer"></i>
				Шаги оформления заказа
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('cart_fields'); ?>">
				<i class="glyphicon glyphicon-phone-alt"></i>
				Поля для оформления заказа
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('cart_delivery'); ?>">
				<i class="glyphicon glyphicon-plane"></i>
				Служба доставки
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('cupon_sales'); ?>">
				<i class="glyphicon glyphicon-gift"></i>
				Купоны и скидки
				<span>new</span>
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('pay_systems'); ?>">
				<i class="glyphicon glyphicon-piggy-bank"></i>
				Система оплаты
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('cart_transactions'); ?>">
				<i class="glyphicon glyphicon-duplicate"></i>
				Транзакции (платежи)
			</a>
		</div>
	</div>
</div>