<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_textarea($field->element_name, $value, array('rows'=>$field->getOption('size'), 'id'=>$field->id, 'required'=>(array_search(array('required'), $field->getRules()) !== false))); ?>
<div class="add_key_block" id="akb_<?php html($field->element_name); ?>">
	<input type="text" id="key" placeholder="ключ" /> 
	<input type="text" id="value" placeholder="значение" /> 
	<input type="button" id="button" value="OK" onclick="akb_add('<?php html($field->element_name); ?>')"  /> 
</div>
<a onclick="akb_toggle('<?php html($field->element_name); ?>')" class="akb_toggle"><?php html($field->title_add); ?></a>
<?php ob_start(); ?>
<script type="text/javascript">
	function akb_toggle(name){
		$('.add_key_block#akb_' + name).toggle();
	}
	function akb_add(name){
		var key = $('.add_key_block#akb_' + name + ' #key').val();
		var value = $('.add_key_block#akb_' + name + ' #value').val();
		var text = $('#' + name).val();
		if (text){
			if (key){
				$('#' + name).val(text + "\n" + key + ' | ' + value);
			}
		} else {
			if (key){
				$('#' + name).val(key + ' | ' + value);
			}
		}
		$('.add_key_block#akb_' + name + ' input[type="text"]').val('');
		$('.add_key_block#akb_' + name).hide();
	}
</script>
<?php $this->addBottom(ob_get_clean()); ?>
<style>
	.akb_toggle{background: #eee;border: 1px solid #ddd;padding: 3px 5px;display: inline-block;cursor: pointer;}
	.add_key_block{position: absolute;background: #fff;display:none}
	.add_key_block input{width: 110px;padding: 3px;}
	.add_key_block input#value{width:130px;margin: 0 -5px;}
	.add_key_block input#button{width: 40px;}
</style>