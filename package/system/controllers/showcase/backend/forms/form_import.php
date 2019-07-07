<?php

class formShowcaseImport extends cmsForm{

    public function init($ctype_name = false){

        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(

					new fieldFile('file', array(
                        'title' => 'Файл',
                        'options' => array(
                            'extensions' => 'csv'
                        ),
                        'rules' => array(
                            array('required')
                        )
                    )),
					
					
					new fieldNumber('limit', array(
                        'title' => 'Ограничить количество импортируемых товаров',
                        'hint' => 'Что бы не грузить сайт, можно по 100 товаров добавить',
						'units' => 'штук',
						'default' => 100
                    )),
					
					new fieldString('sep', array(
                        'title' => 'Разделитель столбцов',
						'default' => ';'
                    )),
					
					new fieldCheckbox('update', array(
						'title' => 'Обновить, если товар с таким ID или артикулом существует',
                        'default' => 0
                    )),
					
					new fieldCheckbox('cat_create', array(
						'title' => 'Создать категорию, если категория из импорта отсутствует на сайте',
                        'default' => 1
                    )),
					
					new fieldList('cat_move', array(
						'title' => 'Переместить в категорию, если категория из импорта отсутствует на сайте',
                        'generator' => function($item) use ($ctype_name) {

                            $model = cmsCore::getModel('content');
                            $cats = $model->selectOnly('i.id, i.title, i.ns_level')->getCategoriesTree($ctype_name);

                            $items = array();

                            if ($cats){
								foreach($cats as $cat){
									if ($cat['ns_level'] > 1){
										$cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
									}
									$items[$cat['id']] = $cat['title'];

								}
							}

                            return $items;

                        },
						'visible_depend' => array('cat_create' => array('hide' => array('1')))
                    )),
					
					new fieldString('cat_sep', array(
                        'title' => 'Разделитель подкатегории',
						'default' => '///'
                    )),
					
					new fieldString('img_sep', array(
                        'title' => 'Разделитель фотографии',
                        'hint' => 'Если фотографии несколько',
						'default' => ','
                    )),
					
					new fieldString('props_sep', array(
                        'title' => 'Разделитель свойств',
						'default' => '///'
                    ))

                )
            )
        );
    }
}