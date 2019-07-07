<?php

class actionShowcaseCart extends cmsAction {

	public function run() {
		
		return $this->cms_template->render('backend/cart');

	}

}