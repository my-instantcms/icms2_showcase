<?php

class actionShowcaseGoodsEdit extends cmsAction {

	public function run() {
		
		if(!$this->request->isAjax()) { cmsCore::error404(); }

        $id = $this->request->get('id', 0);
        $field = $this->request->get('field', '');
        $oldValue = $this->request->get('oldValue', '');
        $newValue = $this->request->get('newValue', '');
        if (!$id || !$field || !$newValue){ cmsCore::error404(); }
		
		$result = true;
		$message = LANG_ERROR;
		
		switch ($field) {
			case 'title':
				list($result, $message) = $this->goodsFieldEdit($id, $field, $newValue, $result, $message);
				break;
			case 'category_id':
				list($result, $message) = $this->goodsFieldEdit($id, $field, $newValue, $result, $message);
				break;
			case 'price':
				list($result, $message) = $this->goodsFieldEdit($id, $field, $newValue, $result, $message);
				break;
			case 'sale':
				list($result, $message) = $this->goodsFieldEdit($id, $field, $newValue, $result, $message);
				break;
		}

		return $this->cms_template->renderJSON(array('error' => $result ? false : true, 'message' => $message));
		$this->halt();

	}
	
	public function goodsFieldEdit($id, $field, $newValue, $result, $message){
		
		$model = cmsCore::getModel('content');
		
		$ctype = $model->getContentTypeByName($this->ctype_name);
		if (!$ctype) { return array(true, 'Тип контент не найден'); }
		
		$item = $model->getContentItem($ctype['name'], $id);
        if (!$item) { return array(true, 'Запись не найдена'); }
		
		$item[$field] = $newValue;
		
		$fields = $model->orderBy('ordering')->getContentFields($ctype['name'], $id);
		
		$item = cmsEventsManager::hook('content_before_update', $item);
		$item = cmsEventsManager::hook("content_{$ctype['name']}_before_update", $item);
		
		$item = $model->updateContentItem($ctype, $id, $item, $fields);

		if ($item[$field] != $newValue){
			$message = 'Не удалось изменить';
		}
		
		return array($result, $message);
		
	}

}