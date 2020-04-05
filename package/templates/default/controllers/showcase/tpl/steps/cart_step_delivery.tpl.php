<?php
	$this->addBreadcrumb($steps['current']['title']);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/cart.css', false));
	$next = !empty($steps['next']['id']) ? $steps['next']['id'] : 'checkout';

?>
<h1 id="sc_cart_title">Корзина (<?php echo html_spellcount((isset($count) ? $count : 0), 'товар|товара|товаров'); ?>)</h1>
<div class="sc_cart_delivery wd_sc_cart sc_style_big">
	<div class="cart_delivery_box">
		<?php if ($delivery_text){ ?>
			<div class="cart_delivery_text"><?php echo $delivery_text; ?></div>
		<?php } ?>
		<?php if (!$courier_delivery && !$pickup_delivery){ ?>Способы доставки не добавлены<?php } ?>
		<?php if ($courier_delivery){ ?>
			<h2>Курьерская доставка</h2>
			<div class="courier_delivery">
				<?php foreach ($courier_delivery as $courier){ ?>
					<div class="courier_delivery_item" onClick="icms.showcase.setDelivery(this, <?php html($courier['id']); ?>)">
						<input type="checkbox" class="sc_delivery_checkbox" id="cb_<?php html($courier['id']); ?>" /> 
						<div class="sc_delivery_info">
							<span><?php html($courier['title']); ?></span>
							<?php if (!empty($courier['hint'])){ ?><p><?php html($courier['hint']); ?></p><?php } ?>
						</div>
						<div class="sc_delivery_price">
							<?php echo $courier['price'] ? $this->controller->getPriceFormat($courier['price']) : (isset($courier['price']) ? 'Бесплатно' : 'Не указана'); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		<?php if ($pickup_delivery){ ?>
			<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
			<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDx6-8xOE5uktxgsOmfN7ElgSjaTRd0h_w"></script>
			<h2>Самовывоз</h2>
			<div class="pickup_delivery">
				<?php foreach ($pickup_delivery as $pickup){ ?>
					<div class="pickup_delivery_item" onClick="icms.showcase.setDelivery(this, <?php html($pickup['id']); ?>)">
						<input type="checkbox" class="sc_delivery_checkbox" id="cb_<?php html($pickup['id']); ?>" /> 
						<div class="sc_delivery_info">
							<span><?php html($pickup['title']); ?></span>
							<?php if (!empty($pickup['hint'])){ ?><p><?php html($pickup['hint']); ?></p> <?php } ?>
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
							<?php echo $pickup['price'] ? $this->controller->getPriceFormat($pickup['price']) : (isset($pickup['price']) ? 'Бесплатно' : 'Не указана'); ?>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<br />
	<?php 
		$this->renderControllerChild('showcase/tpl', 'steps_footer', array(
			'showcase' => $this->controller,
			'summ' => $summ, 
			'next' => !empty($next) ? $next : '', 
			'sale' => $sale, 
			'attributes' => ($courier_delivery || $pickup_delivery) ? array('style' => 'display:none') : false, 
		));
	?>
</div>