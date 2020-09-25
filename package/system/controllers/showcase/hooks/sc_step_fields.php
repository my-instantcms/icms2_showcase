<?php

class onShowcaseScStepFields extends cmsAction {

    public function run($data){

		$steps			= !empty($data['steps']) ? $data['steps'] : $this->getNextStep(0);
		$step			= !empty($steps['current']['id']) ? $steps['current']['id'] : 0;
		$device_type	= !empty($data['device_type']) ? $data['device_type'] : cmsRequest::getDeviceType();

		$data['cart_fields'] = $this->model->
			filterEqual('i.is_pub', 1)->
			orderBy('i.ordering', 'asc')->
			getData('sc_cart_fields', false, false, false, 'name');

		if (!empty($data['cart_fields']['paid'])){
			if (empty($this->options['payment']) || $this->options['payment'] == 'off'){
				unset($data['cart_fields']['paid']);
			} else if (!empty($this->options['payment']) && $this->options['payment'] == 'system'){
				$data['cart_fields']['paid']['type'] = 'system_payment';
				if (!empty($this->options['system_pay_cash'])){
					$data['cart_fields']['paid']['systems'][0] = $this->model->getCashPaySystem();
				}
				if (!empty($this->options['system_pay_check'])){
					$data['cart_fields']['paid']['systems'][999] = $this->model->getCheckPaySystem();
				}
				$systems = $this->model->
					filterEqual('i.is_pub', 1)->
					orderBy('i.ordering', 'ASC')->
					getData('sc_pay_systems');
				$data['cart_fields']['paid']['systems'] = (!empty($data['cart_fields']['paid']['systems'][0]) || !empty($data['cart_fields']['paid']['systems'][999])) ? array_merge($data['cart_fields']['paid']['systems'], ($systems ? $systems : array())) : $systems;
			}
		}

		$data['values'] = cmsUser::isSessionSet('cart_fields_values') ? cmsUser::sessionGet('cart_fields_values') : false;

		return $data;

    }

}
