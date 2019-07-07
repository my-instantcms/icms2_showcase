<?php

class actionShowcaseRefreshCarts extends cmsAction {

    public function run($item_qty_increment = false){
		
		if (!$this->request->isAjax()){ cmsCore::error404(); }
		
		$data = array('steps' => $this->getNextStep(0), 'device_type' => cmsRequest::getDeviceType());
		$data['next'] = !empty($data['steps']['next']['id']) ? $data['steps']['next']['id'] : 0;

		if ($item_qty_increment && $this->request->has('qty')){
			$item_id = $this->request->get('item_id', '');
			$qty = $this->request->get('qty', 1);
			if ($item_id){
				$session_name = 'sc-' . $this->ctype_name;
				if (!empty($_SESSION[$session_name])){
					if (!empty($_SESSION[$session_name][$item_id]['qty'])) {
						$_SESSION[$session_name][$item_id]['qty'] = $qty;
					}
				}
			}
		}
		
		$html = false;

		if ($this->request->has('styles') && is_array($this->request->get('styles'))){
			foreach($this->request->get('styles', array()) as $style => $val){
				$html[$style] = $this->cms_template->render('tpl/cart_' . $style, $this->renderCartData($data), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));
			}
		}

		$html = $html ? $html : $this->cms_template->render('widgets/cart/cart', $this->renderCartData($data), new cmsRequest(array(), cmsRequest::CTX_INTERNAL));
		
		return $this->cms_template->renderJSON(array('error' => false, 'html' => $html));

    }

}
