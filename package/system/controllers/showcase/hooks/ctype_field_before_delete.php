<?php

class onShowcaseCtypeFieldBeforeDelete extends cmsAction {

    public function run($data){
		
		list($field, $ctype_name, $model) = $data;

		$is_sc = (stripos($field['type'], 'sc') !== false) ? true : false;

		if ($ctype_name == $this->ctype_name && $is_sc && $field['type'] != 'scprice' && $field['type'] != 'scvariations'){
			
			if ($this->model->db->isFieldExists('sc_variations', $field['name'])){
				$sql = "ALTER TABLE `{#}sc_variations` DROP `{$field['name']}`";
				$this->model->db->query($sql);
			}
			
		}

        return $data;

    }

}