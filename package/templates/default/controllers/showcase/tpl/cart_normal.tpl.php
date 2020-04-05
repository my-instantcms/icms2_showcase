<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/cart.css', false));
	$showcase = cmsCore::getController('showcase');
?>
<script>icms.showcase.cart_styles.normal = 1;</script>
<div class="wd_sc_cart sc_style_normal">
	
	<div class="wd_sc_cart_loader">
		<img src="/<?php html($this->getTplFilePath('controllers/showcase/img/ajax-loader.gif', false)); ?>" alt="Загрузка..." />
	</div>

	<div class="wd_sc_cart_list">
		<?php if (!empty($items)){ ?>
			<?php foreach ($items as $id => $item){ ?>
				<?php
					$goods = !empty($item['goods']) ? $item['goods'] : $item;
					$url = !empty($item['slug']) ? $item['slug'] : (!empty($goods['slug']) ? href_to($ctype_name, $goods['slug']) . '.html' : 'javascript:void(0);');
				?>
				<div class="wd_scl_item" id="item_<?php html($id); ?>">
					<div class="wd_scl_item_delete tipright" onClick="icms.showcase.scRemoveCartItem(this, '<?php html($id); ?>')" data-sc-tip="<?php html(LANG_DELETE); ?>?"><i class="fa fa-close"></i></div>
					<a href="<?php html($url); ?>" class="wd_scl_item_img">
						<?php if (!empty($goods['photo'])){ ?>
							<?php echo html_image($goods['photo'], 'micro', $goods['title']); ?>
						<?php } else { ?>
							<img src="/templates/default/controllers/showcase/img/delivery_micro.png" alt="Корзина" />
						<?php } ?>
					</a>
					<a href="<?php html($url); ?>" class="wd_scl_item_info" title="<?php html($goods['title']); ?>">
						<div class="wd_scl_item_title"><?php html($goods['title']); ?></div>
						<div class="wd_scl_item_meta">
							<?php if (isset($item['qty']) && (int)$item['qty'] > 1){ ?>
								<span class="sc_qty_span" data-sc-tip="Количество">
									x<?php html($item['qty']); ?>
								</span>
							<?php } ?>
							<?php if ($id == 'delivery'){ ?>
								<span data-sc-tip="Способ доставки">
									<?php echo ($item['type'] == 'courier') ? 'Доставка' : 'Самовывоз'; ?>
								</span>
							<?php } ?>
							<?php 
								$extra_fields = cmsEventsManager::hookAll("sc_html_cart_fields", array($ctype_name, $item, $fields));
								if ($extra_fields) { echo html_each($extra_fields); }
							?>
							<span class="sc_price_span" data-sc-tip="<?php html($fields['price']['title']); ?>">
								<?php if ($item['price']){ ?>
									<?php echo $showcase->getPriceFormat($item['price']); ?>
								<?php } else { ?>
									<?php echo (isset($item['price']) ? 'Бесплатно' : 'Не указана');; ?>
								<?php } ?>
							</span>
						</div>
					</a>
				</div>
			<?php } ?>
			<div class="wd_scl_footer">
				<?php 
					$this->renderControllerChild('showcase/tpl', 'steps_footer', array(
						'showcase' => $showcase,
						'summ' => $summ, 
						'next' => !empty($next) ? $next : '', 
						'sale' => $sale, 
					));
				?>
			</div>
		<?php } else { ?>
			<p class="sc_no_goods">
				<span>Нет товаров</span>
				<i class="<?php echo !empty($widget->options['fa']) ? $widget->options['fa'] : 'fa fa-shopping-basket'; ?>"></i>
			</p>
		<?php } ?>
	</div>

</div>