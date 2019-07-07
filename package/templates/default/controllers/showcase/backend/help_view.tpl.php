<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addBreadcrumb(LANG_HELP, $this->href_to('help'));
	$this->addBreadcrumb(!empty($data['title']) ? $data['title'] : LANG_ERROR);
	$this->setPageTitle(!empty($data['title']) ? $data['title'] : LANG_ERROR);

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('help'); ?>
	<div class="page-content">
		<div class="sc_admin_help_view">
			<a href="<?php html($this->href_to('help')); ?>" class="sc_help_back"><i class="glyphicon glyphicon-arrow-left"></i> Назад</a>
			<div class="sc_admin_help_content"><?php echo $data['html']; ?></div>
		</div>
	</div>
</div>