<?php

class actionShowcaseVariantsAjax extends cmsAction {

	public function run() {
		
		if(!$this->request->isAjax()) {cmsCore::error404();}

        $data = $this->request->getAll();
        if (!$data){
			return $this->cms_template->renderJSON(array('last_page' => 1, 'data' => array()));
		}
		
		$items = $this->model->
			orderBy('i.ordering', 'ASC')->
			filterIn('i.id', $data)->
			getData('sc_variations');
	
		return $this->cms_template->renderJSON(array_values($items));
		$this->halt();

	}

}