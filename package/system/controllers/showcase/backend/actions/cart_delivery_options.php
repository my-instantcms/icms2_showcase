<?php

class actionShowcaseCartDeliveryOptions extends cmsAction {
	
	public $page_name = 'cart_delivery';

    public function run(){
		
		$options = cmsController::loadOptions($this->name);
		$item['delivery_text'] = !empty($options['delivery_text']) ? $options['delivery_text'] : '';
		$item['delivery_center'] = !empty($options['delivery_center']) ? $options['delivery_center'] : '55.76018923, 37.62209300';
		$item['delivery_provider'] = !empty($options['delivery_provider']) ? $options['delivery_provider'] : 'yandex';
		
        $form = new cmsForm();
		$form->addFieldset('Описание доставки', 0);
		$form->addField(0, new fieldHtml('delivery_text'));
		
		$form->addFieldset('Работа с картой', 1);
		$form->addField(1, new fieldScSelectMap('delivery_center', array(
			'title' => 'Центр города по умолчанию'
		)));
		$form->addField(1, new fieldList('delivery_provider', array(
			'title' => 'Провайдер карты',
			'default' => 'yandex',
			'items' => array(
				'yandex' => 'Yandex карта',
				'google' => 'Google карта'
			)
		)));

        if ($this->request->has('submit')) {
            $item = $form->parse($this->request, true);
            $errors = $form->validate($this, $item);
            if(!$errors) {
				$options['delivery_text'] = $item['delivery_text'];
				$options['delivery_center'] = !empty($item['delivery_center']) ? $item['delivery_center'] : '55.76018923, 37.62209300';
				$options['delivery_provider'] = !empty($item['delivery_provider']) ? $item['delivery_provider'] : 'yandex';
                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');
                cmsController::saveOptions($this->name, $options);
                $this->processCallback(__FUNCTION__, array($options));
                $this->redirectToAction('cart_delivery');
            } else {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/' . $this->page_name . '_form', array(
			'do' 	 => 'edit',
			'form' 	 => $form,
			'item' 	 => isset($item) ? $item : false,
			'errors' => isset($errors) ? $errors : false,
			'id' 	 => false
		));
    }
}

class fieldScSelectMap extends cmsFormField {

	public function getInput($value){

		$tpl_file = cmsTemplate::getInstance()->getTemplateFileName('controllers/showcase/fields/sc_select_map');

        ob_start();

        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);

        return ob_get_clean();

    }

}