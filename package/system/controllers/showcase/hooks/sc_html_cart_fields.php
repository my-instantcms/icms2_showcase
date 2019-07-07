<?php

class onShowcaseScHtmlCartFields extends cmsAction {

    public function run($data){
			
		list($ctype_name, $item, $fields) = $data;
		
		//Создаем html разметку
		$html = '';
		foreach ($fields as $name => $field){
			if($field['type'] == 'scprice'){ continue; }
			if (isset($item['variant'][$name])){
				$html .= '<span data-sc-tip="' . $field['title'] . '">';
				$html .= $field['handler']->getStringValue($item['variant'][$name], false);
				$html .= '</span>';
			}
		}

		return $html;

    }

}
