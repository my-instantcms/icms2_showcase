<?php

class actionShowcaseAddPreorder extends cmsAction {

    public function run($item_id = false, $variant_id = false){
		
		if (!$this->request->isAjax() || !$item_id){ cmsCore::error404(); }
		
		$item = $this->model->getGoods($this->ctype_name, $item_id);
		if (!$item){ cmsCore::error404(); }
		
		$variant = false;
		if ($variant_id){
			$variant = $this->model->getData('sc_variations', $variant_id);
			if (!$variant){ cmsCore::error404(); }
		}
		
		$form = $this->getForm('preorder', array($variant_id, mb_strimwidth($item['title'], 0, 30, "...")));

		if ($this->request->get('submit')){

			$data = $form->parse($this->request, true);
			$errors = $form->validate($this, $data);
			
			if (!$errors){
				$result = $this->model->saveData('sc_preorders', $data);
				if ($result){
					cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');
				} else {
					cmsUser::addSessionMessage('Не удалось сохранить данные.', 'error');
				}
			} else {
				cmsUser::addSessionMessage(LANG_FORM_ERRORS,'error');
			}
			
		}
		
		$this->cms_template->render('preorder', array(
			'form' => $form,
			'item' => $item,
			'ctype_name' => $this->ctype_name,
			'data' => isset($data) ? $data : false,
			'errors' => isset($errors) ? $errors : false
		));

    }

}
