<?php

class actionShowcasePaySystemsForm extends cmsAction {
	
	public $page_name = 'pay_systems';

    public function run($id = false, $file_form = false){
		
		if ($id){
			$item = $this->model->getData('sc_' . $this->page_name, $id);
			if (!$item){ cmsCore::error404(); }
			if (!empty($item['gateway_name'])){
				$gateway = $this->model->filterEqual('i.name', $item['gateway_name'])->getData('sc_pay_gateways', 0, 1);
				$file_form = ($gateway && !empty($gateway['file_form'])) ? $gateway['file_form'] : $file_form;
			}
		}

		if (!$file_form){
			$gateways = $this->model->filterEqual('i.is_pub', 1)->getData('sc_pay_gateways');
			return $this->cms_template->render('backend/' . $this->page_name . '_choice', array(
				'gateways' 	=> $gateways,
				'item' 	 	=> isset($item) ? $item : false,
				'id' 		 => $id ? $id : 0
			));
		}
		
		$file_form = ($file_form && $file_form != 'none') ? $file_form : $this->page_name;

        $form = $this->getForm($file_form, array($this->ctype_name));

        if ($this->request->has('submit')) {
            $item = $form->parse($this->request, true);
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