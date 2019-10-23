<?php

function grid_aggregators($controller) {
	
    $options = array(
        'is_sortable' => false,
        'is_filter' => false,
        'is_pagination' => false,
        'is_draggable' => true,
		'is_selectable' => false, 
        'order_by' => 'id',
        'order_to' => 'desc',
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'id',
            'width' => 30,
        ),
        'name' => array(
            'title' => LANG_TITLE,
			'handler' => function($title, $row) use ($controller){
				$url = href_to($controller->root_url, 'aggregators_form', array($row['id']));
                return '<a href="' . $url . '">' . '[' . $row['file'] . ']' . ' ' . $title . '</a>';
            }
        ),
        'url' => array(
            'title' => 'Файл',
			'handler' => function($url, $row){
				$url = cmsConfig::get('upload_host_abs') . '/export/' . $row['file'] . '_' . $row['id'] . '.xml';
                return '<a href="' . $url . '" target="_blank">' . $url . '</a>';
            }
        ),
		'is_pub' => array(
            'title' => LANG_ON,
            'width' => 40,
            'flag' => true,
			'flag_toggle' => href_to($controller->root_url, 'item_toggle', array('sc_aggregators', '{id}'))
        ),
    );

    return array(
        'options' => $options,
        'columns' => $columns,
        'actions' => array(
			array(
				'title' => LANG_EDIT,
				'class' => 'edit',
				'href' => href_to($controller->root_url, 'aggregators_form', array('{id}')),
			),
			array(
				'title' => LANG_DELETE,
				'class' => 'delete',
				'href' => href_to($controller->root_url, 'delete', array('sc_aggregators', '{id}')),
				'confirm' => LANG_DELETE . '?'
			),
		)
    );
}