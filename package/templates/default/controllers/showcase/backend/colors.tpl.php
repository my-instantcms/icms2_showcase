<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Работа с товарами', $this->href_to('goods'));
	$this->addBreadcrumb('Список цветов товара');
	$this->setPageTitle('Список цветов товара');
	$this->addToolButton(array(
		'class' => 'add',
		'title' => LANG_ADD,
		'href'  => $this->href_to('colors_form')
	));
?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<?php $this->renderGrid($this->href_to('colors', 1), $grid); ?>
	</div>
</div>