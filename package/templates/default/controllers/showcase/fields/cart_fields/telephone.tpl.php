<?php 
	$this->addCSS($this->getTplFilePath('controllers/showcase/libs/intlTelInput/css/intlTelInput.min.css', false));
	$this->addJS($this->getTplFilePath('controllers/showcase/libs/intlTelInput/js/intlTelInput.min.js', false));
	$value = !empty($values[$field['name']]) ? $values[$field['name']] : '';
	if (!$value && !empty($this->controller->cms_user->{$field['name']})){
		$value = $this->controller->cms_user->{$field['name']};
	} else if(!$value && $field['name'] == 'tel' && !empty($this->controller->cms_user->phone)){
		$value = $this->controller->cms_user->phone;
	}
?>
<input 
	type="text"
	id="<?php html($field['name']); ?>"
	name="<?php html($field['name']); ?>"
	<?php if ($field['attributes'] && is_array($field['attributes'])){ ?>
		<?php foreach ($field['attributes'] as $key => $val){ ?>
			<?php if ($key == 'type' || $key == 'id' || $key == 'name'){ continue; } ?>
			<?php html($key); ?>="<?php html($val); ?>" 
		<?php } ?>
		value="<?php html($value); ?>"
	<?php } ?>
/>
<style>.sc_cField_value #<?php html($field['name']); ?>{width:100%;padding:6px 8px 6px 52px;border:1px solid #ddd;background:#f7f7f7;outline:none}</style>
<?php ob_start(); ?>
<script>
	var <?php html($field['name']); ?>telInput = document.querySelector("#<?php html($field['name']); ?>");
	
	var iti_<?php html($field['name']); ?> = intlTelInput(<?php html($field['name']); ?>telInput, {
		<?php if (!empty($field['options']['only'])){ ?>
			onlyCountries: [<?php echo $field['options']['only']; ?>],
		<?php } ?>
		<?php if (!empty($field['options']['preferred'])){ ?>
			preferredCountries: [<?php echo $field['options']['preferred']; ?>],
		<?php } ?>
		initialCountry: "<?php echo !empty($field['options']['initial']) ? $field['options']['initial'] : 'ru'; ?>",
		utilsScript: "/<?php echo $this->getTplFilePath('controllers/showcase/libs/intlTelInput/js/utils.js', false); ?>"
	});
	
	<?php if ($value){ ?>
		iti_<?php html($field['name']); ?>.setNumber("<?php html($value); ?>");
	<?php } ?>
	
</script>
<?php $this->addBottom(ob_get_clean()); ?>