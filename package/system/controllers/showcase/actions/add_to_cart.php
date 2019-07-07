<?php

class actionShowcaseAddToCart extends cmsAction {

    public function run($item_id = false, $remove = false){
		
		if (!$this->request->isAjax() || !$item_id){ cmsCore::error404(); }
		
		$ctype_name = $this->ctype_name;
		
		$session_name = 'sc-' . $ctype_name . ':' . $item_id;
		
		if ($remove){
			if (cmsUser::isSessionSet($session_name)){ 
				cmsUser::sessionUnset($session_name);
				return $this->cms_template->renderJSON(array('error' => false));
			}
			return $this->cms_template->renderJSON(array('error' => true));
		}
		
		$data = $this->request->getAll();
		if ($data){
			if (cmsUser::isSessionSet($session_name)){ 
				cmsUser::sessionUnset($session_name);
			}
			cmsUser::sessionSet($session_name, $data);
			return $this->cms_template->renderJSON(array('error' => false));
		}
		
		return $this->cms_template->renderJSON(array('error' => true));

    }

}
