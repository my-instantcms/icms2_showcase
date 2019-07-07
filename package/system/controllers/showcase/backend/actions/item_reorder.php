<?php
class actionShowcaseItemReorder extends cmsAction {

    public function run($table = false) {
		
		if (!$table){ cmsCore::error404(); }

        $items = $this->request->get('items', array());
        if (!$items) { cmsCore::error404(); }

		if ($items[0] == 'undefined') { unset($items[0]); }
        $this->model->reorderData($table, $items);
		
		if($this->request->isAjax()) {
			return $this->cms_template->renderJSON(array('error' => false));
		} else {
			$this->redirectBack();
		}

    }

}