<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Работа с товарами', $this->href_to('goods'));
	$this->addBreadcrumb('Список цветов товара', $this->href_to('colors'));
	$title = ($do == 'add') ? LANG_ADD : LANG_EDIT;
	$this->addBreadcrumb($title);
	$this->setPageTitle($title);
	$this->addToolButton(array(
		'class' => 'save',
		'title' => LANG_SAVE,
		'href'  => "javascript:icms.forms.submit()"
	));
	$this->addToolButton(array(
		'class' => 'cancel',
		'title' => LANG_CANCEL,
		'href'  => $this->href_to('colors')
	));
?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<div class="sc_color_page">
			<?php
				$this->renderForm($form, $item, array(
					'action' => '',
					'method' => 'post'
				), $errors);
			?>
		</div>
	</div>
</div>