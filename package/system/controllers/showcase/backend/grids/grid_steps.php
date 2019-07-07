<?php

function grid_steps($controller) {
	
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
			'handler' => function($title, $row) use ($controller){
				return !empty($row['href']) ? '<a href="'. href_to($controller->root_url, $row['href']) .'">'. $title .'</a>' : $title;
			}
        ),
		'ordering' => array(
            'title' => 'Порядок'
        ),
		'is_pub' => array(
            'title' => LANG_ON,
            'width' => 40,
            'flag' => true,
			'flag_toggle' => href_to($controller->root_url, 'item_toggle', array('sc_steps', '{id}'))
        ),
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => array(
			array(
				'title' => LANG_DELETE,
				'class' => 'delete',
				'href' => href_to($controller->root_url, 'delete', array('sc_steps', '{id}')),
				'confirm' => LANG_DELETE . '?',
				'handler' => function($row){
					return ($row['id'] == 1) ? true : false;
				}
			))
    );
}