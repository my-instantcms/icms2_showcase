<?php

class actionShowcasePaySystemsOptions extends cmsAction {
	
	public $page_name = 'pay_systems_options';

    public function run(){
		
		$options = cmsController::loadOptions($this->name);
		$item['system_pay_cash'] = !empty($options['system_pay_cash']) ? $options['system_pay_cash'] : '';
		
        $form = new cmsForm();
		$form->addFieldset(LANG_OPTIONS, 0);
		$form->addField(0, new fieldCheckbox('system_pay_cash', array(
			'title' => 'Включить способ оплаты "Наличные"'
		)));

        if ($this->request->has('submit')) {
            $item = $form->parse($this->request, true);
            $errors = $form->validate($this, $item);
            if(!$errors) {
				$options['system_pay_cash'] = !empty($item['system_pay_cash']) ? $item['system_pay_cash'] : '';
                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');
                cmsController::saveOptions($this->name, $options);
                $this->processCallback(__FUNCTION__, array($options));
                $this->redirectToAction('pay_systems');
            } else {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/' . $this->page_name, array(
			'do' 	 => 'edit',
			'form' 	 => $form,
			'item' 	 => isset($item) ? $item : false,
			'errors' => isset($errors) ? $errors : false,
			'id' 	 => false
		));
    }
}