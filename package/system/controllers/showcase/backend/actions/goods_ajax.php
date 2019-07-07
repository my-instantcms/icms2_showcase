<?php

class actionShowcaseGoodsAjax extends cmsAction {

	public function run($do = 'list') {
		
		if(!$this->request->isAjax()) {cmsCore::error404();}
		
		$variant_field = $this->model->getItemByField('con_' . $this->ctype_name . '_fields', 'type', 'scvariations');

        $data = $this->request->getAll();
        if ($data){
            if (!empty($data['category_id'])){ $this->model->filterEqual('i.category_id', $data['category_id']); }
            if (!empty($data['price_min'])){ $this->model->filterGtEqual('i.price', $data['price_min']); }
            if (!empty($data['price_max'])){ $this->model->filterLtEqual('i.price', $data['price_max']); }
			if (!empty($data['sale_min'])){ $this->model->filterGtEqual('i.sale', $data['sale_min']); }
            if (!empty($data['sale_max'])){ $this->model->filterLtEqual('i.sale', $data['sale_max']); }
            if (!empty($data['title'])){ $this->model->filterLike('i.title', '%' . $data['title'] . '%'); }
            if (!empty($data['artikul'])){ $this->model->filterEqual('i.artikul', $data['artikul']); }
            if (!empty($data['id'])){ $this->model->filterEqual('i.id', $data['id']); }
        }

		$select = 'i.id';
		$select .= ', i.title, i.user_id, i.category_id, i.date_pub, i.price, i.sale, i.artikul';
		if ($variant_field){
			$select .= ', i.variants';
		}
		
		$items = $this->model->
			selectOnly($select)->
			orderBy('i.date_pub', 'DESC')->
			getGoods($this->ctype_name);
			
		if ($items){
			foreach ($items as $id => $item){
				$items[$id]['variants_count'] = '0 варианов';
				if (!empty($item['variants'])){
					$variants = is_array($item['variants']) ? $item['variants'] : cmsModel::yamlToArray($item['variants']);
					$items[$id]['variants'] = $variants;
					$items[$id]['variants_count'] = html_spellcount(count($variants), 'вариант|варианта|вариантов');
				}
			}
		}

		return $this->cms_template->renderJSON(array(
			'last_page' => 1,
			'data' => $items ? array_values($items) : array()
		));

		$this->halt();

	}

}