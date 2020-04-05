<?php

function grid_sales($controller) {
	
	$page_name = 'sales';
	
    $options = array(
        'is_sortable' => false,
        'is_filter' => false,
        'is_pagination' => false,
        'is_draggable' => false,
		'is_selectable' => false, 
        'order_by' => 'start',
        'order_to' => 'asc',
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
        ),
        'title' => array(
            'title' => LANG_TITLE,
            'href' => href_to($controller->root_url, $page_name . '_form', array('{id}')),
        ),
        'start' => array(
            'title' => 'Начиная от'
        ),
        'type' => array(
            'title' => 'Скидка',
			'handler' => function($type){
                return ($type == 'prosent') ? 'Процент' : 'Сумма';
            }
        ),
		'is_pub' => array(
            'title' => LANG_ON,
            'width' => 40,
            'flag' => true,
			'flag_toggle' => href_to($controller->root_url, 'item_toggle', array('sc_' . $page_name, '{id}'))
        ),
    );

    $actions = array(
        array(
            'title' => LANG_EDIT,
            'class' => 'edit',
            'href' => href_to($controller->root_url, $page_name . '_form', array('{id}')),
        ),
        array(
            'title' => LANG_DELETE,
            'class' => 'delete',
            'href' => href_to($controller->root_url, 'delete', array('sc_' . $page_name, '{id}')),
            'confirm' => LANG_CP_FIELD_DELETE_CONFIRM
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );
}