<?php

class onShowcaseContentListFilter extends cmsAction {

    public function run($data){

		if ($this->cms_core->uri_action != $this->ctype_name || !empty($this->options['variants_off'])){ return $data; }
		
        list($ctype, $model) = $data;
		if ($this->cms_core->request->has('title') || $this->cms_core->request->has('price')){
			$title = $this->cms_core->request->get('title', '');
			$price = $this->cms_core->request->get('price', array());
			$price = (!empty($price['from']) || !empty($price['to'])) ? $price : $this->cms_core->request->get('price', 0);
			$filter = '';
			if ($title || $price){
				if (!empty($model->where)){
					$where = str_replace('  ', '', $model->where);
					$filters = explode('AND', $where);
					if ($filters){
						foreach($filters as $key => $value){
							if (stripos($value, 'i.title') !== false || stripos($value, 'i.price') !== false){ continue; }
							$filter .= $value . '  AND  ';
						}
					}
				}
				$model->distinctSelect();
				$model->filterEqual('i.is_approved', 1)->
					filterEqual('i.is_pub', 1)->
					filterIsNull('i.is_deleted')->
					filterIsNull('i.is_parent_hidden');
				$model->filterOr();
				$model->filterStart();
				$model->joinLeft('sc_variations',  'scv', 'scv.item_id = i.id');
				if ($filter) {
					$model->where .= $filter; /*rtrim($filter, '  AND');*/
				}
				if ($title){
					$model->filterLike('scv.title', "%{$title}%");
				}
				if ($price){
					if (!is_array($price)){
						$model->filterEqual('scv.price', $price);
					} elseif(!empty($price['from']) || !empty($price['to'])) {

						if (!empty($price['from'])){
							$model->filterGtEqual('scv.price+0', $price['from']);
						}
						if (!empty($price['to'])){
							$model->filterLtEqual('scv.price+0', $price['to']);
						}

					}
				}
				$model->filterEnd();
			}
		}

        return array($ctype, $model);

    }

}
