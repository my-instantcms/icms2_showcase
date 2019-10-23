<?php
/* Для яндекс турбо страниц */
class actionShowcaseAddtocart extends cmsAction {

    public function run(){

		$url = $this->request->get('url', '');
		$action = $this->request->get('action', 'BUY');
		$item_id = $this->request->get('id', 0);
		if (!$item_id){ cmsCore::error404(); }
		
		$query_str = parse_url($url, PHP_URL_QUERY);
		parse_str($query_str, $query_params);
		$variant_id = !empty($query_params['variant']) ? $query_params['variant'] : 0;
		
		if ($variant_id){
			$variant = $this->model->selectOnly('i.id, i.item_id')->getData('sc_variations', $variant_id);
			if (!empty($variant['item_id'])){
				$item_id = $variant['item_id'];
			}
		}
		
		$ctype_name = $this->ctype_name;
		
		$session_name = 'sc-' . $ctype_name . ':' . $item_id . 'v' . $variant_id;
		
		$data = array('qty' => 1, 'variant_id' => $variant_id);
		if ($data){
			if (cmsUser::isSessionSet($session_name)){ 
				cmsUser::sessionUnset($session_name);
			}
			cmsUser::sessionSet($session_name, $data);
		}
		
		$this->redirectTo('showcase', 'cart');

    }

}
