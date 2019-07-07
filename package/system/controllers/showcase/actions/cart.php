<?php

class actionShowcaseCart extends cmsAction {

    public function run($step = 0){
		
		if ($step === 'checkout'){
			$this->runExternalAction('checkout', array_slice($this->params, 1));
		}
		
		$ctype = $this->model->
			useCache('content.types')->
			selectOnly('i.id, i.name, i.title')->
			getItemByField('content_types', 'name', $this->ctype_name);

		if ($ctype){
			$this->cms_template->addBreadcrumb($ctype['title'], href_to($ctype['name']));
		}

		$steps = $this->getNextStep($step);
		$device_type = cmsRequest::getDeviceType();
		
		$return_data = array(
			'steps' => $steps,
			'device_type' => $device_type
		);

		if ($step != 0 && !empty($steps['current']['hook'])){
			$return_data = cmsEventsManager::hook('sc_step_' . $steps['current']['hook'], $return_data);
		}

		return $this->cms_template->render('cart', $this->renderCartData($return_data));

    }

}
