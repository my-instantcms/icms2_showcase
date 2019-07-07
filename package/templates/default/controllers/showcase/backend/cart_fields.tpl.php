<?php
	
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Поля для оформления заказа');
	$this->setPageTitle('Поля для оформления заказа');

	$this->addToolButton(array(
		'class' => 'add',
		'title' => LANG_ADD,
		'href'  => $this->href_to('cart_fields_form')
	));

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('cart_fields', 1), $grid); ?>
		<div class="buttons">
			<?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('item_reorder', 'sc_cart_fields')}')"); ?>
		</div>
	</div>
</div>