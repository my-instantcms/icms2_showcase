<?php

class actionShowcaseOrderSave extends cmsAction {

    public function run($id = false){
		
		if (!$id){ cmsCore::error404(); }
		
		if(!$this->cms_user->is_logged){
			return $this->cms_template->renderJSON(
				array(
					'error' => true,
					'message' => 'Ошибка доступа'
				)
			);
		}
		
		$is_manager = ($this->cms_user->id && in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);
		if(!$is_manager){
			return $this->cms_template->renderJSON(
				array(
					'error' => true,
					'message' => 'Ошибка доступа'
				)
			);
		}
			
		$order = $this->model->getData('sc_checkouts', $id);
		if (!$order){ 
			return $this->cms_template->renderJSON(
				array(
					'error' => true,
					'message' => 'Заказ не найден'
				)
			);
		}
		
		$data = $this->request->getAll();
		if ($data){
			
		}
		dump($data);
		
		$result = $this->model->аываываыав('sc_checkouts', $id);
		if ($result){
			if (!empty($this->options['log'])){
				$author = '<a href="' . href_to('users', $this->cms_user->id) . '" target="_blank">' . $this->cms_user->nickname . '</a>';
				$title = '<a href="' . href_to('showcase', 'orders', array($id, $order['status'])) . '" target="_blank">заказ №' . $id . '</a>';
				$this->model->saveData('sc_logs', array(
					'style' => 'info',
					'icon' => 'glyphicon glyphicon-floppy-disk',
					'text' => $author . ' изменил ' . $title
				));
			}
			return $this->cms_template->renderJSON(
				array(
					'error' => false,
					'url' => href_to('showcase', 'orders', array(0, 1))
				)
			);
		} else {
			return $this->cms_template->renderJSON(
				array(
					'error' => true,
					'message' => 'Не удалось сохранить данные'
				)
			);
		}
		
		return $this->cms_template->renderJSON(
			array(
				'error' => true,
				'message' => 'Неизвестная ошибка'
			)
		);

    }

}
