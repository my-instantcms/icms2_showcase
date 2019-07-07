<?php

class actionShowcaseDelivery extends cmsAction {

    public function run($id = false){
		
		$ctype = $this->model->
			useCache('content.types')->
			selectOnly('i.id, i.name, i.title')->
			getItemByField('content_types', 'name', $this->ctype_name);

		if ($ctype){
			$this->cms_template->addBreadcrumb($ctype['title'], href_to($ctype['name']));
		}
		
		if ($id){
			$delivery = $this->model->filterEqual('i.is_pub', 1)->getData('sc_cart_delivery', $id);
			return $this->cms_template->render('delivery_view', array(
				'delivery' => $delivery,
				'delivery_text' => !empty($this->options['delivery_text']) ? $this->options['delivery_text'] : false
			));
		}
		
		$courier_delivery = $this->model->
			filterEqual('i.is_pub', 1)->
			filterEqual('i.type', 'courier')->
			orderBy('i.ordering', 'asc')->
			getData('sc_cart_delivery');
			
		$pickup_delivery = $this->model->
			filterEqual('i.is_pub', 1)->
			filterEqual('i.type', 'pickup')->
			orderBy('i.ordering', 'asc')->
			getData('sc_cart_delivery');
		
        return $this->cms_template->render('delivery', array(
			'courier_delivery' => $courier_delivery,
			'pickup_delivery' => $pickup_delivery,
			'delivery_text' => !empty($this->options['delivery_text']) ? $this->options['delivery_text'] : false
		));

    }

}
