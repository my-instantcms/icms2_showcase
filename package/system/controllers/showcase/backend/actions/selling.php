<?php

class actionShowcaseSelling extends cmsAction {

	public function run($do = false) {

		if ($do == 'wait'){
			$orders = $this->model->
				filterIn('i.status', array(1, 2, 3))->
				orderBy('i.date', 'DESC')->
				getData('sc_checkouts');
		} else if($do == 'failed'){
			$orders = $this->model->
				filterEqual('i.status', 5)->
				orderBy('i.date', 'DESC')->
				getData('sc_checkouts');
		} else {
			$orders = $this->model->
				filterEqual('i.status', 4)->
				orderBy('i.date', 'DESC')->
				getData('sc_checkouts');
		}
		
		return $this->cms_template->render('backend/selling', array(
			'orders' => $orders,
			'do' => $do,
		));

	}

}