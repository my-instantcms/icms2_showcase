<?php

class actionShowcaseHelpView extends cmsAction {

	public function run($action = false, $id = false) {
		
		if (!$action || !$id || !is_numeric($id)){ cmsCore::error404(); }
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_REFERER, !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : $_SERVER['HTTP_HOST']);
		curl_setopt($ch, CURLOPT_URL, 'https://my-instantcms.ru/helper/showcase/' . $action . '/' . $id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "Content-Type: application/json; charset=utf-8"));
		$data = json_decode(trim(curl_exec($ch)), TRUE);
		curl_close($ch);
		
		return $this->cms_template->render('backend/help_view', array(
			'data' => $data,
			'action' => $action,
			'id' => $id
			));

	}

}