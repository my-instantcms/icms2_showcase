<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addBreadcrumb('Работа с товарами');
	$this->setPageTitle('Работа с товарами');

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<div class="sc_admin_cart">
			<a class="sc_ac_btns" href="<?php echo $this->href_to('goods', 'list'); ?>">
				<i class="glyphicon glyphicon-list-alt"></i>
				Список товаров
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('colors'); ?>">
				<i class="glyphicon glyphicon-adjust"></i>
				Список цветов товара
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('export'); ?>">
				<i class="glyphicon glyphicon-paste"></i>
				Экспорт товаров
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('import'); ?>">
				<i class="glyphicon glyphicon-copy"></i>
				Импорт товаров
			</a>
			<a class="sc_ac_btns" href="<?php echo $this->href_to('tabs'); ?>">
				<i class="glyphicon glyphicon-credit-card"></i>
				Управление вкладками
			</a>
		</div>
	</div>
</div>