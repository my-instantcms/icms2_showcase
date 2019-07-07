<?php

class actionShowcaseCartDelivery extends cmsAction {
	
	public $page_name = 'cart_delivery';

	public function run($is_ajax = false) {
		
		$grid = $this->loadDataGrid($this->page_name);

		if ($is_ajax){

			if(!$this->request->isAjax()) {cmsCore::error404();}

			$items = $this->model->orderBy('i.ordering', 'asc')->getData('sc_' . $this->page_name);
			
			$this->cms_template->renderGridRowsJSON($grid, $items);
			$this->halt();

		} else {
			return $this->cms_template->render('backend/' . $this->page_name, array('grid' => $grid));
		}

	}

}