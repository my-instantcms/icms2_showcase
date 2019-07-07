<?php 
	$value = !empty($values[$field['name']]) ? $values[$field['name']] : '';
?>
<select 
	id="<?php html($field['name']); ?>" 
	name="paid" 
	<?php if ($field['attributes'] && is_array($field['attributes'])){ ?>
		<?php foreach ($field['attributes'] as $key => $val){ ?>
			<?php if ($key == 'id' || $key == 'name'){ continue; } ?>
			<?php html($key); ?>="<?php html($val); ?>" 
		<?php } ?>
	<?php } ?>
>
	<option value="0" selected>Наличные, при получении</option>
	<?php if (cmsUser::get('id')){ ?>
		<option value="1">Из баланса (Ваш баланс: <?php echo @cmsUser::get('balance') ? cmsUser::get('balance') : 0; ?>)</option>
	<?php } else { ?>
		<option value="1" disabled>Из баланса (Требуется регистрация)</option>
	<?php } ?>
</select>
<style>.sc_cField_value #<?php html($field['name']); ?>{width:100%;padding:6px 8px;border:1px solid #FFC107;background:#fbee81;outline:none;height: auto;box-shadow: none;color:#a06200}.sc_field_payment{background:#fdf292;border:1px solid #f1dc27;color:#d8860e;padding:10px}</style>