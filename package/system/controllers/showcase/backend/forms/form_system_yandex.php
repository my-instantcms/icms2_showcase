<?php

class formShowcaseSystemYandex extends cmsForm{

    public function init($ctype_name = false){

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(

					new fieldHidden('system', array(
                        'default' => 'yandex'
                    )),

					new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
							array('max_length', 50)
                        )
                    )),
					
					new fieldString('hint', array(
                        'title' => 'Краткое описание',
                        'rules' => array(
							array('max_length', 100)
                        )
                    )),
					
					new fieldImage('icon', array(
                        'title' => 'Иконка (Загрузиться оригинал)',
                        'hint' => 'Загрузиться оригинал картинки, поэтому подгоните размер иконки под дизайн вашего шаблона',
                        'options' => array(
                            'sizes' => array('original'),
							'allow_import_link' => true
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),

					new fieldString('wallet_id', array(
                        'title' => 'Номер кошелька',
                        'hint' => 'Номер кошелька в Яндекс.Деньгах, на который нужно зачислять деньги отправителей.',
                        'rules' => array(
                            array('required'),
							array('max_length', 50)
                        )
                    )),
					
					new fieldList('pay_type', array(
                        'title' => 'Способ оплаты',
                        'default' => 'all',
                        'items' => array(
							'all' => 'Предоставить выбор',
							'PC' => 'из кошелька в Яндекс Деньги',
							'AC' => 'с банковской карты',
							'MC' => 'с баланса мобильного',
						)
                    )),
					
					new fieldString('secret_key', array(
                        'title' => 'Секретное слово HTTP уведомления',
                        'hint' => 'Необязательно если не используете HTTP уведомления'
                    )),

                )
            )
        );
    }
}