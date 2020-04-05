<?php
	
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Купоны и скидки');
	$this->setPageTitle('Купоны и скидки');

	$this->addToolButton(array(
		'class' => 'add',
		'title' => LANG_ADD,
		'href'  => $this->href_to('sales_form')
	));

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('cupon_sales', 1), $grid); ?>
	</div>
</div>