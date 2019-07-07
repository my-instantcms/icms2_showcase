<?php
	$style = !empty($widget->options['style']) ? 'cart_' . $widget->options['style'] : 'cart_normal';

	$this->renderControllerChild('showcase/tpl', $style, array(
		'items' => !empty($items) ? $items : false,
		'fields' => !empty($items) ? $fields : false,
		'summ' => !empty($items) ? $summ : false,
		'count' => !empty($count) ? $count : false,
		'ctype_name' => !empty($items) ? $ctype_name : false,
		'steps' => !empty($steps) ? $steps : false,
		'widget' => $widget
	));
?>