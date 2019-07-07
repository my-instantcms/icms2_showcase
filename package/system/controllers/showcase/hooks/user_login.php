<?php

class onShowcaseUserLogin extends cmsAction {

    public function run($user){
		
		$is_manager = (in_array($user['id'], $this->managers) || $user['is_admin']);

        if ($is_manager && !empty($this->options['log'])) {
			$author = '<a href="' . href_to('users', $user['id']) . '" target="_blank">' . $user['nickname'] . '</a>';
			$this->model->saveData('sc_logs', array(
				'style' => 'warning',
				'icon' => 'glyphicon glyphicon-warning-sign',
				'text' => $author . ' авторизовался с IP ' . $user['ip']
			));
		}

        return $user;

    }

}
