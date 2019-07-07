<?php

class actionShowcaseSetDelivery extends cmsAction {

    public function run($id = false){
		
		if (!$this->request->isAjax() || !$id){ cmsCore::error404(); }
		
		$item = $this->model->
			selectOnly('i.id, i.title, i.type, i.price')->
			getData('sc_cart_delivery', $id);
		if (!$item){ return $this->cms_template->renderJSON(array('error' => true)); }

		$session_name = 'sc-' . $this->ctype_name . ':delivery';
		
		if (cmsUser::isSessionSet($session_name)){ 
			cmsUser::sessionUnset($session_name);
		}

		$item['slug'] = href_to('showcase', 'delivery', $id);

		cmsUser::sessionSet($session_name, $item);

		return $this->cms_template->renderJSON(array('error' => cmsUser::isSessionSet($session_name) ? false : true));

    }

}
