<?php 
class actionShowcaseTabsData extends cmsAction {

	public function run($id = false, $status_id = 1, $is_more = false) {
		
		if (!$this->cms_user->id || !$this->request->isAjax()){ cmsCore::error404(); }
		
		$page = $this->cms_core->request->get('page', 1);
		$perpage = (empty($this->options['limit']) ? 15 : $this->options['limit']);

		if ($id){ $this->model->filterEqual('i.user_id', $id);}
		$total = $this->model->
			limitPage($page, $perpage)->
			filterEqual('i.status', $status_id)->
			getDataCount('sc_checkouts', false);

		$orders = $this->model->orderBy('i.date', 'DESC')->getData('sc_checkouts');
		
		if ($orders){
			foreach ($orders as $key => $order){
				if (!empty($order['goods'])){
					$goods = cmsModel::yamlToArray($order['goods']);
					if ($goods){
						foreach($goods as $index => $good){
							if (!empty($good['variant_id'])){
								$variant = $this->model->getData('sc_variations', $good['variant_id']);
								if ($variant){
									unset($variant['id'], $variant['ctype_name'], $variant['id'], $variant['item_id'], $variant['price'], $variant['in']);
									$good['variant'] = $variant;
								}
							}
							if (!empty($good['ctype_name']) && !empty($good['item_id'])){
								$item = $this->model->
									useCache("content.item.{$good['ctype_name']}")->
									selectOnly('i.id, i.title, i.slug, i.artikul')->
									getItemById('con_' . $good['ctype_name'], $good['item_id']);
								if ($item){
									unset($good['item_id']);
									$item['title'] = !empty($good['variant']['title']) ? $good['variant']['title'] : $item['title'];
									$orders[$key]['items'][$index] = $item + $good;
								}
							}
						}
					}
				}
			}
		}
		
		$fields = cmsCore::getModel('content')->
			orderBy('i.ordering')->
			filterLike('i.type', 'sc%')->
			getContentFields($this->ctype_name);

		$fields = cmsEventsManager::hook('sc_get_fields', $fields);

		$html = $this->cms_template->render('my_orders', array(
			'orders' => $orders,
			'fields' => $fields,
			'page' => $page + 1,
			'perpage' => $perpage,
			'total' => $total,
			'status_id' => $status_id,
			'page_url' => href_to('showcase', 'tabs_data', array($id, $status_id)),
			'status' => $this->getStatuses(),
			'is_manager' => (in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin),
			'is_more' => $is_more
		), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));

		return $this->cms_template->renderJSON(array('error' => false, 'html' => $orders ? $html : trim($html)));
		
	}

}