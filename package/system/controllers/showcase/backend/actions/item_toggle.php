<?php
class actionShowcaseItemToggle extends cmsAction {

    public function run($table = false, $id = false){

		if (!$table || !$id){ $this->cms_template->renderJSON(array('error' => true)); }

        $item = $this->model->getData($table, $id);
		if (!$item){ $this->cms_template->renderJSON(array('error' => true)); }

		$is_pub = !empty($item['is_pub']) ? false : true;

		$this->model->update($table, $id, array('is_pub' => $is_pub));

		$this->cms_template->renderJSON(array('error' => false,'is_on' => $is_pub));

    }

}
