<?php
	$this->addBreadcrumb($item['title'], href_to($ctype_name, $item['slug'] . '.html'));
	$this->addBreadcrumb('Предзаказ');
	$this->setPageTitle('Предзаказ');
?>
<div class="sc_preorder_box">
	<?php
		$this->renderForm($form, $data, array(
			'action' => href_to('showcase', 'add_preorder'),
			'method' => 'post',
			'form_id' => 'preorder_form',
			'toolbar' => false
		), $errors);
	?>
</div>