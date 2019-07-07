<?php

class formShowcaseCartFields extends cmsForm{

    public function init($ctype_name = false){

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(
				
					new fieldString('name', array(
                        'title' => LANG_SYSTEM_NAME,
                        'rules' => array(
                            array('required'),
							array('max_length', 40)
                        )
                    )),

					new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
							array('max_length', 50)
                        )
                    )),

					new fieldString('hint', array(
                        'title' => LANG_CP_FIELD_HINT,
                        'rules' => array(
                            array('max_length', 150)
                        )
                    )),
					
					new fieldList('type', array(
						'title' => LANG_CP_FIELD_TYPE,
                        'default' => 'string',
                        'generator' => function() {
                            return cmsCore::getController('showcase')->getCartFieldsType();
                        }
                    )),
					
					new fieldKeyValue('attributes', array(
                        'title' => 'Атрибуты поля',
                        'title_add' => 'Добавить атрибут'
                    )),
					
					new fieldKeyValue('options', array(
                        'title' => LANG_OPTIONS,
						'title_add' => 'Добавить опцию'
                    )),

                )
            )
        );
    }
}

class fieldKeyValue extends cmsFormField {
	
	public function getInput($value){

		$tpl_file = cmsTemplate::getInstance()->getTemplateFileName('controllers/showcase/fields/key_value');

        ob_start();

        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);

        return ob_get_clean();

    }

}