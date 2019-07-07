<?php

use DiDom\Document;

class onShowcaseCronCourse extends cmsAction {
	
	public function __construct($request) {
        parent::__construct($request);
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
        cmsCore::includeFile('system/controllers/showcase/libs/didom/bootstrap.php');
    }
	
	public function run(){

		$systems = $this->model->
			filterEqual('i.is_pub', 1)->
			filterNotNull('i.conversion')->
			getData('sc_pay_systems');

		if ($systems){
			foreach ($systems as $system){
				if (!empty($system['conversion'])){

					$conversion = cmsModel::yamlToArray($system['conversion']);

					if (empty($conversion['status']) || empty($conversion['url']) || empty($conversion['selector'])){ continue; }

					$html = $this->model->curl_get_contents($conversion['url']);
					$document = $html ? new Document($html) : false;

					if ($document) {
						$course = $document->find($conversion['selector']);
						if (!empty($course[0])){
							$data = $course[0]->text();
							if ($data){
								$conversion['course'] = str_replace(',', '.', $data);
								$this->model->updData('sc_pay_systems', $system['id'], array('conversion' => $conversion));
							}
						}
					}

				}
			}
		}
		
	}
	
}