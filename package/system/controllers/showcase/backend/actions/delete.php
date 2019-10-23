<?php
class actionShowcaseDelete extends cmsAction {

    public function run($table = false, $id = false){

		if (!$table || !$id){ cmsCore::error404(); }
		
		if (!cmsForm::validateCSRFToken( $this->request->get('csrf_token', '') )){
            cmsCore::error404();
        }

		$item = $this->model->getData($table, $id);
		
		if ($item){
			$result = empty($item['is_fixed']) ? $this->model->deleteData($table, $id) : false;
			if ($result){
				if ($table == 'sc_pay_systems' && !empty($item['icon'])){
					if (!is_array($item['icon'])){ 
						$item['icon'] = cmsModel::yamlToArray($item['icon']);
					}
					foreach($item['icon'] as $image_url){
						files_delete_file($image_url, 2);
					}
				}
				cmsUser::addSessionMessage('Данные успешно удалены', 'success');
				$this->redirectBack();
			}
		}

		cmsUser::addSessionMessage('Ошибка удаление данных', 'error');
		$this->redirectBack();

	}

}