<?php
class widgetShowcaseCategories extends cmsWidget {

    public $is_cacheable = false;

    public function run(){

        $ctype = cmsModel::getCachedResult('current_ctype');

        $ctype_name = $this->getOption('ctype_name');
        $tpl = $this->getOption('tpl', 'slider');
		$icon = $this->getOption('icon', 'sc_icon');
        $slides = $this->getOption('slides', 6);
        $goods_count = $this->getOption('goods_count', 0);

        $active_cat = false;

        if (!$ctype_name){

            if(!$ctype){ return false; }
            $ctype_name = $ctype['name'];

        }

        if($ctype && $ctype['name'] == $ctype_name){

            if(strpos(cmsCore::getInstance()->uri, '.html') === false){

                $current_ctype_category = cmsModel::getCachedResult('current_ctype_category');
                if(!empty($current_ctype_category['id'])){
                    $active_cat = $current_ctype_category;
                }

            } else {

                $item = cmsModel::getCachedResult('current_ctype_item');
                if(!$item){ return false; }

                if(!empty($item['category'])){
                    $active_cat = $item['category'];
                }

            }

        }

        $model = cmsCore::getModel('content');
		
		if ($this->getOption('only_parent')){
			$model->filterEqual('i.parent_id', 1);
		}

        $cats = $model->getCategoriesTree($ctype_name, $this->getOption('is_root'));
        if (!$cats) { return false; }

        if($active_cat){

            $path = array_filter($cats, function($cat) use($active_cat){
                return ($cat['ns_left'] <= $active_cat['ns_left'] &&
                        $cat['ns_level'] <= $active_cat['ns_level'] &&
                        $cat['ns_right'] >= $active_cat['ns_right'] &&
                        $cat['ns_level'] > 0);
            });

        }
		
		if ($cats && $tpl == 'list' && $goods_count){
			foreach ($cats as $index => $cat){
				$model->resetFilters();
				$cats[$index]['goods_count'] = $model->
					filterCategory($ctype_name, $cat, 1)->
					getContentItemsCount($ctype_name);
			}
		}

		if ($tpl){
			$this->setWrapper('wrapper_plain');
		}
		
		if($tpl){
            $this->setTemplate('categories_' . $tpl);
        } else {
            $this->setTemplate($this->tpl_body);
        }

        return array(
            'ctype_name' => $ctype_name,
            'icon'       => $icon,
            'slides'     => $slides,
            'cats'       => $cats,
            'active_cat' => $active_cat,
            'path'       => (!empty($path) ? $path : array())
        );

    }

}
