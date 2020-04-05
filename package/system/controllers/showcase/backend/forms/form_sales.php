<?php

class formShowcaseSales extends cmsForm{

    public function init($do){
		
		$showcase = cmsCore::getController('showcase');

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(

					new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required')
                        )
                    )),

					new fieldNumber('start', array(
                        'title' => 'Сумма заказа начинается от',
                        'units' => !empty($showcase->options['currency']) ? $showcase->options['currency'] : 'руб',
                        'prefix' => LANG_FROM . ' ',
						'rules' => array(
                            array('required'),
							$do=='add' ? array('unique', 'sc_sales', 'start') : false
                        )
                    )),
					
					new fieldList('type', array(
						'title' => 'Тип скидки',
                        'default' => 'prosent',
                        'items' => array(
							'prosent' => 'Процент',
							'pickup' => 'Сумма',
						)
                    )),

					new fieldNumber('sale', array(
                        'title' => 'Сумма скидки',
                    )),

                )
            )
        );
    }
}