<?php

class actionShowcaseDeliveryMap extends cmsAction {

    public function run($type = 'view', $field_or_id = false, $value = false){
		
		if ($type == 'view'){
			$pickup = $this->model->filterNotNull('i.pickup_map')->getData('sc_cart_delivery', $field_or_id);
			if (!$pickup){ cmsCore::error404(); }
			$value = $pickup['pickup_map'];
		} else {
			$value = $value ? $value : (!empty($this->options['delivery_center']) ? $this->options['delivery_center'] : '55.76018923, 37.62209300');
		}
		
        return $this->cms_template->render('delivery_map', array(
			'pickup' => isset($pickup) ? $pickup : false,
			'value' => $value,
			'field_or_id' => $field_or_id,
			'provider' => !empty($this->options['delivery_provider']) ? $this->options['delivery_provider'] : 'yandex'
		));

    }

}
