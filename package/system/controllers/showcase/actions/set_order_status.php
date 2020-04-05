<?php

class actionShowcaseSetOrderStatus extends cmsAction {

    public function run($order_id = false){
		
		$is_manager = ($this->cms_user->id && in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);

        if (!$this->request->isAjax() || !$order_id || !$is_manager){ cmsCore::error404(); }

        $status_id = $this->request->get('status_id', 0);
        if (!$status_id){ return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Ошибка $status_id')); }

        $item = $this->model->getData('sc_checkouts', $order_id);
		if (!$item){ return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Запись не найдена')); }

        $result = $this->model->updData('sc_checkouts', $order_id, array('status' => $status_id));
		if ($result){
			
			cmsEventsManager::hook('sc_order_status', array($item, $status_id));

			if (!empty($item['fields'])){
				$status = $this->getStatuses($status_id);
				// $delivery = !empty($item['delivery']) ? cmsModel::yamlToArray($item['delivery']) : false;
				// if ($delivery){
					// if ($delivery){
						// if ($delivery['type'] == 'courier'){
							// $status = 'Доставляется';
						// } else {
							// $status = 'Ожидает получения';
						// }
					// }
				// }
				$cart_fields = cmsModel::yamlToArray($item['fields']);
				if (!empty($cart_fields['email'])){
					$to = array('email' => $cart_fields['email'], 'name' => $cart_fields['name']);
					$hash = !empty($item['user_id']) ? '' : '?access=' . hash("adler32", $order_id . $cart_fields['email']);
					$mail_data = array(
						'nickname' => $cart_fields['name'],
						'order_id' => $order_id,
						'status' => $status,
						'url' => href_to_abs('showcase', 'orders', array($order_id, 1)) . $hash
					);
					cmsCore::getController('messages')->sendEmail($to, array('name' => 'sc_order_status'), $mail_data);
				}
			}
			

			if (!empty($this->options['log'])){
				$author = '<a href="' . href_to('users', $this->cms_user->id) . '" target="_blank">' . $this->cms_user->nickname . '</a>';
				$title = '<a href="' . href_to('showcase', 'orders', array($order_id, $status_id)) . '" data-toggle="tooltip" data-placement="top" title="Заказ №' . $order_id . '" target="_blank">заказа</a> на ' . $this->getStatuses($status_id);
				$this->model->saveData('sc_logs', array(
					'style' => 'success',
					'icon' => 'glyphicon glyphicon-cog',
					'text' => $author . ' изменил статус ' . $title
				));
			}
			return $this->cms_template->renderJSON(array('error' => false));
		} else {
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Ошибка сохранения'));
		}

    }

}
