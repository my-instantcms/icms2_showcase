<?php

class formShowcasePaySystems extends cmsForm{

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
                        'title' => 'Краткое описание',
                        'rules' => array(
							array('max_length', 100)
                        )
                    )),

					new fieldImage('icon', array(
                        'title' => 'Иконка (Загрузиться оригинал)',
                        'title' => 'Загрузиться оригинал, поэтому подгоните размер иконки под дизайн вашего шаблона',
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

					new fieldString('redirect_success', array(
                        'title' => 'URL-адрес для редиректа после успешной оплаты'
                    )),

                )
            )
        );
    }
}