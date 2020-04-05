<?php

class actionShowcasePaySystemsOptions extends cmsAction {
	
	public $page_name = 'pay_systems_options';

    public function run(){
		
		$options = cmsController::loadOptions($this->name);
		$item['system_pay_cash'] = !empty($options['system_pay_cash']) ? $options['system_pay_cash'] : '';
		$item['system_pay_check'] = !empty($options['system_pay_check']) ? $options['system_pay_check'] : '';
		$item['pay_check_data'] = !empty($options['pay_check_data']) ? $options['pay_check_data'] : '';
		
        $form = new cmsForm();
		$form->addFieldset(LANG_OPTIONS, 0);
		$form->addField(0, new fieldCheckbox('system_pay_cash', array(
			'title' => 'Включить способ оплаты "Наличные"'
		)));
		$form->addField(0, new fieldCheckbox('system_pay_check', array(
			'title' => 'Включить способ оплаты "По реквизитам"'
		)));
		$form->addField(0, new fieldHtml('pay_check_data', array(
			'title' => 'Реквизиты по оплате',
			'hint' => 'Используйте шаблон для замены слов на значения:<br /><b style="color:red">{order_id}</b> = ID заказа<br /><b style="color:red">{order_price}</b> = Сумма заказа<br />',
			'visible_depend' => array('system_pay_check' => array('show' => array('1')))
		)));

        if ($this->request->has('submit')) {
            $item = $form->parse($this->request, true);
            $errors = $form->validate($this, $item);
            if(!$errors) {
				$options['system_pay_cash'] = !empty($item['system_pay_cash']) ? $item['system_pay_cash'] : '';
				$options['system_pay_check'] = !empty($item['system_pay_check']) ? $item['system_pay_check'] : '';
				$options['pay_check_data'] = !empty($item['pay_check_data']) ? $item['pay_check_data'] : '';
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