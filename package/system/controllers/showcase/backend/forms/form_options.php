<?php

class formShowcaseOptions extends cmsForm {

    public function init() {
		
		$billing_text = cmsCore::isControllerExists('billing') ? '' : ' (не найден)';
		$model = cmsCore::getModel('showcase');
		$payment_systems = $model->filterEqual('i.is_pub', 1)->getDataCount('sc_pay_systems');

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('ctype_name', array(
						'title' => 'Выберите тип контента в качестве Магазина',
						'hint' => 'Нажмите кнопку <b>"Генерировать поля для типа контента"</b>, после того, как выбрали/сменили тип контента',
						'generator' => function($item) {

                            $tree = cmsCore::getModel('content')->getContentTypes();
                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;

                        }
					)),
					
					new fieldString('currency', array(
						'title' => 'Валюта',
						'default' => LANG_CURRENCY
					)),

					new fieldString('currency_iso', array(
						'title' => 'Валюта в ISO формате',
						'hint' => 'Например RUB, USD, UAH, EUR. Список всех валют <a href="https://en.wikipedia.org/wiki/ISO_4217#Active_codes" target="_blank">здесь</a>',
						'default' => 'RUB'
					)),
					
					new fieldList('price_format', array(
						'title' => 'Формат вывода цен',
						'default' => 1,
						'items' => array(
							1 => 'Красивый округленный вид: 1 500 руб.',
							2 => 'Реальный вид с точкой: 1499.99 руб'
						)
					)),
					
					new fieldList('payment', array(
						'title' => 'Способ оплаты',
						'hint' => 'Настроить <a href="' . href_to('admin', 'controllers', array('edit', 'showcase', 'pay_systems')) . '">систему оплаты</a>. Купить компонент <a href="https://addons.instantcms.ru/addons/billing2.html" target="_blank">Биллинг</a>.',
						'default' => ($payment_systems ? 'system' : 'off'),
						'items' => array(
							'off' => 'Не использовать',
							'system' => 'Стандартный (настроенных систем оплаты: ' . $payment_systems . ')',
							'billing' => 'Платный компонент Биллинг' . $billing_text
						)
					)),
					
					new fieldCheckbox('menu', array(
						'title' => 'Вывести магазин в меню админки',
						'hint' => 'В меню админки появиться новый пункт меню, для быстрого перехода в настроки магазина'
					)),

					new fieldCheckbox('fa', array(
						'title' => 'Не подключать иконки FontAwesome из компонента',
						'hint' => 'Поставьте галочку, если ваш шаблон уже поддерживает иконки FontAwesome'
					)),

                )
            ),
			
			array(
                'type' => 'fieldset',
                'title' => 'Персонал',
                'childs' => array(

                    new fieldList('managers', array(
                        'title' => 'Менеджеры заказов',
                        'hint' => 'Те кто обрабатывает заказы',
                        'is_chosen_multiple' => 1,
						'generator' => function($item) {
							
							$model = new cmsModel();
                            $tree = $model->selectOnly('i.id, i.nickname')->get('{users}');
                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['id']] = $item['nickname'];
                                }
                            }

                            return $items;

                        }
                    )),

					new fieldCheckbox('log', array(
						'title' => 'Логировать действия персонала',
						'hint' => 'В лог действий записывается действия персонала',
						'default' => 1
					)),
					
					new fieldString('email', array(
						'title' => 'Отправить письмо о новых заказах или оплатах',
						'hint' => 'Укажите email через запятую, если несколько',
					)),

                )
            ),
			
			array(
                'type' => 'fieldset',
                'title' => 'Работа с вариантами',
                'childs' => array(

                    new fieldCheckbox('variants_off', array(
                        'title' => 'Отключить варианты',
                        'hint' => 'Если не используете варианты товаров, лучше выключить их, что бы не было лишних запросов',
                        'default' => 0
                    )),

					new fieldCheckbox('variants_opened', array(
						'title' => 'Автоматический раскрыть выбор вариантов на странице товара',
						'hint' => 'Выбор варианта будет в списке или уже в раскрытом виде',
						'default' => 0
					)),
					
					new fieldList('variants_list', array(
						'title' => 'Стиль отображение выбор вариантов в списке',
						'default' => 'box',
						'items' => array(
							'box' => 'Квадратики',
							'select' => 'Список',
						)
					)),

                )
            ),
			
			array(
                'type' => 'fieldset',
                'title' => 'Дизайн страниц списка',
                'childs' => array(

                    new fieldList('list_pos', array(
                        'title' => 'Стиль отображения фото товара в списке',
                        'hint' => 'Стиль отображения картинки, на страницах списка товаров',
                        'default' => 'center',
						'items' => array(
							'center' => 'Выравнивание по центру',
							'top' => 'Выравнивание сверху',
							'bottom' => 'Выравнивание снизу',
							'contain' => 'Выравнивание по размеру блока',
						)
                    )),

					new fieldNumber('list_height', array(
						'title' => 'Высота блока фотографии',
						'default' => 200,
						'units' => 'px'
					)),
					
					new fieldColor('list_bg', array(
						'title' => 'Фон блока фотографии',
						'default' => '#ffffff'
					)),

                )
            ),
			
			array(
                'type' => 'fieldset',
                'title' => 'Дизайн страниц товара',
                'childs' => array(
				
					new fieldList('view_pos', array(
                        'title' => 'Стиль отображения фото товара',
                        'hint' => 'Стиль отображения картинки, на страницах просмотр товаров',
                        'default' => 'center',
						'items' => array(
							'center' => 'Выравнивание по центру',
							'top' => 'Выравнивание сверху',
							'bottom' => 'Выравнивание снизу',
							'contain' => 'Выравнивание по размеру блока',
						)
                    )),
					
					new fieldList('cover_size', array(
                        'title' => 'Размер фото в полноэкранном виде',
						'hint' => 'При щелчке на иконку, какой пресет фото открывать?',
                        'default' => 'original',
                        'generator' => function (){
                            $presets = cmsCore::getModel('images')->getPresetsList();
                            $presets['original'] = LANG_PARSER_IMAGE_SIZE_ORIGINAL;
                            return $presets;
                        }
                    )),
					
					new fieldColor('view_bg', array(
						'title' => 'Фон блока фотографии',
						'default' => '#ffffff'
					)),

                    new fieldCheckbox('hide_artikul', array(
                        'title' => 'Не показать артикул'
                    )),
					
					new fieldList('show_instock', array(
                        'title' => 'Тип отображение "В наличии"',
                        'default' => 'counter',
						'items' => array(
							'none' => 'Не показать',
							'counter' => 'Текст + количество',
							'text' => 'Просто текст',
						)
                    )),
					
					new fieldCheckbox('off_inctock', array(
                        'title' => 'Не уменьшать количество "В наличии" при продаже',
                        'hint' => 'При включении этой опции, пользователь сможет заказать товары, больше того, что указано в Наличии',
                    )),

                )
            )

        );

    }

}
