<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
    $this->setPageTitle(LANG_OPTIONS);
    $this->addBreadcrumb(LANG_OPTIONS);
    $this->addToolButton(array(
        'class' => 'save',
        'title' => LANG_SAVE,
        'href'  => "javascript:icms.forms.submit()"
    ));
	
	if($toolbar){
        foreach ($toolbar as $menu) {
            $this->addToolButton($menu);
        }
    }

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('options'); ?>
	<div class="page-content">
		<div id="<?php echo $this->controller->name; ?>_options_form">
		<?php
			$this->renderForm($form, $options, array(
				'action' => '',
				'method' => 'post'
			), $errors);
		?>
		</div>
	</div>
</div>