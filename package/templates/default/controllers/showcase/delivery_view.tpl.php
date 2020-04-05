<?php
	$this->addBreadcrumb('Способы доставки', $this->href_to('delivery'));
	$this->addBreadcrumb($delivery['title']);
	$this->setPageTitle('Способы доставки - ' . $delivery['title']);
	$this->setPageKeywords('доставка, способ доставки, выбор информация о доставке ' . $delivery['title']);
	if (!empty($delivery['hint'])){
		$this->setPageDescription($delivery['hint']);
	} else {
		$this->setPageDescription('На этой странице можете ознакомиться способами доставки товаров нашего магазина ' . $this->controller->cms_config->sitename . ' ' . $delivery['title']);
	}
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/cart.css', false));
?>
<h1><?php html($delivery['title']); ?></h1>
<div class="sc_cart_delivery">
	<div class="cart_delivery_box">
		<?php if (!empty($delivery['pickup_map'])){ ?> 
			<script src="//api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
			<script src="//maps.googleapis.com/maps/api/js?key=AIzaSyDx6-8xOE5uktxgsOmfN7ElgSjaTRd0h_w"></script>
		<?php } ?>
		<table class="sc_d_table">
			<tbody>
				<tr>
					<td class="sc_d_title">Заголовок</td>
					<td class="sc_d_value"><?php echo $delivery['title']; ?></td>
				</tr>
				<?php if (!empty($delivery['hint'])){ ?>
					<tr>
						<td class="sc_d_title">Описание</td>
						<td class="sc_d_value"><?php echo $delivery['hint']; ?></td>
					</tr>
				<?php } ?>
				<tr>
					<td class="sc_d_title">Способ</td>
					<td class="sc_d_value"><?php echo ($delivery['type'] == 'courier') ? 'Курьерская доставка' : 'Самовывоз'; ?></td>
				</tr>
				<?php if (!empty($delivery['pickup_address'])){ ?>
					<tr>
						<td class="sc_d_title">Адрес самовывоза</td>
						<td class="sc_d_value">
							<?php html($delivery['pickup_address']); ?> 
							<?php if (!empty($delivery['pickup_map'])){ ?> 
								<a href="<?php echo href_to('showcase', 'delivery_map', array('view', $delivery['id'])); ?>" class="ajax-modal" title="Адрес на карте">показать на карте</a>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
				<tr>
					<td class="sc_d_title">Цена</td>
					<td class="sc_d_price"><?php echo $delivery['price'] ? $delivery['price'] . ' ' . (!empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY) : (isset($delivery['price']) ? 'Бесплатно' : 'Не указана'); ?></td>
				</tr>
			</tbody>
		</table>
		<?php if ($delivery_text){ ?>
			<div class="cart_delivery_text"><?php echo $delivery_text; ?></div>
		<?php } ?>
	</div>
</div>