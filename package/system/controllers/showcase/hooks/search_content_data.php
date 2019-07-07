<?php

class onShowcaseSearchContentData extends cmsAction {

    public function run($data){

		if (!empty($data['sources'][$this->ctype_name])){
			$select_fields = array('price', 'sale', 'photo', 'variants', 'comments', 'tags');
			$match_fields = array('tags');
			$data['select_fields'][$this->ctype_name] = array_merge($data['select_fields'][$this->ctype_name], $select_fields);
			$data['match_fields'][$this->ctype_name] = array_merge($data['match_fields'][$this->ctype_name], $match_fields);
		}

        return $data;

    }

}
