<?php

class onShowcaseContentCatForm extends cmsAction {

    public function run($form){
		
		if ($this->cms_core->uri_action != $this->ctype_name){ return $form; }

		$form->addField(0, new fieldString('sc_fa', array(
			'title' => 'Иконочный шрифт',
			'prefix' => 'fa ',
			'default' => 'fa-folder'
		)));
		$form->addField(0, new fieldColor('sc_color', array(
			'title' => 'Цвет',
			'default' => '#444'
		)));
		$form->addField(0, new fieldScCatIcon('sc_icon', array('title' => 'Иконка категории', 'default' => 'default.png')));
        return $form;
    }

}

class fieldScCatIcon extends cmsFormField {
	
	public function getInput($value){
		$template = cmsTemplate::getInstance();
		$template->addCSS($template->getTplFilePath('controllers/showcase/css/simple-iconpicker.min.css', false));
		$template->addJS($template->getTplFilePath('controllers/showcase/js/simple-iconpicker.min.js', false));
		$tpl_file = $template->getTemplateFileName('controllers/showcase/fields/my_cat_icon');
        ob_start();
        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);
        return ob_get_clean();
    }

}