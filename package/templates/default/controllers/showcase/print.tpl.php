<html>
<head>
<title>Печать</title>
<style>
body{font-size:14px}
.sc_print_box, .sc_print_box *{
	-webkit-box-sizing: border-box !important;
    -moz-box-sizing: border-box !important;
    box-sizing: border-box !important;
    line-height: 21px;
}
.sc_print_box{
	width:877px;
	margin:auto
}
.sc_print_box .sc_print_head{
    margin-bottom: 15px;
    overflow: hidden;
}
.sc_print_box .sc_print_id{
    width: 50%;
    float: left;
}
.sc_print_box .sc_print_meneger{
    width: 50%;
    float: left;
}

.sc_print_box .sc_print_fields .sc_print_field{
    margin-bottom: 5px;
    overflow: hidden;
}
.sc_print_box .sc_print_fields .sc_print_field > b{
    width: 50%;
    display: inline-block;
    float: left;
}
.sc_print_box .sc_print_fields .sc_print_field > span{
    width: 50%;
    display: inline-block;
    float: left;
}
.sc_print_box .sc_print_items{
    margin-top: 15px;
    border-collapse: collapse;
    width: 100%;
    border: 1px solid #ddd;
}
.sc_print_box .sc_print_items .sc_print_tr_head{
    background: #eee;
    font-weight: bold;
}
.sc_print_box .sc_print_items .sc_print_tr_head td{padding: 5px 8px;}
.sc_print_box .sc_print_items td{
    border: 1px solid #ddd;
    padding: 4px 6px;
}
</style>
</head>
<body>
<div class="sc_print_box" onload="window.print()">
	<div class="sc_print_head">
		<div class="sc_print_id"><b>№ заказа:</b> <?php html($order['id']); ?></div>
		<div class="sc_print_meneger"><b>Менеджер:</b> <?php html($this->controller->cms_user->nickname); ?></div>
	</div>
	<div class="sc_print_fields">
		<?php if (!empty($order['fields'])){ ?>
			<?php foreach ($order['fields'] as $key => $value){ ?>
				<?php if ($key == 'agreement' || !$value || empty($cart_fields[$key])){ continue; } ?>
				<div class="sc_print_field sc_print_<?php html($key); ?>">
					<b><?php html($cart_fields[$key]['title']); ?>:</b> 
					<span>
						<?php 
							if ($cart_fields[$key]['type'] == 'select' && !empty($cart_fields[$key]['options'][$value])){
								echo $cart_fields[$key]['options'][$value];
							} else if ($key == 'paid'){
								if ($value != 1 || isset($order['fields']['payment_system']) && $order['fields']['payment_system'] == 0){
									echo 'Наличные, при получении';
								} else if (!empty($order['fields']['payment_system']) && $system){
									echo $system['title'];
								} else {
									echo 'Из баланса';
								}
							} else {
								echo $value;
							}
						?>
					</span>
				</div>
			<?php } ?>
		<?php } ?>
		<?php if (!empty($order['delivery'])){ ?>
			<div class="sc_print_field sc_print_delivery">
				<?php $delivery = cmsModel::yamlToArray($order['delivery']); ?>
				<b><?php echo ($delivery['type'] == 'courier') ? 'Курьерская доставка' : 'Самовывоз'; ?>: </b> 
				<span>
					<?php html($delivery['title']); ?> - 
					<b><?php echo $delivery['price'] ? $delivery['price'] . ' ' . (!empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY) : (isset($delivery['price']) ? 'Бесплатно' : 'Не указана'); ?></b>
				</span>
			</div>
		<?php } ?>
	</div>
	<?php if (!empty($order['items'])){ ?>
		<table class="sc_print_items">
			<tbody>
				<tr class="sc_print_tr_head">
					<td>Артикул</td>
					<td>Наименование товара</td>
					<td>Количество</td>
				</tr>
				<?php foreach ($order['items'] as $id => $good){ ?>
					<tr>
						<td><?php echo $good['artikul']; ?></td>
						<td><?php echo $good['title']; ?></td>
						<td><?php echo $good['qty']; ?></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } ?>
</div>
<script>
	window.onload = function() { window.print(); }
</script>
</body>
</html>