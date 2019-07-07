<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php if(isset($field->prefix)){ ?>
    <div class="input-prefix-suffix">
        <?php if(isset($field->prefix)) { ?><span class="prefix"><?php echo $field->prefix; ?></span><?php } ?>
        <?php echo html_input('text', $field->element_name, $value, array('id' => $field->element_name)); ?>
    </div>
<?php } ?>
<script>
	$(document).ready(function(){
		$('#font_icon').iconpicker("#font_icon");
	});
</script>