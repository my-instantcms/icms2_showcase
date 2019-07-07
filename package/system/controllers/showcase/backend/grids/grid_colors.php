<?php

function grid_colors($controller) {
	
	$page_name = 'colors';
	
    $options = array(
        'is_sortable' => false,
        'is_filter' => false,
        'is_pagination' => false,
        'is_draggable' => false,
		'is_selectable' => false, 
        'order_by' => 'id',
        'order_to' => 'desc',
        'show_id' => false
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
		'color' => array(
            'title' => LANG_PARSER_COLOR,
			'handler' => function($color){
                return '<div style="background-color:'.$color.';width: 20px;height: 20px;" title="'.$color.'"></div>';
            }
        )
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
            'href' => href_to($controller->root_url, 'delete', array('sc_'. $page_name, '{id}')),
            'confirm' => LANG_DELETE . '?'
        )
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => $actions
    );
}