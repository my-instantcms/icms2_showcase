<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Работа с товарами', $this->href_to('goods'));
	$this->addBreadcrumb('Прайс-агрегаторы');
	$this->setPageTitle('Прайс-агрегаторы');
	$this->addToolButton(array(
		'class' => 'add',
		'title' => LANG_ADD,
		'href'  => $this->href_to('aggregators_form')
	));
	$this->addToolButton(array(
		'class' => 'settings',
		'title' => LANG_CP_SCHEDULER,
		'href'  => href_to('admin', 'settings', 'scheduler')
	));
?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('aggregators', 1), $grid); ?>
	</div>
</div>