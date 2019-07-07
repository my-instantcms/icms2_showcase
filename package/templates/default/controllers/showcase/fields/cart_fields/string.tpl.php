<?php 
	$value = !empty($values[$field['name']]) ? $values[$field['name']] : '';
	if (!$value && !empty($this->controller->cms_user->{$field['name']})){
		$value = $this->controller->cms_user->{$field['name']};
	} else if(!$value && $field['name'] == 'name'){
		$value = $this->controller->cms_user->nickname;
	}
?>
<input 
	id="<?php html($field['name']); ?>" 
	name="<?php html($field['name']); ?>" 
	<?php if (!empty($field['is_fixed'])){ ?>required <?php } ?>
	<?php if ($field['attributes'] && is_array($field['attributes'])){ ?>
		<?php foreach ($field['attributes'] as $key => $val){ ?>
			<?php if ($key == 'id' || $key == 'name'){ continue; } ?>
			<?php html($key); ?>="<?php html($val); ?>" 
		<?php } ?>
	<?php } ?>
	type="text" 
	value="<?php html($value); ?>" 
/>
<style>.sc_cField_value #<?php html($field['name']); ?>{width:100%;padding:6px 8px;border:1px solid #ddd;background:#f7f7f7;outline:none}</style>