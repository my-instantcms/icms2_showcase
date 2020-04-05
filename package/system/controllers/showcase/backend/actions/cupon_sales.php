<?php

class actionShowcaseCuponSales extends cmsAction {
	
	public $page_name = 'cupon_sales';

	public function run($is_ajax = false) {
		
		$grid = $this->loadDataGrid('sales');

		if ($is_ajax){

			if(!$this->request->isAjax()) {cmsCore::error404();}
			
			$filter     = array();
			$filter_str = $this->request->get('filter', '');

			if ($filter_str){
				parse_str($filter_str, $filter);
				$this->model->applyGridFilter($grid, $filter);
			}

			$grid['filter'] = $filter;
			
			$total = $this->model->getDataCount('sc_sales', false);

			$perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;

			$this->model->setPerPage($perpage);

			$pages = ceil($total / $perpage);

			$items = $this->model->getData('sc_sales');
			
			$this->cms_template->renderGridRowsJSON($grid, $items, $total, $pages);
			$this->halt();

		} else {
			return $this->cms_template->render('backend/' . $this->page_name, array('grid' => $grid));
		}

	}

}