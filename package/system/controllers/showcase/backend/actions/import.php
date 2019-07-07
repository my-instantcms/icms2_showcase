<?php

class actionShowcaseImport extends cmsAction {

	public function run($back = false) {

		$form = $this->getForm('import', array($this->ctype_name));
		
		if ($back && cmsUser::isSessionSet('sc_import_data')){
			$data = cmsUser::sessionGet('sc_import_data');
		}

		if ($this->request->has('submit')) {
			
			$old_file = !empty($data['file']) ? $data['file'] : false;
			
            $data = $form->parse($this->request, true);
			if ($old_file && !empty($data['file'])){
				if ($old_file['id'] != $data['file']['id']){
					cmsCore::getModel('files')->deleteFile($old_file['id']);
					if (cmsUser::isSessionSet('sc_import_data')){ cmsUser::sessionUnset('sc_import_data'); }
				}
			}
			if (empty($data['file']) && $back && $old_file){
				$data['file'] = $old_file;
			}
            $errors = $form->validate($this, $data);
            if(!$errors) {
                if (!empty($data['file'])){
					if (cmsUser::isSessionSet('sc_import_data')){ cmsUser::sessionUnset('sc_import_data'); }
					cmsUser::sessionSet('sc_import_data', $data);
					$this->redirectToAction('import_fields');
				} else {
					$errors['file'] = 'Загрузите csv файл';
				}
            } else {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

		return $this->cms_template->render('backend/import/start', array(
			'form'		=> $form,
			'data'		=> isset($data) ? $data : false,
			'errors'	=> isset($errors) ? $errors : false,
		));

	}

}