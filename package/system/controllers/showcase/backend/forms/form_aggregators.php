<?php

class formShowcaseAggregators extends cmsForm{

    public function init(){
		
		$showcase = cmsCore::getController('showcase');
		$result = $showcase->model->db->query("SHOW COLUMNS FROM `{#}con_{$showcase->ctype_name}`");
		$hint = 'Доступны поля: ';
		$skip = array('seo_keys', 'seo_desc', 'seo_title', 'tags', 'template', 'date_pub', 'is_pub', 'user_id', 'parent_id', 'parent_type', 'parent_title', 'parent_url', 'is_parent_hidden', 'category_id', 'folder_id', 'is_comments_on', 'comments', 'is_deleted', 'is_approved', 'approved_by', 'date_approved', 'is_private', 'variants', 'in_stock', 'fav', 'revs', 'recommends');
		while($col = $showcase->model->db->fetchAssoc($result)){
			if (in_array($col['Field'], $skip)){ continue; }
            $hint .= '{' . $col['Field'] . '}, ';
        }
		$cats = $showcase->model->get('con_' . $showcase->ctype_name . '_cats');
		if ($cats[1]){ 
			$cats[1]['id'] = 'all';
			$cats[1]['title'] = 'Все категории';
		}
        return array(
            array(
                'type' => 'fieldset',
                'childs' => array(
				
					new fieldString('file', array(
                        'title' => 'Название xml файла',
                        'hint' => 'Должен присутствовать файл-шаблон для генерации xml, например: \templates\default\controllers\showcase\<b>[имя_файла]</b>.tpl.php',
                        'rules' => array(
                            array('required'),
                            array('max_length', 20)
                        )
                    )),

					new fieldString('name', array(
                        'title' => 'Название магазина',
                        'rules' => array(
                            array('required'),
                            array('max_length', 20)
                        )
                    )),
					
					new fieldString('company', array(
                        'title' => 'Название компании',
                        'rules' => array(
                            array('required'),
                            array('max_length', 50)
                        )
                    )),
					
					new fieldString('email', array(
                        'title' => 'Почта магазина',
                        'rules' => array(
                            array('email')
                        )
                    )),
					
					new fieldString('url', array(
                        'title' => 'URL сайта',
                        'default' => cmsConfig::get('host'),
                        'rules' => array(
                            array('required')
                        )
                    )),
					
					// new fieldList('categories', array(
						// 'title' => 'Категории',
						// 'hint' => 'Если надо генерировать товары из всей категории, выберите "Все категории", а не по одному.',
						// 'is_chosen_multiple' => true,
						// 'items' => $cats ? array_collection_to_list($cats, 'id', 'title') : array()
					// )),
					
					new fieldRelateds('relateds', array(
                        'title' => 'Связать необходимые поля'
                    )),

					new fieldList('fields', array(
                        'title' => 'Дополнительные поля',
						'is_chosen_multiple' => true,
						'generator' => function($item) use ($showcase) {

                            $fields = $showcase->model->orderBy('i.ordering', 'asc')->get('con_' . $showcase->ctype_name . '_fields');
                            $props = $showcase->model->get('con_' . $showcase->ctype_name . '_props');

                            $items = array();

                            if ($fields) {
                                foreach ($fields as $field) {
                                    $items[$field['name']] = $field['title'];
                                }
                            }

                            if ($props) {
                                foreach ($props as $prop) {
                                    $items[$prop['id']] = 'Свойства: ' . $prop['title'];
                                }
                            }

                            return $items;

                        }
                    )),
					
					new fieldString('currency', array(
                        'title' => 'Основная валюта сайта',
                        'hint' => 'Например: RUR, USD, EUR, UAH, KZT',
                        'rules' => array(
                            array('required')
                        )
                    )),

					new fieldCurrencies('currencies', array(
                        'title' => 'Список курсов валют',
                        'rules' => array(
                            array('required')
                        )
                    )),

					new fieldCheckbox('adult', array(
                        'title' => 'Товары 18+'
                    )),

					new fieldCheckbox('delivery', array(
                        'title' => 'Есть доставка?'
                    )),

					new fieldNumber('cost', array(
                        'title' => 'Цена доставки',
                        'hint' => 'Указывайте максимальную цену доставки',
                        'units' => !empty($showcase->options['currency']) ? $showcase->options['currency'] : '$',
						'visible_depend' => array('delivery' => array('show' => array('1')))
                    )),

					new fieldString('days', array(
                        'title' => 'Срок доставки',
                        'suffix' => 'дней',
						'visible_depend' => array('delivery' => array('show' => array('1')))
                    )),
					
					new fieldCheckbox('pickup', array(
                        'title' => 'Есть самовывоз?'
                    )),

					new fieldNumber('pickup_cost', array(
                        'title' => 'Цена самовывоза',
                        'units' => !empty($showcase->options['currency']) ? $showcase->options['currency'] : '$',
						'visible_depend' => array('pickup' => array('show' => array('1')))
                    )),

					new fieldString('pickup_days', array(
                        'title' => 'Срок самовывоза',
                        'hint' => '0 = сегодня. 1 = через 1 день. 1-3 = от 1 до 3 дней',
                        'suffix' => 'дней',
						'visible_depend' => array('pickup' => array('show' => array('1')))
                    )),
					
					new fieldCheckbox('store', array(
                        'title' => 'Покупка без предварительного заказа (на месте)'
                    )),

                )
            )
        );
    }
}

class fieldCurrencies extends cmsFormField {

	public function getInput($value){

		$tpl_file = cmsTemplate::getInstance()->getTemplateFileName('controllers/showcase/fields/sc_currencies');

        ob_start();

        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);

        return ob_get_clean();

    }

}

class fieldRelateds extends cmsFormField {
	
	public $var_type    = 'array';

	public function getInput($value){

		$tpl_file = cmsTemplate::getInstance()->getTemplateFileName('controllers/showcase/fields/sc_relateds');
		
		$showcase = cmsCore::getController('showcase');
		$model = cmsCore::getModel('content');
		$fields = $model->orderBy('i.ordering')->getContentFields($showcase->ctype_name);
		$props = $model->getContentProps($showcase->ctype_name);
		
		$relfields = array(0 => 'Выбрать поле', 'cat_name' => 'Категория');
		if ($fields){
			foreach ($fields as $field){
				$relfields[$field['name']] = $field['title'];
			}
		}
		if ($props){
			foreach ($props as $prop){
				$relfields[$prop['id']] = 'Свойства: ' . $prop['title'];
			}
		}

        ob_start();

        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);

        return ob_get_clean();

    }

}