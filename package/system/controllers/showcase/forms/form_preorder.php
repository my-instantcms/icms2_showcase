<?php 
class formShowcasePreorder extends cmsForm {

	public function init($variant_id = false, $title = ''){
		
		$user = cmsUser::getInstance();
		$name = $user->id ? $user->nickname : '';
		$email = $user->id ? $user->email : '';

		return array(
			'basic' => array(
				'type' => 'fieldset',
				'title' => $title,
				'childs' => array(

					'name' => new fieldString('name',array(
						'title' => 'Как к Вам обращаться?',
						'default' => $name
					)),

					'email' => new fieldString('email',array(
						'title' => 'Телефон или почта',
						'default' => $email
					)),

					'text' => new fieldText('text',array(
						'title' => 'Комментарий',
					))

				)
			)
		);

	}

}