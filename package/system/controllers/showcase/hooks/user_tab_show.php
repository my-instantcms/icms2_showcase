<?php

class onShowcaseUserTabShow extends cmsAction {

    public function run($profile){
		
		if (!$this->cms_user->id){ cmsCore::error404(); }

		return $this->cms_template->renderInternal($this, 'profile_tab', array(
			'is_page' => 'profile',
			'status' => $this->getStatuses(),
			'profile' => $profile,
			'type' => $this->cms_core->request->get('type', 1)
		));

    }

}
