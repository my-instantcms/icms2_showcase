<?php

function grid_pay_systems($controller) {
	
	$page_name = 'pay_systems';
	
    $options = array(
        'is_sortable' => false,
        'is_filter' => false,
        'is_pagination' => false,
        'is_draggable' => true,
		'is_selectable' => false, 
        'order_by' => 'ordering',
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
            'href' => href_to($controller->root_url, '{file_action}', array('{id}')),
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
            'href' => href_to($controller->root_url, '{file_action}', array('{id}')),
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