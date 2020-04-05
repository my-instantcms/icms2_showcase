<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/cart.css', false));
	$showcase = cmsCore::getController('showcase');
?>
<h1 id="sc_cart_title">Корзина (<?php echo html_spellcount((isset($count) ? $count : 0), 'товар|товара|товаров'); ?>)</h1>
<script>icms.showcase.cart_styles.big = 1;</script>
<div class="wd_sc_cart sc_style_big">
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
					<a href="<?php html($url); ?>" class="wd_scl_item_img">
						<?php if (!empty($goods['photo'])){ ?>
							<?php echo html_image($goods['photo'], 'small', $goods['title']); ?>
						<?php } else { ?>
							<img src="/templates/default/controllers/showcase/img/delivery_small.png" />
						<?php } ?>
					</a>
					<div class="wd_scl_item_info">
						<a href="<?php html($url); ?>" class="wd_scl_item_title" title="<?php html($goods['title']); ?>">
							<?php html($goods['title']); ?>
						</a>
						<div class="wd_scl_item_meta">
							<?php if (!empty($item['goods'])){ ?>
								<div class="sc_buy_qty">
									<button class="sc_qty_btn_minus" onClick="icms.showcase.scSetQty(this, '<?php html($id); ?>', true)"><i class="fa fa-minus fa-fw"></i></button>
									<div class="sc_qty_count">
										<input type="text" value="<?php echo !empty($item['qty']) ? $item['qty'] : 1; ?>" onkeyup="icms.showcase.scSetQty(false, '<?php html($id); ?>', true)">
									</div>
									<button class="sc_qty_btn_plus" onClick="icms.showcase.scSetQty(this, '<?php html($id); ?>', true)" <?php if (empty($showcase->options['off_inctock']) && !empty($item['variant']['in']) || empty($showcase->options['off_inctock']) && !empty($item['goods']['in_stock'])){ ?>data-max="<?php echo (!empty($item['variant']['in']) && $item['variant']['in'] != 'none') ? $item['variant']['in'] : (isset($item['goods']['in_stock']) ? $item['goods']['in_stock'] : 0); ?>"<?php } ?>><i class="fa fa-plus fa-fw"></i></button>
								</div>
							<?php } ?>
							<?php if ($id == 'delivery'){ ?>
								<span data-sc-tip="Способ доставки">
									<?php echo ($item['type'] == 'courier') ? 'Курьерская доставка' : 'Самовывоз'; ?>
								</span>
							<?php } ?>
							<?php 
								$extra_fields = cmsEventsManager::hookAll("sc_html_cart_fields", array($ctype_name, $item, $fields));
								if ($extra_fields) { echo html_each($extra_fields); }
							?>
						</div>
					</div>
					<div class="sc_price_div">
						<?php if ($item['price']){ ?>
							<?php echo $showcase->getPriceFormat($item['price']); ?>
						<?php } else { ?>
							<?php echo (isset($item['price']) ? 'Бесплатно' : 'Не указана');; ?>
						<?php } ?>
					</div>
					<div class="wd_scl_item_delete dsct_top_left" onClick="icms.showcase.scRemoveCartItem(this, '<?php html($id); ?>')" data-sc-tip="<?php html(LANG_DELETE); ?>?"><i class="fa fa-close"></i></div>
				</div>
			<?php } ?>
			<?php 
				$this->renderControllerChild('showcase/tpl', 'steps_footer', array(
					'showcase' => $showcase,
					'summ' => $summ, 
					'next' => !empty($next) ? $next : '', 
					'sale' => $sale, 
				));
			?>
		<?php } else { ?>
			<p class="sc_no_goods">
				<span>Нет товаров</span>
				<i class="<?php echo !empty($widget->options['fa']) ? $widget->options['fa'] : 'fa fa-shopping-basket'; ?>"></i>
			</p>
		<?php } ?>
	</div>

</div>