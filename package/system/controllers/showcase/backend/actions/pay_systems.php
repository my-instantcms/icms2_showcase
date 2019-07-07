<?php

class actionShowcasePaySystems extends cmsAction {
	
	public $page_name = 'pay_systems';

	public function run($is_ajax = false) {
		
		$grid = $this->loadDataGrid($this->page_name);

		if ($is_ajax){

			if(!$this->request->isAjax()) {cmsCore::error404();}

			$items = $this->model->
				selectOnly('i.*, p.file_action, p.file_form')->
				joinLeft('sc_pay_gateways', 'p', 'p.name=i.gateway_name')->
				orderBy('i.ordering', 'asc')->
				getData('sc_' . $this->page_name);
			
			if ($items){
				foreach ($items as $key => $item){
					$items[$key]['file_action'] = !empty($item['file_action']) ? $item['file_action'] : 'pay_systems_form';
					$items[$key]['file_form'] = !empty($item['file_form']) ? $item['file_form'] : 'pay_systems';
				}
			}

			$this->cms_template->renderGridRowsJSON($grid, $items);
			$this->halt();

		} else {
			return $this->cms_template->render('backend/' . $this->page_name, array('grid' => $grid));
		}

	}

}