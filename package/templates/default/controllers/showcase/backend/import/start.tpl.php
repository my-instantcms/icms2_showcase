<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addBreadcrumb('Товары', $this->href_to('goods'));
	$this->addBreadcrumb('Импорт товаров');
	$this->setPageTitle('Импорт товаров');
	
	$this->addToolButton(array(
		'class' => 'save',
		'title' => LANG_SAVE,
		'href'  => "javascript:icms.forms.submit()"
	));

	$this->addToolButton(array(
		'class' => 'cancel',
		'title' => LANG_CANCEL,
		'href'  => $this->href_to('goods')
	));

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<div class="sc_admin_import">
		
			<div class="f1-steps">
				<div class="f1-progress">
					<div class="f1-progress-line" style="width: 11%;"></div>
				</div>
				<div class="f1-step active">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-download-alt"></i></div>
					<p>Загрузить файл</p>
				</div>
				<div class="f1-step">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-cog"></i></div>
					<p>Назначение столбцов</p>
				</div>
				<div class="f1-step">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-duplicate"></i></div>
					<p>Импорт</p>
				</div>
				<div class="f1-step">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-ok-sign"></i></div>
					<p>Готово</p>
				</div>
			</div>
		
			<?php
				$this->renderForm($form, $data, array(
					'action' => '',
					'method' => 'post',
					'submit' => array('title' => LANG_CONTINUE)
				), $errors);
			?>
			
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			
		</div>
	</div>
</div>

<style>
.f1-steps{overflow:hidden;position:relative;margin-top:20px}.f1-progress,.f1-progress-line{position:absolute;left:0;height:1px}.f1-progress{top:24px;width:100%;background:#ddd}.f1-progress-line{top:0;background:#337ab7}.f1-step{position:relative;float:left;width:25%;padding:0 5px;text-align:center}.f1-step-icon{display:inline-block;width:40px;height:40px;margin-top:4px;background:#ddd;font-size:16px;color:#fff;line-height:44px;-moz-border-radius:50%;-webkit-border-radius:50%;border-radius:50%;text-align:center}.f1-step.activated .f1-step-icon{background:#fff;border:1px solid #337ab7;color:#337ab7;line-height:38px}.f1-step.active .f1-step-icon{width:48px;height:48px;margin-top:0;background:#337ab7;font-size:22px;line-height:50px}.f1-step p{color:#ccc}.f1-step.activated p,.f1-step.active p{color:#337ab7}
</style>