<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Работа с товарами', $this->href_to('goods'));
	$this->addBreadcrumb('Управление вкладками');
	$this->setPageTitle('Управление вкладками');
	$this->addToolButton(array(
		'class' => 'add',
		'title' => LANG_ADD,
		'href'  => $this->href_to('tabs_form')
	));
?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('tabs', 1), $grid); ?>
		<div class="buttons">
			<?php echo html_button(LANG_SAVE_ORDER, 'save_button', "icms.datagrid.submit('{$this->href_to('item_reorder', 'sc_tabs')}')"); ?>
		</div>
	</div>
</div>