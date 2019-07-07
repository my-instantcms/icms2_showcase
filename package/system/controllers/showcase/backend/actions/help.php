<?php

class actionShowcaseHelp extends cmsAction {

	public function run($action = false) {
		
		if ($action){
			
			$date = date("ymd");
			$cache = cmsConfig::get('root_path') . "/cache/showcase/{$action}_{$date}.yml";
			$cache_date = substr($cache, -10, 6);
			$data = array();
			if(file_exists($cache) && $cache_date == date("ymd")){
				$result = @file_get_contents($cache);
				$data = cmsModel::yamlToArray($result);
			} else {

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_HEADER, 0);
				curl_setopt($ch, CURLOPT_REFERER, !empty($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : $_SERVER['HTTP_HOST']);
				curl_setopt($ch, CURLOPT_URL, 'https://my-instantcms.ru/helper/showcase/' . $action);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_TIMEOUT, 5);
				curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "Content-Type: application/json; charset=utf-8"));
				$data = json_decode(trim(curl_exec($ch)), TRUE);
				curl_close($ch);
				
				if($data && is_writable(dirname($cache))){
					foreach(glob(dirname($cache) . '/' . $action . '*') as $file){unlink($file);}
					$cache_yaml = cmsModel::arrayToYaml($data);
					@file_put_contents($cache, $cache_yaml);
				}

			}

			return $this->cms_template->renderJSON(array(
				'html' => !empty($data['html']) ? $data['html'] : '<p class="sc_help_error">Неизвестная ошибка</p>'
			));
			
		}
		
		return $this->cms_template->render('backend/help');

	}

}