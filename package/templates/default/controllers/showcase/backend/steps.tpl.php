<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Шаги оформления заказа');
	$this->setPageTitle('Шаги оформления заказа');

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('steps', 1), $grid); ?>
		<div class="buttons">
			<?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('item_reorder', 'sc_steps')}')"); ?>
		</div>
	</div>
</div>