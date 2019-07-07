<?php

class actionShowcaseSteps extends cmsAction {
	
	public $page_name = 'steps';

	public function run($is_ajax = false) {
		
		$grid = $this->loadDataGrid($this->page_name);

		if ($is_ajax){

			if (!$this->request->isAjax()) { cmsCore::error404(); }

			$steps = cmsCore::getController('showcase')->getStepList(0);
			
			$this->cms_template->renderGridRowsJSON($grid, $steps);
			$this->halt();

		} else {
			return $this->cms_template->render('backend/' . $this->page_name, array('grid' => $grid));
		}

	}

}