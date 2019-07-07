<?php

class formWidgetShowcaseCartOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(
					
					new fieldList('options:style', array(
                        'title' => 'Стиль виджета',
                        'default' => 'normal',
						'generator' => function($item) {

							$tpls = cmsTemplate::getInstance()->getAvailableTemplatesFiles('controllers/showcase/tpl', 'cart_*.tpl.php', cmsConfig::get('template'));

                            $items = array();

                            if ($tpls) {
                                foreach ($tpls as $key => $tpl) {
									preg_match('/^cart_([a-z0-9_\-]*)/i', $tpl, $matches);
									if (!empty($matches[0]) && !empty($matches[1])){
										$items[$matches[1]] = $matches[0];
									}
                                }
                            }

                            return $items;

                        }
                    )),
					
					new fieldCheckbox('options:hide', array(
                        'title' => 'Не отображать виджет на странице Корзины',
                        'hint' => 'Что бы не дублировать корзину'
                    )),
					
					new fieldString('options:fa', array(
                        'title' => 'Иконка корзины от Font Awesome',
                        'hint' => 'Посмотреть весь список можно <a href="https://fontawesome.com/v4.7.0/icons/" target="_blank">тут</a>',
                        'default' => 'fa fa-shopping-cart'
                    )),
					
					new fieldColor('options:color', array(
                        'title' => 'Цвет иконки корзины',
                        'default' => '#fff'
                    )),

                )
            ),

        );

    }

}
