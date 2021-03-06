<?php
	
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Служба доставки');
	$this->setPageTitle('Служба доставки');

	$this->addToolButton(array(
		'class' => 'add',
		'title' => 'Добавить способ доставки',
		'href'  => $this->href_to('cart_delivery_form')
	));
	
	$this->addToolButton(array(
		'class' => 'settings',
		'title' => LANG_OPTIONS,
		'href'  => $this->href_to('cart_delivery_options')
	));

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('cart_delivery', 1), $grid); ?>
		<div class="buttons">
			<?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('item_reorder', 'sc_cart_delivery')}')"); ?>
		</div>
	</div>
</div>