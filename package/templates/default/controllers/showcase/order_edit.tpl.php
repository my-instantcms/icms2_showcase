<?php 
	if ($is_manager){
		$this->addBreadcrumb('Заказы', $this->href_to('orders', array(0, $order['status'])));
	} else if (empty($order['user_id'])){
		
	} else {
		$this->addBreadcrumb(LANG_USERS, href_to('users'));
		$this->addBreadcrumb($this->controller->cms_user->nickname, href_to('users', $order['user_id']));
		$this->addBreadcrumb('Мои заказы', href_to('users', $order['user_id'], 'orders'));
	}
    $this->addBreadcrumb('Заказ №' . $order['id'], $this->href_to('orders', array($order['id'], $order['status'])));
    $this->addBreadcrumb(LANG_EDIT);
    $this->setPageTitle(LANG_EDIT);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/tab.css', false));
	$pay = false;
	$this->addTplJSNameFromContext([
    'jquery-ui',
    'i18n/jquery-ui/'.cmsCore::getLanguageName()
    ]);
	$this->addTplCSSNameFromContext('jquery-ui');
?>
<table class="sc_order_view sc_order_edit">
	<tbody>
		<?php foreach ($order as $name => $val){ ?>
			<?php if ($name == 'id' || $name == 'shop_id' || $name == 'coupon' || $name == 'extra'){ continue; } ?>
			<tr class="sc_ol_item">
				<td class="sc_oli_title"><?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?></td>
				<td class="sc_oli_val">
					<?php if ($name == 'fields'){ ?>
						<table>
							<tbody>
								<?php foreach ($val as $key => $v){ ?>
									<?php if ($key == 'agreement' || $key == 'sale_id' || $key == 'payment_system'){ continue; } ?>
									<tr>
										<td><b><?php html($cart_fields[$key]['title']); ?></b></td>
										<td class="order_fields_td"><?php 
											if ($cart_fields[$key]['type'] == 'select' && !empty($cart_fields[$key]['options'][$v])){
												echo $cart_fields[$key]['options'][$v];
											} else if ($key == 'paid'){
												if ($v != 1){
													echo 'Наличные, при получении';
												} else if (isset($val['payment_system']) && $system){
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
					<?php } else if ($name == 'delivery' && $deliverys){ ?>
						<?php
							$val = cmsModel::yamlToArray($val);
							echo html_select($name, $deliverys, $val['id']);
							if ($val['type'] == 'courier'){
								$status[3] = 'Доставляется';
							} else {
								$status[3] = 'Ожидает получения';
							}
						?>
					<?php } else if ($name == 'price'){ ?>
						<input type="number" class="price_input" name="price" placeholder="<?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?>" value="<?php html($val); ?>" />
						<span><?php echo !empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY; ?></span>
					<?php } else if ($name == 'sale_id'){ ?>
						<?php echo html_select($name, array(0 => LANG_SELECT) + array_collection_to_list($sales, 'id', 'title'), $val); ?>
					<?php } else if ($name == 'user_id'){ ?>
						<?php if (empty($order['user_id'])){ ?>
							<input type="text" value="<?php echo !empty($order['fields']['name']) ? $order['fields']['name'] : LANG_GUEST; ?>" />
						<?php } else { ?>
							<?php echo html_select($name, array(0 => LANG_SELECT) + array_collection_to_list($users, 'id', 'nickname'), $val); ?>
						<?php } ?>
					<?php } else if ($name == 'status'){ ?>
						<?php if ($is_manager){ ?>
							<?php echo html_select('status', $status, $val, array('onchange' => 'scSetStatus(this, ' . $order['id'] . ')')); ?>
						<?php } else { ?>
							<?php html($status[$val]); ?>
						<?php } ?>
					<?php } else if ($name == 'date'){ ?>
						<?php
							if(!$val){
								$hours = 0;
								$mins = 0;
							} else {
								list($hours, $mins) = explode(':', date('H:i', strtotime($val)));
							}
						?>
						<?php echo html_datepicker($name . '[date]', ($val ? date('d.m.Y', strtotime($val)) : ''), array('id'=>$name), array('maxDate'=>'0')); ?>
						<?php echo html_select_range($name . '[hours]', 0, 23, 1, true, $hours); ?> :
						<?php echo html_select_range($name . '[mins]', 0, 59, 1, true, $mins); ?>
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
						<div class="sc_order_view_goods">
							<?php foreach ($val as $index => $good){ ?>
								<div class="sc_order_view_good">
									<?php echo html_select($name . '[' . $index . '][item_id]', array_collection_to_list($goods, 'id', 'title'), $good['id'], array('id' => $name . '_' . $index)); ?>
									<?php echo html_select($name . '[' . $index . '][variant]', array_collection_to_list($variants, 'id', 'title'), $good['variant_id'], array('id' => $name . '_variant_' . $index)); ?>
									<input type="number" class="scGoodEditQty" name="<?php html($name . '[' . $index . '][qty]'); ?>" placeholder="Количество" value="<?php html($good['qty']); ?>" />
									<a href="javascript:void(0)" onClick="swDeleteGoods(this)" class="scovg_delete">X</a>
									<?php ob_start(); ?>
									<script type="text/javascript">
										$(document).ready(function() {
											$("#<?php echo $name . '_' . $index; ?>").change(function () {
												icms.forms.updateChildList('<?php echo $name . '_variant_' . $index; ?>', '<?php echo href_to("showcase", "get_ajax_variants"); ?>', $(this).val(), "<?php html($good['variant_id']); ?>");
											}).change();
										});
									</script>
									<?php $this->addBottom(ob_get_clean()); ?>
								</div>
							<?php } ?>
							<a href="javascript:void(0)" class="scovg_add_goods" onClick="swCloneGoods(this)">Добавить товар</a>
						</div>
					<?php } else { ?>
						<?php html($val); ?>
					<?php } ?>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td colspan="2" class="sc_order_btns">
				<a href="javascript:void(0)" onClick="swSaveOrder(this)"><i class="fa fa-save"></i> <?php html(LANG_SAVE); ?></a>
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
	function swCloneGoods(btn){
		var block = $('.sc_order_view_goods .sc_order_view_good').eq(0).clone();
		block.insertBefore(btn);
	}
	function swDeleteGoods(btn){
		if ($('.sc_order_view_goods .sc_order_view_good').length == 1){
			return;
		}
		var parent = $(btn).parent();
		parent.remove();
	}
	var save_data = {};
	function swSaveOrder(btn){
		$(".sc_order_edit input, .sc_order_edit select").each(function(index) {
			var name = $(this).attr('name');
			var value = $(this).val();
			save_data[name] = value;
		});
		if (!$.isEmptyObject(save_data)){
			$.post('<?php echo href_to('showcase', 'order_save'); ?>/' + <?php html($order['id']); ?>, {data : save_data}, function(result){
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