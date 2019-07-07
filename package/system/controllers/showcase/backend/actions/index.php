<?php

class actionShowcaseIndex extends cmsAction {

    public function run(){
		
		if (!$this->ctype_name){
			$this->redirectToAction('options');
		}
		
		$counts['goods'] = array(
			'title' => 'Товары',
			'count' => $this->model->useCache("content.list.{$this->ctype_name}")->getCount('con_' . $this->ctype_name),
			'url' => href_to($this->ctype_name, 'add'),
			'url_title' => 'Добавить товар',
			'icon' => 'glyphicon glyphicon-shopping-cart',
			'color' => '#00c0ef',
		);
		$counts['selling'] = array(
			'title' => 'Завершенные сделки',
			'count' => $this->model->filterEqual('i.status', 4)->getDataCount('sc_checkouts'),
			'url' => href_to('showcase', 'orders', array(0, 4)),
			'url_title' => 'Посмотреть',
			'icon' => 'glyphicon glyphicon-ok-sign',
			'color' => '#00a65a',
		);
		$counts['selling/wait'] = array(
			'title' => 'Сделки в процессе',
			'count' => $this->model->filterIn('i.status', array(1, 2, 3))->getDataCount('sc_checkouts'),
			'url' => href_to('showcase', 'orders', array(0, 1)),
			'url_title' => 'Посмотреть',
			'icon' => 'glyphicon glyphicon-info-sign',
			'color' => '#f39c12',
		);
		$counts['selling/failed'] = array(
			'title' => 'Неудачные сделки',
			'count' => $this->model->filterEqual('i.status', 5)->getDataCount('sc_checkouts'),
			'url' => href_to('showcase', 'orders', array(0, 5)),
			'url_title' => 'Посмотреть',
			'icon' => 'glyphicon glyphicon-remove-sign',
			'color' => '#dd4b39',
		);
		
		$logs = $this->model->orderBy('i.date', 'DESC')->limit(0, 10)->getData('sc_logs');

        return $this->cms_template->render('backend/index', array(
            'counts' => $counts,
            'logs' => $logs,
        ));

    }

}
