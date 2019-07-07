<?php

use DiDom\Document;

class actionShowcaseConversion extends cmsAction {
	
	public function __construct($request) {
        parent::__construct($request);
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
        cmsCore::includeFile('system/controllers/showcase/libs/didom/bootstrap.php');
    }

    public function run(){

		if (!$this->request->isAjax()){ cmsCore::error404(); }

		$url = $this->request->get('url', '');
		$selector = $this->request->get('selector', '');
		$data = false;

		if (!$url) {
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'URL для парсинга не указана', 'url' => true));
		}
		if (!$selector) {
			return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Заполните поле правильно', 'selector' => true));
		}
		
		$html = $this->model->curl_get_contents($url);
		
		$document = $html ? new Document($html) : false;

		if ($document) {
			$course = $document->find($selector);
			if (!empty($course[0])){
				$data = $course[0]->text();
			}
		}
		
		if ($data){
			return $this->cms_template->renderJSON(array('error' => false, 'course' => str_replace(',', '.', $data)));
		}

		return $this->cms_template->renderJSON(array('error' => true, 'message' => 'Не удалось получить данные'));

    }
	
	function getSiteData($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_FAILONERROR, true);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_REFERER, "http://google.com");
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($curl, CURLOPT_TIMEOUT, 1);
		$result = curl_exec($curl);
		curl_close($curl);
		$result = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $result);
		$result = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $result);
		$result = str_ireplace(array("\r","\n",'\r','\n'),'', $result);
		dump($result);
		$old_libxml_error = libxml_use_internal_errors(true);
		$doc = new DOMDocument();
		$doc->loadHTML('<?xml encoding="UTF-8">' . $result);
		
		libxml_use_internal_errors($old_libxml_error);
		$tags = $doc->getElementsByTagName('meta');
		if (!$tags || $tags->length === 0) {
			return false;
		} else {
			$data = array();
			foreach ($tags AS $tag) {
				if ($tag->hasAttribute('property') && strpos($tag->getAttribute('property'), 'og:title') === 0) {
					$data['title'] = strip_tags(trim(str_ireplace(array("\r","\n",'\r','\n'),'', $tag->getAttribute('content'))));
				}
				if ($tag->hasAttribute('property') && strpos($tag->getAttribute('property'), 'og:description') === 0) {
					$data['text'] = strip_tags(trim(str_ireplace(array("\r","\n",'\r','\n'),'', $tag->getAttribute('content'))));
				}
				if ($tag->hasAttribute('property') && strpos($tag->getAttribute('property'), 'og:image') === 0) {
					$data['image'] = strip_tags(trim(str_ireplace(array("\r","\n",'\r','\n'),'', $tag->getAttribute('content'))));
				}
			}
			return $data;
		}
		
		return false;
	}

}
