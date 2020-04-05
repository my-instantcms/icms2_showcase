<?php
	$style = !empty($widget->options['style']) ? 'cart_' . $widget->options['style'] : 'cart_normal';
	$all_vars = get_defined_vars();
	$this->renderControllerChild('showcase/tpl', $style, $all_vars['data'] + array('widget' => $widget));
?>