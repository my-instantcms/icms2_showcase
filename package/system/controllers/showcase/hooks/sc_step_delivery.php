<?php

class onShowcaseScStepDelivery extends cmsAction {

    public function run($data){
			
		$steps			= !empty($data['steps']) ? $data['steps'] : $this->getNextStep(0);
		$step			= !empty($steps['current']['id']) ? $steps['current']['id'] : 0;
		$device_type	= !empty($data['device_type']) ? $data['device_type'] : cmsRequest::getDeviceType();
		
		$session_name = 'sc-' . $this->ctype_name . ':delivery';
		cmsUser::sessionUnset($session_name);
		
		$data['courier_delivery'] = $this->model->
			filterEqual('i.is_pub', 1)->
			filterEqual('i.type', 'courier')->
			orderBy('i.ordering', 'asc')->
			getData('sc_cart_delivery');
			
		$data['pickup_delivery'] = $this->model->
			filterEqual('i.is_pub', 1)->
			filterEqual('i.type', 'pickup')->
			orderBy('i.ordering', 'asc')->
			getData('sc_cart_delivery');
			
		$data['delivery_text'] = !empty($this->options['delivery_text']) ? $this->options['delivery_text'] : false;
		
		return $data;

    }

}
