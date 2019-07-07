<?php

	$this->setPageTitle('Корзина');
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$arrs = get_defined_vars();
	$arrs['data']['next'] = !empty($steps['next']['id']) ? $steps['next']['id'] : 0;
	if ($steps['current']['id'] == 0){
		$tpl = 'cart_big';
	} else if (!empty($steps['current']['hook'])){
		$tpl = 'steps/cart_step_' . $steps['current']['hook'];
	} else {
		$tpl = 'cart_big';
	}
	if (!isset($items) || !$items){
		$tpl = 'cart_big';
	}
	if ($tpl == 'cart_big' && $device_type == 'mobile'){
		$tpl = 'cart_normal';
	}
	
?>
<?php
	$this->renderControllerChild('showcase/tpl', $tpl, $arrs['data']);
?>