<?php

class actionShowcaseCartTransactions extends cmsAction {
	
	public $page_name = 'cart_transactions';

	public function run($is_ajax = false) {
		
		$grid = $this->loadDataGrid($this->page_name);

		if ($is_ajax){

			if(!$this->request->isAjax()) {cmsCore::error404();}

			$this->model->setPerPage(admin::perpage);
			$filter = array();
			$filter_str = $this->request->get('filter', '');
			if ($filter_str){ 
				parse_str($filter_str, $filter);
				if (!empty($filter['order_id'])){
					$filter['order_id'] = preg_replace('/[^0-9]/', '', $filter['order_id']);
					$filter['order_id'] = $filter['order_id'] ? $filter['order_id'] : -1;
				}
				if (!empty($filter['id'])){
					$filter['id'] = preg_replace('/[^0-9]/', '', $filter['id']);
					$filter['id'] = $filter['id'] ? $filter['id'] : -1;
				}
				$this->model->applyGridFilter($grid, $filter);
			}

			$total = $this->model->getDataCount('sc_transactions', false);
			$perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;
			$pages = ceil($total / $perpage);
			
			$items = $this->model->
				select('p.title', 'sys_name')->
				joinLeft('sc_pay_systems', 'p', 'p.id=i.system_id')->
				orderBy('i.date_pub', 'DESC')->
				getData('sc_transactions');

			$this->cms_template->renderGridRowsJSON($grid, $items, $total, $pages);
			$this->halt();

		} else {
			return $this->cms_template->render('backend/' . $this->page_name, array('grid' => $grid));
		}

	}

}