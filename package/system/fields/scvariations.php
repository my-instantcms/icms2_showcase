<?php

class fieldScvariations extends cmsFormField {

    public $title       = '[showcase] Варианты товара';
    public $sql         = 'text NULL DEFAULT NULL';
    public $var_type    = 'array';

    public function parse($value){
		return false;
    }

    public function store($value, $is_submitted, $old_value=null){

		if (!is_array($value)){ return false; }

        return $value;

    }

    public function getInput($value){
		
		$showcase = cmsCore::getController('showcase');
		if (!empty($showcase->options['variants_off'])){ return false; }
		
		$fields = false;
		$variations = false;
		if ($value){
			$value = is_array($value) ? $value : cmsModel::yamlToArray($value);
			if ($value && !empty($this->item['ctype_name']) && !empty($this->item['id'])){
				$model = cmsCore::getModel('content');
				$fields = $model->
					orderBy('i.ordering')->
					filterLike('i.type', 'sc%')->
					getContentFields($this->item['ctype_name']);

				$variations = $model->
					useCache("showcase.sc_variations")->
					filterEqual('i.ctype_name', $this->item['ctype_name'])->
					filterEqual('i.item_id', $this->item['id'])->
					filterIn('i.id', $value)->
					orderBy('i.ordering', 'ASC')->
					get('sc_variations');
			}
		}

		$tpl = cmsTemplate::getInstance();
		$field = $this;

        ob_start();
		include($tpl->getTemplateFileName('controllers/showcase/fields/list_variations'));
		return ob_get_clean();

    }

}