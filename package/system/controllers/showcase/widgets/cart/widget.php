<?php
class widgetShowcaseCart extends cmsWidget {
	
	public $is_cacheable = false;

    public function run(){
		
		$showcase = cmsCore::getController('showcase');
		
		if ($this->getOption('hide', 0)){
			if ($showcase->cms_core->uri_controller == 'showcase' && $showcase->cms_core->uri_action == 'cart'){
				return false;
			}
		}

		$data = $showcase->renderCartData(array(
			'steps' => array('current' => array('id' => 0)),
			'device_type' => cmsRequest::getDeviceType()
		));
		
		return $data ? $data : array();

    }

}
