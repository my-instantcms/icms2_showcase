<?php
	
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Транзакции');
	$this->setPageTitle('Транзакции');

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('cart_transactions', 1), $grid); ?>
	</div>
</div>