<?php 
	if ($is_manager){
		$this->addBreadcrumb('Заказы', $this->href_to('orders', array(0, $order['status'])));
	} else if (empty($order['user_id'])){
		
	} else {
		$this->addBreadcrumb(LANG_USERS, href_to('users'));
		$this->addBreadcrumb($this->controller->cms_user->nickname, href_to('users', $order['user_id']));
		$this->addBreadcrumb('Мои заказы', href_to('users', $order['user_id'], 'orders'));
	}
    $this->addBreadcrumb('Заказ №' . $order['id']);
    $this->setPageTitle('Заказ №' . $order['id']);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/tab.css', false));
	$pay = false;
?>
<?php if ($is_manager){ ?>
	<div class="sc_order_view_btns">
		<a href="<?php echo href_to('showcase', 'order_edit', $order['id']); ?>" style="display:none"><?php html(LANG_EDIT); ?></a>
		<a href="<?php echo href_to('showcase', 'order_delete', $order['id']); ?>" onclick="return confirm('Вы уверены что хотите удалить заказ?')"><?php html(LANG_DELETE); ?></a>
	</div>
<?php } ?>
<table class="sc_order_view">
	<tbody>
		<?php foreach ($order as $name => $val){ ?>
		
			<?php if (!isset($val)){ continue; } ?>
		
			<tr class="sc_ol_item">
				<td class="sc_oli_title"><?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?></td>
				<td class="sc_oli_val">
					<?php if ($name == 'fields'){ ?>
						<table>
							<tbody>
								<?php foreach ($val as $key => $v){ ?>
									<?php if ($key == 'agreement' || !$v || empty($cart_fields[$key])){ continue; } ?>
									<tr>
										<td><b><?php html($cart_fields[$key]['title']); ?></b></td>
										<td class="order_fields_td"><?php 
											if ($cart_fields[$key]['type'] == 'select' && !empty($cart_fields[$key]['options'][$v])){
												echo $cart_fields[$key]['options'][$v];
											} else if ($key == 'paid'){
												if ($v != 1){
													echo 'Наличные, при получении';
												} else if (!empty($val['payment_system']) && $system){
													if ($order['paid'] == 1){
														echo  '<a href="' . href_to('showcase', 'set_payment', $order['id']) . '" class="ajax-modal order_fields_paid" title="Изменить способ оплаты">' . $system['title'] . '</a>';
													} else {
														echo $system['title'];
													}
												} else {
													echo 'Из баланса';
												}
											} else {
												echo $v;
											}
										?></td>
									</tr>
								<?php } ?>
							</tbody>
						</table>
					<?php } else if ($name == 'delivery'){ ?>
						<?php
							$val = cmsModel::yamlToArray($val);
							if ($val['type'] == 'courier'){
								$status[3] = 'Доставляется';
							} else {
								$status[3] = 'Ожидает получения';
							}
						?>
						<?php echo ($val['type'] == 'courier') ? 'Курьерская доставка' : 'Самовывоз'; ?>: 
						<a href="<?php html($val['slug']); ?>" target="_blank"><?php html($val['title']); ?></a> - 
						<b style="color:red"><?php echo $val['price'] ? $this->controller->getPriceFormat($val['price']) : (isset($val['price']) ? 'Бесплатно' : 'Не указана'); ?></b>
					<?php } else if ($name == 'price'){ ?>
						<strong style="color:red"><?php echo $this->controller->getPriceFormat($val); ?></strong>
					<?php } else if ($name == 'sale_id' && $sale){ ?>
						<b style="color:green"><?php html($sale['title']); ?></b>
					<?php } else if ($name == 'user_id'){ ?>
						<?php if (empty($order['user_id'])){ ?>
							<?php echo !empty($order['fields']['name']) ? $order['fields']['name'] : LANG_GUEST; ?>
						<?php } else { ?>
							<a href="<?php echo href_to('users', $val); ?>" target="_blank"><?php echo $is_manager ? 'Открыть профиль' : $this->controller->cms_user->nickname; ?></a>
						<?php } ?>
					<?php } else if ($name == 'status'){ ?>
						<?php if ($is_manager){ ?>
							<?php echo html_select('status', $status, $val, array('onchange' => 'scSetStatus(this, ' . $order['id'] . ')')); ?>
						<?php } else { ?>
							<?php html($status[$val]); ?>
						<?php } ?>
					<?php } else if ($name == 'date'){ ?>
						<?php echo lang_date(date('j F Y H:i', strtotime($val))); ?>
					<?php } else if ($name == 'paid' && $val){ ?>
						<?php if ($is_manager){ ?>
							<?php $pay = ($val == 1) ? ((isset($order['fields']['payment_system']) && $order['fields']['payment_system'] == 0) ? false : true) : false; ?>
							<?php echo html_select('pay', array(1 => 'Ожидается оплата', 2 => 'Оплачено'), $val, array('onchange' => 'scPayStatus(this, ' . $order['id'] . ')')); ?>
						<?php } else { ?>
							<?php 
								if ($val == 1){
									$pay = (isset($order['fields']['payment_system']) && $order['fields']['payment_system'] == 0) ? false : true;
									echo 'Ожидается оплата';
								} else {
									$pay = false;
									echo 'Оплачено';
								}
							?>
						<?php } ?>
					<?php } else if ($name == 'items'){ ?>
						<?php foreach ($val as $index => $good){ ?>
							<div class="sc_order_view_goods">
								<?php if (!empty($good['artikul'])){ ?>
									<span data-sc-tip="Артикул"><?php html($good['artikul']); ?></span> 
								<?php } ?>
								<span><a href="<?php echo href_to($good['ctype_name'], $good['slug'] . '.html'); ?>" target="_blank"><?php echo !empty($good['title']) ? $good['title'] : '[Неизвестно]'; ?></a></span>
								<span data-sc-tip="Количество">x<?php echo !empty($good['qty']) ? $good['qty'] : 1; ?></span>
								<span data-sc-tip="Цена" class="sc_gi_price"><?php echo $this->controller->getPriceFormat((!empty($good['price']) ? $good['price'] : 0)); ?></span>
								<?php 
									$extra_fields = cmsEventsManager::hookAll("sc_html_cart_fields", array($this->controller->ctype_name, $good, $fields));
									if ($extra_fields) { echo html_each($extra_fields); }
								?>
							</div>
						<?php } ?>
					<?php } else { ?>
						<?php html($val); ?>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" class="sc_order_btns">
				<?php if ($is_manager){ ?>
					<a href="<?php echo $this->href_to('print', $order['id']); ?>" target="_blank"><i class="fa fa-print"></i> Распечатать</a>
				<?php } ?>
				<?php if ($pay && !empty($this->controller->options['payment']) && $this->controller->options['payment'] != 'off'){ ?>
					<?php if ($this->controller->options['payment'] == 'system'){ ?>
						<a href="<?php echo href_to('showcase', 'payment', array($order['id'] . ($hash ? '?access=' . $hash : ''))); ?>"><i class="fa fa-money"></i> Оплатить <?php echo $this->controller->getPriceFormat($order['price']); ?></a>
					<?php } else { ?>
						<a href="javascript:void(0);" onclick="scOrderPay(this)" class="sc_payment_btn" data-sc-tip="Ваш баланс: <?php echo @$this->controller->cms_user->balance ? $this->controller->cms_user->balance : 0; ?>"><i class="fa fa-money"></i> Оплатить <?php echo $this->controller->getPriceFormat($order['price']); ?></a>
					<?php } ?>
				<?php } ?>
			</td>
		</tr>
	</tbody>
</table>
<?php ob_start(); ?>
<script>
	<?php if (!empty($this->controller->options['payment']) && $this->controller->options['payment'] != 'off'){ ?>
		function scOrderPay(btn){
			if (!confirm("Подтверждаете оплату?")) { return false; }
			btn = $(btn);
			if (btn.hasClass('pay_process')){ return; }
			btn.addClass('pay_process').html('<i class="fa fa-refresh fa-spin"></i> Подождите...');
			$.post('<?php echo $this->href_to('payment', $order['id']); ?>', false, function(result){
				if(result.error){
					icms.modal.alert(result.message, 'ui_error');
				} else {
					window.location.reload(true);
				}
				btn.removeClass('pay_process').html('<i class="fa fa-money"></i> Оплатить <?php echo $this->controller->getPriceFormat($order['price']); ?>');
			}, 'json');
		}
	<?php } ?>
	function scSetStatus(btn, order_id){
		var status_id = $(btn).val();
		if (status_id){
			$.post('<?php echo href_to('showcase', 'set_order_status'); ?>/' + order_id, {status_id : status_id}, function(result){
				if(result.error){
					icms.modal.alert(result.message);
				} else {
					icms.modal.alert('Успешно сохранено');
				}
			}, 'json');
		}
	}
	function scPayStatus(btn, order_id){
		var status_id = $(btn).val();
		if (status_id){
			$.post('<?php echo href_to('showcase', 'set_pay_status'); ?>/' + order_id, {status_id : status_id}, function(result){
				if(result.error){
					icms.modal.alert(result.message);
				} else {
					icms.modal.alert('Успешно сохранено');
				}
			}, 'json');
		}
	}
</script>
<?php $this->addBottom(ob_get_clean()); ?>