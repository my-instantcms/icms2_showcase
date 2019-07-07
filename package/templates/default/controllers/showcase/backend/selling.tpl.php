<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/libs/tabulator/css/tabulator.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/libs/tabulator/css/semantic-ui/tabulator_semantic-ui.min.css', false), false);
	$this->addJS($this->getTplFilePath('controllers/showcase/libs/tabulator/js/tabulator.min.js', false), false);
	$this->addBreadcrumb('Товары', $this->href_to('goods'));
	$this->addBreadcrumb('Список заказов');
	$this->setPageTitle('Список заказов');

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<div class="tabulator_loader"><img src="/templates/default/controllers/showcase/img/ajax-loader.gif"> </div>
		В разработке...
	</div>
</div>