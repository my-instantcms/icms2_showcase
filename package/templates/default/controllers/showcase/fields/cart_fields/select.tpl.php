<?php 
	$value = !empty($values[$field['name']]) ? $values[$field['name']] : '';
?>
<select 
	id="<?php html($field['name']); ?>" 
	name="<?php html($field['name']); ?>" 
	<?php if (!empty($field['is_fixed'])){ ?>required <?php } ?>
	<?php if ($field['attributes'] && is_array($field['attributes'])){ ?>
		<?php foreach ($field['attributes'] as $key => $val){ ?>
			<?php if ($key == 'id' || $key == 'name'){ continue; } ?>
			<?php html($key); ?>="<?php html($val); ?>" 
		<?php } ?>
	<?php } ?>
>
<?php if (!empty($field['options']) && is_array($field['options'])){ ?>
	<?php foreach ($field['options'] as $index => $option){ ?>
		<?php $selected = ($value == $index) ? 'selected' : ''; ?>
		<option value="<?php html($index); ?>" <?php html($selected); ?>><?php html($option); ?></option>
	<?php } ?>
<?php } ?>
</select>
<style>.sc_cField_value #<?php html($field['name']); ?>{width:100%;padding:6px 8px;border:1px solid #ddd;background:#f7f7f7;outline:none;height: auto;box-shadow: none;}</style>