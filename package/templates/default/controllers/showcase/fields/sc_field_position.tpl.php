<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php 
	$items = array(
		0 => 'Выбрать позицию',
		1 => 'Позиция 1',
		2 => 'Позиция 2',
		3 => 'Позиция 3',
		5 => 'Позиция 5 (после вкладки)'
	);
	echo html_select($field->element_name, $items, $value, array('id' => $field->element_name));
?>
<a href="/templates/default/controllers/showcase/img/fields_position.png" style="display:inline-block;border:1px solid red;background:#fff5f5;color:red;height:20px;line-height:20px;padding:3px 6px;border-radius:2px;text-decoration:none" class="ajax-modal">Что за позиции?</a>