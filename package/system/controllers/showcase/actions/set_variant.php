<?php

class actionShowcaseSetVariant extends cmsAction {

    public function run($variant_id = false){
		
		if (!$this->request->isAjax() || !$variant_id){ cmsCore::error404(); }
		
		$variant = $this->model->getData('sc_variations', $variant_id);
		if (!$variant){ return $this->cms_template->renderJSON(array('error' => true)); }

		return $this->cms_template->renderJSON(array(
			'error' => false,
			'title' => $variant['title'],
			'price' => $variant['price'],
			'price_round' => $this->getPriceFormat($variant['price'], false, false),
			'sale' => $variant['sale'],
			'sale_round' => $variant['sale'] ? $this->getPriceFormat($variant['sale'], false, false) : false,
			'photo' => $variant['photo'] ? cmsModel::yamlToArray($variant['photo']) : false,
			'artikul' => $variant['artikul'],
			'in_stock' => $variant['in']
		));

    }

}
