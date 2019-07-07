<?php

class onShowcaseAdminpanelMenu extends cmsAction {

    public function run($menus){
		
		if (empty($this->options['menu'])){ return $menus; }
		
		$menu = array(
			'title' => 'Магазин',
			'url' => href_to('admin', 'controllers', array('edit', 'showcase')),
			'options' => array(
				'class' => 'item-showcase'
			)
		);

        return array_merge(array($menu), $menus);

    }

}
