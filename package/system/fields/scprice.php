<?php

class fieldScprice extends cmsFormField {

    public $title       = '[showcase] Цена';
    public $sql         = 'float NULL DEFAULT NULL';
    public $filter_type = 'int';
    public $showcase = false;
	public $units = LANG_CURRENCY;
	
	public function __construct($name, $options = null) {		
        parent::__construct($name, $options);
		$this->units = !empty(cmsCore::getController('showcase')->options['cerrency']) ? cmsCore::getController('showcase')->options['cerrency'] : LANG_CURRENCY;
    }

    public function getOptions(){
        return array(
            new fieldCheckbox('filter_range', array(
                'title' => LANG_PARSER_NUMBER_FILTER_RANGE,
                'default' => false
            ))
        );
    }

    public function getRules() {

        $this->rules[] = array('number');

        return $this->rules;

    }

    public function parse($value){

        if ($value) {
			$tpl = cmsTemplate::getInstance();
			ob_start();
			include($tpl->getTemplateFileName('controllers/showcase/tpl/price'));
			return ob_get_clean();
		}
		
		return false;
    }

	public function parseTeaser($value){
        
        if ($value) {
			$value = !empty($this->item['sale']) ? $this->item['sale'] : $value;
			return cmsCore::getController('showcase')->getPriceFormat($value);
		}
		
		return false;
    }
	
	public function getStringValue($value){

        if (!$value) { return ''; }

		$string = '';
		if (!empty($value['from']) || !empty($value['to'])) {
			if (!empty($value['from'])){
				$string .= LANG_FROM . ' ' . $value['from'] . ' ';
			}
			if (!empty($value['to'])){
				$string .= LANG_TO . ' ' . $value['to'];
			}
		}

        return $string . ' ' . $this->units;

    }

    public function getDefaultVarType($is_filter=false) {

        if ($is_filter && $this->getOption('filter_range')){
            $this->var_type = 'array';
        }

        return parent::getDefaultVarType($is_filter);

    }

    public function getFilterInput($value) {

        if ($this->getOption('filter_range')){

            $from = !empty($value['from']) ? intval($value['from']) : false;
            $to = !empty($value['to']) ? intval($value['to']) : false;

            return LANG_FROM . ' ' . html_input('text', $this->element_name.'[from]', $from, array('class'=>'input-small')) . ' ' .
                    LANG_TO . ' ' . html_input('text', $this->element_name.'[to]', $to, array('class'=>'input-small')) .
                    ' ' . $this->units;

        } else {

            return parent::getFilterInput($value);

        }

    }

    public function applyFilter($model, $value) {

        if (!is_array($value)){

            return $model->filterEqual($this->name, "{$value}");

        } elseif(!empty($value['from']) || !empty($value['to'])) {

            if (!empty($value['from'])){
                $model->filterGtEqual($this->name.'+0', $value['from']);
            }
            if (!empty($value['to'])){
                $model->filterLtEqual($this->name.'+0', $value['to']);
            }

            return $model;

        }

        return parent::applyFilter($model, $value);

    }

    public function store($value, $is_submitted, $old_value=null){

        return str_replace(',', '.', trim($value));

    }

    public function getInput($value){

        $this->data['units'] = $this->units;

        return cmsTemplate::getInstance()->renderFormField('number', array(
            'field' => $this,
            'value' => $value
        ));

    }

}