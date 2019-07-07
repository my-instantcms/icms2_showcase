<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Система оплаты', $this->href_to('pay_systems'));
	$this->addBreadcrumb('Выбор платежных систем');
	$this->setPageTitle('Выбор платежных систем');
?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">
		<ul class="sc_add_pay_systems">
			<?php if ($gateways){ ?>
				<?php foreach ($gateways as $gateway){ ?>
					<?php
						$file_action = !empty($gateway['file_action']) ? $gateway['file_action'] : 'pay_systems_form';
					?>
					<li>
						<a href="<?php echo $this->href_to($file_action, $id); ?>">
							<i class="glyphicon glyphicon-plus"></i> <?php echo LANG_ADD . ' ' . $gateway['title']; ?>
						</a>
					</li>
				<?php } ?>
			<?php } else { ?>
				<li>Нет доступных платежных систем</li>
			<?php } ?>
		</ul>
		
	</div>
</div>