<?php

class backendShowcase extends cmsBackend {
	
	public $useOptions = true;
	public $ctype_name = false;
	
	public function __construct($request) {
        parent::__construct($request);
        $this->ctype_name = !empty($this->options['ctype_name']) ? $this->options['ctype_name'] : false;
    }

	public function getSidebarMenu($action = 'index'){
		$menus = array(
			1 => array(
				'title' => 'Сводка',
				'href' => $this->cms_template->href_to('index'),
				'icon' => 'dashboard',
				'active' => ($action == 'index') ? true : false
			),
			2 => array(
				'title' => 'Товары',
				'href' => href_to($this->root_url, 'goods'),
				'icon' => 'list-alt',
				'active' => ($action == 'goods') ? true : false
			),
			3 => array(
				'title' => 'Корзина',
				'href' => href_to($this->root_url, 'cart'),
				'icon' => 'shopping-cart',
				'active' => ($action == 'cart') ? true : false
			),
			10 => array(
				'title' => LANG_OPTIONS,
				'href' => href_to($this->root_url, 'options'),
				'icon' => 'cog',
				'active' => ($action == 'options') ? true : false
			),
			11 => array(
				'title' => LANG_HELP,
				'href' => href_to($this->root_url, 'help'),
				'icon' => 'question-sign',
				'active' => ($action == 'help') ? true : false
			)
		);
		
		if ($this->ctype_name){
			$ctype = $this->model->useCache('content.types')->getItemByField('content_types', 'name', $this->ctype_name);
			if ($ctype){
				$menus[9] = array(
					'title' => 'Тип контента',
					'href' => href_to('admin', 'ctypes', array('edit', $ctype['id'])),
					'icon' => 'glyphicon glyphicon-briefcase',
					'active' => false
				);
			}
		}
		
		$menus = cmsEventsManager::hook('sc_backend_sidebar', $menus);
		
		ksort($menus);

		return $menus;
		
	}
	
	public function renderHtmlSidebar($index){
		$html = '<div class="page-sidebar">';
		$html .= '<ul class="sidebar-menu">';
		foreach($this->getSidebarMenu($index) as $menu){
			$html .= $menu['active'] ? '<li class="active">' : '<li>';
				$html .= '<a href="' . href_to($menu['href']) . '">';
					$html .= '<i class="glyphicon glyphicon-' . $menu['icon'] . '"></i>';
					$html .= '<span>' . $menu['title'] . '</span>';
				$html .= '</a>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}
	
	public function getOptionsToolbar(){
        return array(
			array(
				'class'  => 'install',
				'title'  => 'Генерировать поля для типа контента',
				'href'   => href_to($this->root_url, 'generate'),
				'confirm' => sprintf('Убедитесь, что выбрали тип контент предназначенный для Магазина')
			),
			array(
				'class'  => 'addons',
				'title'  => 'Диагностика',
				'href'   => href_to($this->root_url, 'diagnostics')
			)
		);
    }
	
	public function actionClear(){
		if (!$this->cms_user->is_admin){ cmsCore::error404(); }
		cmsCache::getInstance()->clean("showcase.sc_logs");
		$this->model->db->query("TRUNCATE TABLE `{#}sc_logs`");
		$this->redirectBack();
	}
	
}