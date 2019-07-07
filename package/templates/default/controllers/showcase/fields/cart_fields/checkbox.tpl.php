<?php 
	$value = !empty($values[$field['name']]) ? $values[$field['name']] : '';
?>
<?php if (!empty($field['options']['label'])){ ?><label><?php } ?>
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
	type="checkbox" 
	value="<?php html($value); ?>" 
/>
<?php if (!empty($field['options']['label'])){ ?>
	<span class="checkmark"></span> <?php echo $field['options']['label']; ?></label><?php } ?>
<style>
.sc_field_<?php html($field['name']); ?> .sc_cField_value label{
    display: block;
    position: relative;
    padding-left: 35px;
	height: 25px;
    line-height: 26px;
    margin-bottom: 0;
    cursor: pointer;
    font-size: 14px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

.sc_field_<?php html($field['name']); ?> .sc_cField_value label input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.sc_field_<?php html($field['name']); ?> .sc_cField_value label .checkmark {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #eee;
}

.sc_field_<?php html($field['name']); ?> .sc_cField_value label:hover input ~ .checkmark {
    background-color: #ccc;
}

.sc_field_<?php html($field['name']); ?> .sc_cField_value label input:checked ~ .checkmark {
    background-color: #2196F3 !important;
}

.sc_field_<?php html($field['name']); ?> .sc_cField_value label .checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.sc_field_<?php html($field['name']); ?> .sc_cField_value label input:checked ~ .checkmark:after {
    display: block;
}

.sc_field_<?php html($field['name']); ?> .sc_cField_value label .checkmark:after {
    left: 9px;
    top: 5px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
</style>