<?php if ($orders){ ?>
	<table style="width: 100%;" >
		<tbody>
			<?php if (!$is_more){ ?>
			<tr class="sc_ol_titles">
				<td>Заказ</td>
				<td>Товары</td>
				<td>Цена</td>
				<td>Дата</td>
				<?php if ($is_manager){ ?><td>Статус</td><?php } ?>
				<td>Инфо</td>
			</tr>
			<?php } ?>
			<?php foreach ($orders as $id => $order){ ?>
				<?php
					$delivery = !empty($order['delivery']) ? cmsModel::yamlToArray($order['delivery']) : false;
					if ($delivery){
						$delivery['artikul'] = (isset($delivery['type']) && $delivery['type'] == 'courier') ? 'Доставка' : 'Самовывоз';
						$order['items']['delivery'] = $delivery;
					}
					if ($delivery['type'] == 'courier'){
						$status[3] = 'Доставляется';
					} else {
						$status[3] = 'Ожидает получения';
					}
				?>
				<tr class="sc_ol_item" id="item_<?php html($id); ?>">
					<td class="sc_oli_id">№<?php html($id); ?></td>
					<td class="sc_oli_goods">
						<div class="sc_oli_toggle" onClick="$('#goods_<?php html($id); ?>').toggle();">Товары (<?php echo count(!empty($order['items']) ? $order['items'] : 0); ?>)</div>
					</td>
					<td class="sc_oli_price">
						<?php echo $order['price']; ?> 
						<?php echo !empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY; ?>
					</td>
					<td class="sc_oli_date"><?php echo html_date($order['date'], true); ?></td>
					<?php if ($is_manager){ ?>
						<td class="sc_oli_status">
							<?php echo html_select('status', $status, $order['status'], array('onchange' => 'scSetStatus(this, ' . $id . ')')); ?>
						</td>
					<?php } ?>
					<td class="sc_oli_info">
						<a href="<?php echo href_to('showcase', 'orders', array($id, $order['status'])); ?>">Детали <i class="fa fa-angle-double-right"></i></a>
					</td>
				</tr>
				<?php if (!empty($order['items'])){ ?>
					<tr class="sc_oli_goods_list" id="goods_<?php html($id); ?>">
						<td colspan="<?php echo $is_manager ? 6 : 5; ?>">
							<?php foreach ($order['items'] as $good){ ?>
								<div class="sc_oli_goods_item">
									<?php if (!empty($good['artikul'])){ ?>
										<span data-sc-tip="Артикул"><?php html($good['artikul']); ?></span> 
									<?php } ?>
									<span><a href="<?php echo !empty($good['slug']) ? (!empty($good['ctype_name']) ? href_to($good['ctype_name'], $good['slug'] . '.html') : $good['slug']) : 'javascript:void(0);'; ?>" target="_blank"><?php echo !empty($good['title']) ? $good['title'] : '[Неизвестно]'; ?></a></span>
									<span data-sc-tip="Количество">x<?php echo !empty($good['qty']) ? $good['qty'] : 1; ?></span>
									<span data-sc-tip="Цена" class="sc_gi_price"><?php echo !empty($good['price']) ? $good['price'] : 0; ?> <?php echo !empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY; ?></span>
									<?php 
										$extra_fields = cmsEventsManager::hookAll("sc_html_cart_fields", array($this->controller->ctype_name, $good, $fields));
										if ($extra_fields) { echo html_each($extra_fields); }
									?>
								</div>
							<?php } ?>
						</td>
					</tr>
				<?php } ?>
			<?php } ?>
		</tbody>
	</table>
	
	<?php if (!empty($perpage) && $perpage < $total) { ?>
		<div class="tab_btn_more" onClick="tabMore(this, <?php html($page); ?>, <?php html($perpage); ?>, <?php html($status_id); ?>, 1)"><img src="/templates/default/controllers/showcase/img/sort.png" /> Загрузить еще</div>
	<?php } ?>
	
<?php } else { ?>
	<p><?php html(LANG_LIST_EMPTY); ?></p>
<?php } ?>