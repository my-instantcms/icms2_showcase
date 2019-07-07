<?php

function grid_cart_transactions($controller) {
	
	$page_name = 'cart_transactions';
	
    $options = array(
        'is_sortable' => true,
        'is_filter' => true,
        'is_pagination' => true,
        'is_draggable' => false,
		'is_selectable' => false, 
        'order_by' => 'date_pub',
        'order_to' => 'desc',
        'show_id' => true
    );

    $columns = array(
        'id' => array(
            'title' => 'ID',
			'filter' => 'exact',
            'href' => href_to($controller->root_url, $page_name . '_view', array('{id}')),
			'handler' => function($id){
                return 'Транзакция №' . $id;
            }
        ),
		'order_id' => array(
            'title' => '№ заказа',
			'filter' => 'exact',
			'handler' => function($order_id){
                return '<a href="' . href_to('showcase', 'orders', $order_id) . '" target="_blank">Заказ №' . $order_id . '</a>';
            }
        ),
		'sys_name' => array(
            'title' => 'Платежная система',
			'handler' => function($sys_name){
                return $sys_name;
            }
        ),
		'price' => array(
            'title' => 'Сумма',
			'filter' => 'exact',
			'handler' => function($price){
                return $price ? cmsCore::getController('showcase')->getPriceFormat($price) : 0;
            }
        ),
		'errors' => array(
            'title' => 'Ошибки',
			'handler' => function($errors){
				$errors = $errors ? cmsModel::yamlToArray($errors) : false;
                return $errors ? '<b style="color:red">' . LANG_YES . '</b>' : LANG_NO;
            }
        ),
		'history' => array(
            'title' => 'История',
			'handler' => function($history){
				$history = $history ? cmsModel::yamlToArray($history) : array();
                return html_spellcount(count($history), 'запись|записи|записей');
            }
        ),
		'date_pub' => array(
            'title' => 'Дата',
			'handler' => function($date_pub){
                return lang_date(date('j F Y H:i', strtotime($date_pub)));;
            }
        ),
    );

    $actions = array(
        array(
            'title' => LANG_VIEW,
            'class' => 'play',
            'href' => href_to($controller->root_url, $page_name . '_view', array('{id}')),
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