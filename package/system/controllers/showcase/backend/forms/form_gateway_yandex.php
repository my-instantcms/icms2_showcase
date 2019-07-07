<?php

class formShowcaseGatewayYandex extends cmsForm{

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
                        'hint' => 'Номер кошелька в Яндекс.Деньгах, куда будут приходить деньги',
                        'rules' => array(
                            array('required'),
							array('max_length', 50)
                        )
                    )),
					
					new fieldString('currency', array(
                        'title' => 'Код валюты',
                        'default' => 643,
                        'hint' => '643 - это код рубля РФ согласно <a href="https://index.minfin.com.ua/reference/currency/code/" target="_blank">ISO 4217</a>',
                    )),
					
					new fieldHidden('pay_type', array(
                        'default' => 'PC'
                    )),
					
					new fieldString('secret_key', array(
                        'title' => 'Секретное слово HTTP уведомления',
                        'hint' => 'Инструкция настройки HTTP уведомления<p>Откройте сайт <a href="https://money.yandex.ru/myservices/online.xml" target="_blank">https://money.yandex.ru/myservices/online.xml</a><br />Введите пароль или код СМС, после чего откроется <a href="/templates/default/controllers/showcase/img/yandex_http.png" class="ajax-modal">такая страница</a>.<br />В поле Адрес укажите</p> <pre style="display:inline-block">' . cmsConfig::get('host') . '/showcase/success_yandex</pre><p>Потом нажмите кнопку <b>Показать секрет</b><br />Скопируйте секретный код и вставьте сюда в поле выше <label for="secret_key" style="display:inline-block">Секретное слово HTTP уведомления</label><br />Поставьте галочку на <b>Отправлять уведомления</b> и Сохраните</p>'
                    )),

                )
            )
        );
    }
}