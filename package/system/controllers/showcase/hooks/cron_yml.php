<?php

class onShowcaseCronYml extends cmsAction {
	
	private $max_count = 400;

    public function run(){

        $sources_list = $this->model->filterEqual('i.is_pub', 1)->getData('sc_aggregators', false, false, function($item){
			$item['categories'] = !empty($item['categories']) ? cmsModel::yamlToArray($item['categories']) : false;
			$item['relateds'] = !empty($item['relateds']) ? cmsModel::yamlToArray($item['relateds']) : false;
			$item['fields'] = !empty($item['fields']) ? cmsModel::yamlToArray($item['fields']) : false;
			$item['currencies'] = !empty($item['currencies']) ? cmsModel::yamlToArray($item['currencies']) : false;
			return $item;
		});
        if (!$sources_list) { return false; }
		
		if (!is_writable($this->cms_config->root_path . 'upload/export/')){
            return false;
        }
		
		$items = $this->model->
			filterIsNull('is_deleted')->
			filterNotEqual('is_private', 1)->
			filterNotEqual('is_approved', 0)->
			limit(false)->
			orderBy('i.date_pub', 'DESC')->
			useCache('content.list.' . $this->ctype_name)->
			get('con_' . $this->ctype_name);
		if (!$items){ return false; }
		
		$model = cmsCore::getModel('content');
		foreach ($items as $item){
			$items[$item['id']]['sc_props'] = array_filter((array)$model->getPropsValues($this->ctype_name, $item['id']));
		}

		$cats = $this->model->selectOnly('i.id, i.title, i.parent_id, i.ns_level')->getCategoriesTree($this->ctype_name, 0);
		$fields = $model->orderBy('i.ordering')->getContentFields($this->ctype_name);
		$props = $model->getContentProps($this->ctype_name);

        foreach ($sources_list as $list){

			if(count($items) > $this->max_count){

				$chunk_data = array_chunk($items, $this->max_count, true); unset($items);
				foreach ($chunk_data as $index => $chunk_urls) {
					$index = $index ? '_'.$index : '';
					file_put_contents(
						$this->cms_config->root_path . "upload/export/sitemap_{$this->ctype_name}{$index}.xml",
						html_minify($this->cms_template->renderInternal($this, 'xml', array(
							'items' => $chunk_urls,
							'list' => $list,
							'cats' => $cats,
							'fields' => $fields,
							'props' => $props,
							'host' => $this->cms_config->host
						)))
					);
				}

			} else {

				file_put_contents(
					$this->cms_config->root_path . "upload/export/yml_{$this->ctype_name}.xml",
					html_minify($this->cms_template->renderInternal($this, 'xml', array(
						'items' => $items,
						'list' => $list,
						'cats' => $cats,
						'fields' => $fields,
						'props' => $props,
						'host' => $this->cms_config->host
					)))
				);
			
			}

        }

        return true;

    }

}
