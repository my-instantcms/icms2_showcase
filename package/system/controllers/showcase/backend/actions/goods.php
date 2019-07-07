<?php

class actionShowcaseGoods extends cmsAction {

	public function run($do = false) {

        if (!$do){
            return $this->cms_template->render('backend/goods');
        }
		
		$cats = $this->model->selectOnly('i.id, i.title, i.ns_level')->getCategoriesTree($this->ctype_name, false);
		if ($cats){
			foreach($cats as $cat){
				if ($cat['ns_level'] > 1){
					$cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
				}
				$cats_all[$cat['id']] = $cat;
				$cats_list[$cat['id']] = $cat['title'];

			}
		}
		
		$fields = cmsCore::getModel('content')->orderBy('i.ordering')->filterLike('i.type', 'sc%')->getContentFields($this->ctype_name);
		
		return $this->cms_template->render('backend/goods_' . $do, array(
			'cats_all' => isset($cats_all) ? $cats_all : false,
			'cats_list' => isset($cats_list) ? $cats_list : false,
			'fields' => $fields
		));

	}

}