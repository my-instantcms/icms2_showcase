<?php

class actionShowcaseColorsForm extends cmsAction {
	
	public $page_name = 'colors';

    public function run($id = false){
		
		if ($id){
			$item = $this->model->getData('sc_' . $this->page_name, $id);
			if (!$item){ cmsCore::error404(); }
		}
		
        $form = $this->getForm($this->page_name);
        $is_submitted = $this->request->has('submit');

        if ($is_submitted) {
            $item = $form->parse($this->request, $is_submitted);
            $errors = $form->validate($this, $item);
            if(!$errors) {
                $result = $id ? $this->model->updData('sc_' . $this->page_name, $id, $item) : $this->model->saveData('sc_' . $this->page_name, $item);
				if ($result){
					cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');
				} else {
					cmsUser::addSessionMessage('Не удалось сохранить данные.', 'error');
				}
                $this->redirectToAction($this->page_name);
            } else {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/' . $this->page_name . '_form', array(
			'do' 	 => $id ? 'edit' : 'add',
			'form' 	 => $form,
			'item' 	 => isset($item) ? $item : false,
			'errors' => isset($errors) ? $errors : false,
			'id' 	 => $id
		));
    }
}