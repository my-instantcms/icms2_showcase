<?php

class onShowcaseCtypeFieldForm extends cmsAction {

    public function run($form){
		$params = $form->getParams();
		if ($params && !empty($params[1]) && $params[1] == $this->ctype_name){
			if ($params[0] == 'edit'){
				$uri = $this->cms_core->uri_params;
				if (!empty($uri[2])){
					$item = $this->model->
						selectOnly('i.id, i.name')->
						getItemById('con_' . $this->ctype_name . '_fields', $uri[2]);
					if ($item){
						if ($item['name'] == 'title' || $item['name'] == 'photo' || $item['name'] == 'content' || $item['name'] == 'price' || $item['name'] == 'sale' || $item['name'] == 'variants'){ return $form; }
					}
				}
			}
			$form->addField('visibility', new fieldScPosition('options:sc_position', array(
				'title' => 'Позиция поле на странице просмотр товара',
				'hint' => 'Укажите где вывести это поле'
			)));
		}
        return $form;
    }

}

class fieldScPosition extends cmsFormField {
	
	public function getInput($value){
		$template = cmsTemplate::getInstance();
		$tpl_file = $template->getTemplateFileName('controllers/showcase/fields/sc_field_position');
        ob_start();
        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);
        return ob_get_clean();
    }

}