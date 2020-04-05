<?php

class actionShowcaseGetAjaxVariants extends cmsAction {

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }
		if (!cmsUser::isAdmin()) { cmsCore::error404(); }

		$item_id = $this->request->get('value', 0);
		if (!$item_id) { cmsCore::error404(); }
		
		$variants = $this->model->filterEqual('i.item_id', $item_id)->getData('sc_variations');

		if ($variants){
			$list = array(''=>'') + array_collection_to_list($variants, 'id', 'title');
		} else {
			$list = array(''=>'');
		}

		return $this->cms_template->renderJSON($list);

    }

}
