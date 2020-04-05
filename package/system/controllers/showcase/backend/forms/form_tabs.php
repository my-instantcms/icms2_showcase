<?php

class formShowcaseTabs extends cmsForm{

    public function init(){
		
		$showcase = cmsCore::getController('showcase');
		$result = $showcase->model->db->query("SHOW COLUMNS FROM `{#}con_{$showcase->ctype_name}`");
		$hint = 'Доступны поля: ';
		$skip = array('seo_keys', 'seo_desc', 'seo_title', 'tags', 'template', 'date_pub', 'is_pub', 'user_id', 'parent_id', 'parent_type', 'parent_title', 'parent_url', 'is_parent_hidden', 'category_id', 'folder_id', 'is_comments_on', 'comments', 'is_deleted', 'is_approved', 'approved_by', 'date_approved', 'is_private', 'variants', 'in_stock', 'fav', 'revs', 'recommends');
		while($col = $showcase->model->db->fetchAssoc($result)){
			if (in_array($col['Field'], $skip)){ continue; }
            $hint .= '{' . $col['Field'] . '}, ';
        }
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
					
					new fieldString('icon', array(
                        'title' => 'Иконка FontAwesom'
                    )),

					new fieldList('type', array(
						'title' => 'Тип вывода',
						'items' => array(
							'fields' => 'Выводить поля',
							'text' => 'Произвольный текст'
						)
					)),

					new fieldList('fields', array(
                        'title' => 'Поля',
						'is_chosen_multiple' => true,
						'generator' => function($item) use ($showcase) {

                            $tree = $showcase->model->
								selectOnly('i.id, i.name, i.title, i.type')->
								orderBy('i.ordering', 'asc')->
								get('con_' . $showcase->ctype_name . '_fields');
							$skips = array('caption', 'scvariations', 'scprice', 'number', 'bookmarks', 'navigation', 'recommendstars');

                            $items = array();

                            if ($tree) {
                                foreach ($tree as $item) {
									if (in_array($item['type'], $skips)){ continue; }
                                    $items[$item['name']] = $item['title'];
                                }
                            }

                            return $items + array('sc_tag_list' => 'Список тегов', 'sc_prop_list' => 'Список свойств');

                        },
						'visible_depend' => array('type' => array('show' => array('fields')))
                    )),
					
					new fieldHtml('text', array(
                        'title' => 'Произвольный текст',
                        'hint' => $hint,
						'visible_depend' => array('type' => array('show' => array('text')))
                    )),

                )
            )
        );
    }
}