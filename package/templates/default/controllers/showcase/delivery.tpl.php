<?php
	$this->addBreadcrumb('Способы доставки');
	$this->setPageTitle('Способы доставки');
	$this->setPageKeywords('доставка, способ доставки, выбор информация о доставке');
    $this->setPageDescription('На этой странице можете ознакомиться способами доставки товаров нашего магазина ' . $this->controller->cms_config->sitename);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/cart.css', false));
?>
<h1>Способы доставки</h1>
<div class="sc_cart_delivery">
	<div class="cart_delivery_box">
		<?php if ($delivery_text){ ?>
			<div class="cart_delivery_text"><?php echo $delivery_text; ?></div>
		<?php } ?>
		<?php if ($courier_delivery){ ?>
			<h2>Курьерская доставка</h2>
			<div class="courier_delivery">
				<?php foreach ($courier_delivery as $courier){ ?>
					<a href="<?php echo $this->href_to('delivery', $courier['id']); ?>" class="courier_delivery_item">
						<div class="sc_delivery_info">
							<span><?php html($courier['title']); ?></span>
							<?php if (!empty($courier['hint'])){ ?><p><?php html($courier['hint']); ?></p><?php } ?>
						</div>
						<div class="sc_delivery_price">
							<?php echo $courier['price'] ? $courier['price'] . ' ' . (!empty($this->controller->options['cerrency']) ? $this->controller->options['cerrency'] : LANG_CURRENCY) : (isset($courier['price']) ? 'Бесплатно' : 'Не указана'); ?>
						</div>
					</a>
				<?php } ?>
			</div>
		<?php } ?>
		<?php if ($pickup_delivery){ ?>
			<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
			<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDx6-8xOE5uktxgsOmfN7ElgSjaTRd0h_w"></script>
			<h2>Самовывоз</h2>
			<div class="pickup_delivery">
				<?php foreach ($pickup_delivery as $pickup){ ?>
					<div class="pickup_delivery_item">
						<div class="sc_delivery_info">
							<a href="<?php echo $this->href_to('delivery', $pickup['id']); ?>"><?php html($pickup['title']); ?></a>
							<?php if (!empty($pickup['hint'])){ ?><p><?php html($pickup['hint']); ?></p><?php } ?>
							<?php if (!empty($pickup['pickup_address'])){ ?>
								<div class="sc_delivery_address">
									<?php html($pickup['pickup_address']); ?>
									<?php if (!empty($pickup['pickup_map'])){ ?> 
										<a href="<?php echo href_to('showcase', 'delivery_map', array('view', $pickup['id'])); ?>" class="ajax-modal" title="Адрес на карте">показать на карте</a>
									<?php } ?>
								</div>
							<?php } ?>
						</div>
						<div class="sc_delivery_price">
							<?php echo $pickup['price'] ? $pickup['price'] . ' ' . (!empty($this->controller->options['cerrency']) ? $this->controller->options['cerrency'] : LANG_CURRENCY) : (isset($pickup['price']) ? 'Бесплатно' : 'Не указана'); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
</div>