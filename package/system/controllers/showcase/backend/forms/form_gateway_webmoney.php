<?php

class formShowcaseGatewayWebmoney extends cmsForm{

    public function init($ctype_name = false){

        return array(
            1 => array(
                'type' => 'fieldset',
                'childs' => array(

					new fieldString('title', array(
                        'title' => LANG_TITLE,
                        'rules' => array(
                            array('required'),
							array('max_length', 50)
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
                        'hint' => 'Номер кошелька Webmoney, куда будут приходить деньги',
                        'rules' => array(
                            array('required'),
							array('max_length', 50)
                        )
                    )),
					
					new fieldString('pay_type', array(
                        'title' => 'Тип кошелька',
                        'hint' => 'WMR, WMZ или другой тип. Укажите уникальное имя, если кошелков несколько'
                    )),
					
					new fieldCheckbox('conversion:status', array(
                        'title' => 'Конвертировать цену',
                        'hint' => 'Система конвертирует цену, например: 2 WMR на 2 WMZ (или другой валюты). <b style="color:red">Если валюта сайта и кошелка совпадает, пропустите эту опцию</b>'
                    )),
					
					new fieldString('conversion:url', array(
                        'title' => 'Сайт откуда надо парсить курс',
						'default' => 'https://wm.exchanger.ru/asp/wmlist.asp?exchtype=1',
						'visible_depend' => array('conversion:status' => array('show' => array('1')))
                    )),
					
					new fieldString('conversion:selector', array(
                        'title' => 'Селектор блока с курсом',
                        'default' => '.indexTopControls .info b.main-info',
						'visible_depend' => array('conversion:status' => array('show' => array('1')))
                    )),
					
					new fieldScWebmoneyCourse('conversion:course', array(
                        'title' => 'Текущий курс',
						'hint' => 'Обязательно проверьте правильно ли спарсен курс',
						'visible_depend' => array('conversion:status' => array('show' => array('1')))
                    )),
					
					new fieldString('conversion:formula', array(
                        'title' => 'Формула',
                        'hint' => '<b>{price} / {course}</b><br />{price} - это цена товара<br />{course} - это текущий курс<br /><p>Более подробно о формуле можете узнать <a href="https://wm.exchanger.ru/asp/wmtransnew.asp" target="_blank">здесь</a>, описание можно найти по <a href="/templates/default/controllers/showcase/img/webmoney_course.png" class="ajax-modal">картинке</a></p>',
						'visible_depend' => array('conversion:status' => array('show' => array('1')))
                    )),
					
					new fieldString('secret_key', array(
                        'title' => 'Secret Key',
                        'hint' => 'Значение Secret Key, который указали на сайте Webmoney'
                    )),
					
					new fieldCheckbox('is_test', array(
                        'title' => 'Тестовый режимы (<span style="color:red">не забудьте потом убрать</span>)',
                        'hint' => 'Если на сайте webmoney, в настройках кошелка, поле "Тестовый/Рабочий режимы" ровно "тестовый"',
                        'default' => false
                    )),

                )
            )
        );
    }
}

class fieldScWebmoneyCourse extends cmsFormField {

	public function getInput($value){

		$tpl_file = cmsTemplate::getInstance()->getTemplateFileName('controllers/showcase/fields/conversion');

        ob_start();

        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);

        return ob_get_clean();

    }

}