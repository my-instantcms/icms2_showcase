<?php

function grid_cart_delivery($controller) {
	
	$page_name = 'cart_delivery';
	
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
            'href' => href_to($controller->root_url, $page_name . '_form', array('{id}')),
        ),
        'type' => array(
            'title' => 'Способ доставки',
			'handler' => function($type){
                return ($type == 'courier') ? 'Курьерская доставка' : 'Самовывоз';
            }
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
            'confirm' => LANG_DELETE . '?'
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );
}