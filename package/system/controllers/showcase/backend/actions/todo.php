<?php

class actionShowcaseTodo extends cmsAction {

    public function run($type = false) {

        if (!$type || !$this->request->isAjax()) {cmsCore::error404();}
		
		switch ($type) {
			case 'load':
				$lists = $this->model->orderBy('i.ordering', 'ASC')->getData('sc_todo');
				if ($lists) {
					foreach($lists as $id => $list) {
						$items = $this->model->filterEqual('i.listId', $id)->orderBy('i.ordering', 'ASC')->getData('sc_todo_items');
						$lists[$id]['items'] = $items ? array_merge($items, array()) : false;
					}
				} else {
					$lists = array(array('title' => 'Задачи', 'defaultStyle' => 'lobilist-danger'));
				}

				$this->cms_template->renderJSON(array('lists' => array_merge($lists, array())), true);
				break;
			case 'add':
				$data = $this->request->getAll();
				if ($data && is_array($data) && isset($data['title'])) {
					$data['description'] = nl2br(str_replace("<br>", "", $data['description']));
					$add = $this->model->saveData('sc_todo_items', $data);
				}
				$this->cms_template->renderJSON(array('success' => isset($add) ? true : false), true);
				break;
			case 'list':
				$add = $this->model->saveData('sc_todo', array('user_id' => $this->cms_user->id));
				$this->cms_template->renderJSON(array('success' => $add ? $add : false), true);
				break;
			case 'update':
				$data = $this->request->getAll();
				if ($data && is_array($data) && isset($data['title']) && isset($data['id']) && $data['id']) {
					$id = $data['id'];
					unset($data['id']);
					$data['description'] = nl2br(str_replace("<br>", "", $data['description']));
					$update = $this->model->updData('sc_todo_items', $id, $data);
				}
				$this->cms_template->renderJSON(array('success' => isset($update) ? $update : false), true);
				break;
			case 'delete':
				if ($this->request->has('id')) {
					$id = $this->request->get('id', 0);
					$table = $this->request->has('table') ? false : '_items';
					$delete = $id ? $this->model->deleteData('sc_todo' . $table, $id) : false;
					if (!$table && $delete){
						$this->model->deleteData('sc_todo_items', $id, 'listId');
					}
				}
				$this->cms_template->renderJSON(array('success' => isset($delete) ? $delete : false), true);
				break;
			case 'toggle':
				if ($this->request->has('id')) {
					$id = $this->request->get('id', 0);
					$item = $this->model->selectOnly('i.id, i.done')->getData('sc_todo_items', $id);
					$update = $this->model->updData('sc_todo_items', $id, array('done' => ($item['done'] ? false : true)));
				}
				$this->cms_template->renderJSON(array('success' => isset($update) ? $update : false), true);
				break;
			case 'title':
				if ($this->request->has('id') && $this->request->has('title')) {
					$id = $this->request->get('id', 0);
					$title = $this->request->get('title', '');
					$update = $this->model->updData('sc_todo', $id, array('title' => $title));
				}
				$this->cms_template->renderJSON(array('success' => isset($update) ? $update : false), true);
				break;
			case 'reorder':
				if ($this->request->has('items')) {
					$items = $this->request->get('items', array());
					$table = $this->request->has('table') ? '_items' : '';
					if ($items) {
						$update = $this->model->reorderData('sc_todo' . $table, $items);
					}
				}
				$this->cms_template->renderJSON(array('success' => isset($update) ? $update : false), true);
				break;
			case 'style':
				if ($this->request->has('id') && $this->request->has('defaultStyle')) {
					$id = $this->request->get('id', 0);
					$defaultStyle = $this->request->get('defaultStyle', 'lobilist-default');
					$update = $this->model->updData('sc_todo', $id, array('defaultStyle' => $defaultStyle));
				}
				$this->cms_template->renderJSON(array('success' => isset($update) ? $update : false), true);
				break;
			default:
			   $this->cms_template->renderJSON(array('success' => false), true);
		}

    }

}