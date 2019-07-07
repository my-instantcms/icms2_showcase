<?php
	
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Служба доставки', $this->href_to('cart_delivery'));
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
		'href'  => $this->href_to('cart_delivery')
	));

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<?php
			$this->renderForm($form, $item, array(
				'action' => '',
				'method' => 'post'
			), $errors);
		?>
	</div>
</div>