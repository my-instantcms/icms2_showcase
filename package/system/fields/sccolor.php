<?php

class fieldSccolor extends cmsFormField {

    public $title       = '[showcase] Цвет';
    public $sql         = 'varchar(100) NULL DEFAULT NULL';
    public $allow_index = true;
    public $filter_type = 'str';
    public $var_type    = 'array';

    public function getOptions(){
        return array(
            new fieldCheckbox('is_checkbox_multiple', array(
                'title'   => LANG_PARSER_BITMASK_CHECKBOX_MULTIPLE,
                'default' => false
            )),
            new fieldString('list_class', array(
                'title'   => LANG_PARSER_BITMASK_LIST_CLASS,
                'default' => 'multiple_tags_list'
            ))
        );
    }

    public function getFilterInput($value) {

        $this->data['items']    = $this->getListItems(false);
        $this->data['selected'] = array();

        if(is_array($value)){
            foreach ($value as $k => $v) {
                if(is_numeric($v)){ $this->data['selected'][$k] = intval($v); }
            }
        } else {
            $this->data['selected'] = array();
        }

        $this->title = false;

        $tpl_file = cmsTemplate::getInstance()->getTemplateFileName('controllers/showcase/fields/color_filter');

        ob_start();

        extract(array('field' => $this, 'value' => $value));
		include($tpl_file);

        return ob_get_clean();


    }

    public function getStringValue($value, $is_multiple = true){

        if (!$value) { return ''; }

        $items = $this->getListItems();

        $string = '';

		if ($items) {
			
			if (!$is_multiple && !empty($items[$value])){
				return $items[$value];
			}

			$pos = 0; $list = array();

			foreach($items as $key => $item){

                if(!is_array($value)){

                    if (substr($value, $pos, 1) == 1){
                        $list[] = $item;
                    }
                    $pos++;
                    if ($pos+1 > strlen($value)) { break; }

                } else {

                    if(in_array($key, $value)){
                        $list[] = $item;
                    }

                }

			}

            $string = implode(', ', $list);

		}

        return $string;

    }

    public function parse($value){

		if (!isset($this->item)){ return false; }

		if ($value) {
			$colors = array();
			$items = $this->getListItems(false);
			if ($items){
				$pos = 0;
				foreach ($items as $item){
					if (substr($value, $pos, 1) == 1){
						$colors[] = $item;
					}
					$pos++;
					if ($pos+1 > strlen($value)) { break; }
				}
			}
			$tpl = cmsTemplate::getInstance();
			ob_start();
			include($tpl->getTemplateFileName('controllers/showcase/tpl/color'));
			return ob_get_clean();
		}
		
		return false;

    }

    public function getListItems($return_list = true){

        $items = cmsCore::getModel('showcase')->getData('sc_colors');

        if ($items){

            $items = $return_list ? array_collection_to_list($items, 'id', 'title') : $items;

        } else if (isset($this->generator)) {

            $generator = $this->generator;
            $items = $generator($this->item);

        } else if ($this->hasDefaultValue()) {

            $items = string_explode_list($this->getDefaultValue());

        }

        return $items;

    }

    public function setOptions($options){
        parent::setOptions($options);
        if (!isset($this->items) && $this->hasDefaultValue()){
            $this->items = string_explode_list($this->getDefaultValue());
            $this->default = null;
        }
    }

	public function parseValue($values){

		if (!$values) { return ''; }

		$items = $this->getListItems();
		$value = '';

		if ($items){
			foreach($items as $key => $title){
				$value .= in_array($key, $values) ? '1' : '0';
			}
		}

		return $value;

	}

	public function store($value, $is_submitted, $old_value=null){

        $value = $this->parseValue($value);

		if (mb_strpos($value, '1') === false){
			return '';
		}

        return $value;

    }

    public function applyFilter($model, $values) {

		if (!is_array($values)) { return parent::applyFilter($model, $values); }

		$filter = $this->parseValue($values);
        if (!$filter) { return parent::applyFilter($model, $values); }

		$filter = str_replace('0', '_', $filter) . '%';

		return $model->filterLike($this->name, $filter);

    }

    public function getInput($value){

        $this->data['items']    = $this->getListItems();
        $this->data['selected'] = array();

        if($value){
            if(!is_array($value)){
                $pos = 0;
                foreach($this->data['items'] as $key => $title){
                    if(mb_substr($value, $pos, 1) == 1){
                        $this->data['selected'][] = $key;
                    }
                    $pos++;
                    if($pos+1 > mb_strlen($value)){break;}
                }
            }else{
                $this->data['selected'] = $value;
            }
        }

        return cmsTemplate::getInstance()->renderFormField('listbitmask', array(
            'field' => $this,
            'value' => $value
        ));

    }

}
