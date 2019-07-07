<?php

class onShowcaseCtypeFieldAfterAdd extends cmsAction {

    public function run($data){
		
		list($field, $ctype_name, $model) = $data;

		$is_sc = (stripos($field['type'], 'sc') !== false) ? true : false;

		if ($ctype_name == $this->ctype_name && $is_sc && $field['type'] != 'scprice' && $field['type'] != 'scvariations'){
			
			if (!$this->model->db->isFieldExists('sc_variations', $field['name'])){
				$f_class  = 'field'.string_to_camel('_', $field['type']);
				$f_parser = new $f_class(null, (isset($field['options']) ? array('options' => $field['options']) : null));
				$sql = "ALTER TABLE {#}sc_variations ADD `{$field['name']}` {$f_parser->getSQL()}";
				$this->model->db->query($sql);
			}
			
		}

        return $data;

    }

}