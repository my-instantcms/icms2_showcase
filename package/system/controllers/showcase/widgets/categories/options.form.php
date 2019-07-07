<?php

class formWidgetShowcaseCategoriesOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_CONTENT_TYPE,
                'childs' => array(

                    new fieldList('options:ctype_name', array(
                        'generator' => function($item) {

                            $model = cmsCore::getModel('content');
                            $tree = $model->getContentTypes();

                            $items = array(0 => 'Определить автоматический');

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items;

                        }
                    )),
					
					new fieldList('options:tpl', array(
						'title' => 'Шаблон',
						'default' => 'slider',
						'items' => array(
							'' => 'По умолчанию',
							'slider' => 'Слайдер',
							'list' => 'Список'
						)

					)),
					
					new fieldCheckbox('options:goods_count', array(
						'title' => 'Вывести количество товаров рядом с категории',
						'default' => false,
						'visible_depend' => array('options:tpl' => array('show' => array('list')))
					)),
					
					new fieldList('options:icon', array(
						'title' => 'Поле иконка',
						'default' => 'sc_icon',
						'items' => array(
							'sc_icon' => 'Иконка',
							'sc_fa' => 'Иконочный шрифт'
						),
						'visible_depend' => array('options:tpl' => array('show' => array('slider')))
					)),
					
					new fieldNumber('options:slides', array(
                        'title' => 'Количество видимых слайдов',
                        'default' => 6,
						'visible_depend' => array('options:tpl' => array('show' => array('slider')))
                    )),

                    new fieldCheckbox('options:is_root', array(
                        'title' => 'Показывать корневую категорию',
                        'default' => false
                    )),
					
					new fieldCheckbox('options:only_parent', array(
                        'title' => 'Показывать только родительские категорий',
                        'default' => false
                    )),

                    new fieldCheckbox('options:show_full_tree', array(
                        'title' => 'Показывать всё дерево категорий',
                        'default' => false
                    ))

                )
            ),

        );

    }

}
