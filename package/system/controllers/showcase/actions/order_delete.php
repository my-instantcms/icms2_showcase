<?php

class actionShowcaseOrderDelete extends cmsAction {

    public function run($id = false){
		
		if (!$id){ cmsCore::error404(); }
		
		if(!$this->cms_user->is_logged){
			cmsUser::goLogin();
		}
		
		$is_manager = ($this->cms_user->id && in_array($this->cms_user->id, $this->managers) || $this->cms_user->is_admin);
			
		$order = $this->model->getData('sc_checkouts', $id);
		if (!$order || !$is_manager){ cmsCore::error404(); }
		
		$result = $this->model->deleteData('sc_checkouts', $id);
		if ($result){
			if (!empty($this->options['log'])){
				$author = '<a href="' . href_to('users', $this->cms_user->id) . '" target="_blank">' . $this->cms_user->nickname . '</a>';
				$this->model->saveData('sc_logs', array(
					'style' => 'danger',
					'icon' => 'glyphicon glyphicon-trash',
					'text' => $author . ' удалил заказ №' . $id
				));
			}
			cmsUser::addSessionMessage('Заказ успешно удален', 'success');
		} else {
			cmsUser::addSessionMessage('Ошибка удаление заказа', 'error');
		}
		$this->redirectTo('showcase', 'orders', array(0, 1));

    }

}
