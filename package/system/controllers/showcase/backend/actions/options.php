<?php

class actionShowcaseOptions extends cmsAction {

    public function run(){

        $form = $this->getForm('options');
        if (!$form) { cmsCore::error404(); }

        $form = $this->addControllerSeoOptions($form);

        $options = cmsController::loadOptions($this->name);

        if ($this->request->has('submit')){

            $options = array_merge( $options, $form->parse($this->request, true) );
            $errors  = $form->validate($this, $options);

            if (!$errors){

                cmsUser::addSessionMessage(LANG_CP_SAVE_SUCCESS, 'success');
				
				if (!empty($options['old_ctype'])){
					if ($options['old_ctype'] != $options['ctype_name']){
						$options['old_ctype'] = $options['ctype_name'];
					}
				} else {
					$options['old_ctype'] = $options['ctype_name'];
				}

                cmsController::saveOptions($this->name, $options);

                $this->processCallback(__FUNCTION__, array($options));

                $this->redirectToAction('options');

            }

            if ($errors){

                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');

            }

        }

        $template_params = array(
            'toolbar' => $this->getOptionsToolbar(),
            'options' => $options,
            'form'    => $form,
            'errors'  => isset($errors) ? $errors : false
        );

        return $this->cms_template->render('backend/options', $template_params);

    }


}