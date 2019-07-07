<?php

class formShowcaseCartDelivery extends cmsForm{

    public function init($ctype_name = false){

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(

					new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
							array('max_length', 50)
                        )
                    )),
					
					new fieldString('hint', array(
                        'title' => 'Описание',
                        'rules' => array(
							array('max_length', 250)
                        )
                    )),
					
					new fieldList('type', array(
						'title' => 'Способ доставки',
                        'default' => 'courier',
                        'items' => array(
							'courier' => 'Курьерская доставка',
							'pickup' => 'Самовывоз',
						)
                    )),
					
					new fieldString('pickup_address', array(
                        'title' => 'Адрес самовывоза',
                        'rules' => array(
							array('max_length', 160)
                        ),
						'visible_depend' => array('type' => array('show' => array('pickup')))
                    )),
					
					new fieldScSelectMap('pickup_map', array(
                        'title' => 'Адрес самовывоза на карте',
						'visible_depend' => array('type' => array('show' => array('pickup')))
                    )),
					
					new fieldNumber('price', array(
                        'title' => 'Цена',
                        'hint' => 'Если оставить пустой, будет отображатся надпись "Не указана", 0 = Бесплатно',
						'units' => !empty(cmsCore::getController('showcase')->options['cerrency']) ? cmsCore::getController('showcase')->options['cerrency'] : LANG_CURRENCY
                    )),

                )
            )
        );
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