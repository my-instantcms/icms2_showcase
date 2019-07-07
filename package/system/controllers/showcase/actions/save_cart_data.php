<?php

class actionShowcaseSaveCartData extends cmsAction {

    public function run(){
		
		if (!$this->request->isAjax()){ cmsCore::error404(); }
		
		$data = $this->request->getAll();
		
		if (!$data){ cmsCore::error404(); }

		if (cmsUser::isSessionSet('cart_fields_values')){ cmsUser::sessionUnset('cart_fields_values'); }

		cmsUser::sessionSet('cart_fields_values', $data);

		return $this->cms_template->renderJSON(array('error' => false));

    }

}
