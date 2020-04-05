<?php

class actionShowcaseSetPayment extends cmsAction {

    public function run($order_id = false){
		
		if (!$order_id || !is_numeric($order_id)){ cmsCore::error404(); }
		
		$order = $this->model->getData('sc_checkouts', $order_id);
		if (!$order){
			if ($this->request->isAjax()) {
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Заказ не найден'));
			} else {
				cmsCore::error404();
			}
		}
		
		if ($this->request->has('id')){
			if(!$this->request->isAjax()) { cmsCore::error404(); }
			$id = $this->request->get('id', 0);
			if (!$id && empty($this->options['system_pay_cash']) && empty($this->options['system_pay_check'])){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Неизвестные ID')); 
			}
				
			$fields = !empty($order['fields']) ? cmsModel::yamlToArray($order['fields']) : false;
			if (!empty($fields['paid']) && $fields['paid'] == 2){ 
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Заказ уже оплачен')); 
			}

			$system_id = !empty($fields['payment_system']) ? $fields['payment_system'] : false;
			if ($system_id && $system_id == $id){ return $this->cms_template->renderJSON(array('error' => true)); }

			$system = $id ? $this->model->filterEqual('i.is_pub', 1)->getData('sc_pay_systems', $id) : false;
			if (!$system && empty($this->options['system_pay_cash']) && empty($this->options['system_pay_check'])){
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Система оплаты не найдена или выключена'));
			}
			
			if (!$system){
				if ($system_id == 999){
					$system = $this->model->getCheckPaySystem();
				} else {
					$system = $this->model->getCashPaySystem();
				}
			}

			if (!$fields){ 
				return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Поля не найдены')); 
			}

			$fields['payment_system'] = $id;
			$result = $this->model->updData('sc_checkouts', $order_id, array('fields' => $fields));
			if ($result){
				return $this->cms_template->renderJSON(array('error' => false, 'title' => $system['title'])); 
			}
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Не удалось выполнить задачу'));

		}
		
		if (!empty($this->options['system_pay_cash'])){
			$cash_systems = $this->model->getCashPaySystem();
		}
		
		if (!empty($this->options['system_pay_check'])){
			$check_systems = $this->model->getCheckPaySystem();
		}
		
		$systems = $this->model->
			filterEqual('i.is_pub', 1)->
			orderBy('i.ordering', 'ASC')->
			getData('sc_pay_systems');
			
		$systems = !empty($check_systems) ? array_merge(array($check_systems), ($systems ? $systems : array())) : $systems;
		$systems = !empty($cash_systems) ? array_merge(array($cash_systems), ($systems ? $systems : array())) : $systems;

		$this->cms_template->render('set_payment', array(
			'order' => $order,
			'systems' => $systems
		));
		
    }

}
