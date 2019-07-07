<?php

class actionShowcaseSetPayStatus extends cmsAction {

    public function run($order_id = false){
		
		$is_manager = ($this->cms_user->id && in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);

        if (!$this->request->isAjax() || !$order_id || !$is_manager){ cmsCore::error404(); }

        $status_id = $this->request->get('status_id', 0);
        if (!$status_id){ return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Ошибка $status_id')); }

        $item = $this->model->getData('sc_checkouts', $order_id);
		if (!$item){ return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Запись не найдена')); }

        $result = $this->model->updData('sc_checkouts', $order_id, array('paid' => $status_id));
		if ($result){
			

			if (!empty($this->options['log'])){
				$author = '<a href="' . href_to('users', $this->cms_user->id) . '" target="_blank">' . $this->cms_user->nickname . '</a>';
				$title = '<a href="' . href_to('showcase', 'orders', array($order_id, $item['status'])) . '" data-toggle="tooltip" data-placement="top" title="Заказ №' . $order_id . '" target="_blank">заказа</a> на ' . (($status_id == 1) ? 'Ожидается оплата' : 'Оплачено');
				$this->model->saveData('sc_logs', array(
					'style' => 'success',
					'icon' => 'glyphicon glyphicon-cog',
					'text' => $author . ' изменил статус оплаты ' . $title
				));
			}
			return $this->cms_template->renderJSON(array('error' => false));
		} else {
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Ошибка сохранения'));
		}

    }

}
