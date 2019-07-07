<?php

class actionShowcaseCheckout extends cmsAction {

    public function run(){

		$cart_fields = cmsUser::sessionGet('cart_fields_values');
		$data = $this->renderCartData();
		
		list($cart_fields, $data) = cmsEventsManager::hook('sc_cart_checkout', array($cart_fields, $data));
		
		$items = $data['items'];
		$fields = $data['fields'];
		$summ = $data['summ'];
		$targets = array();
		
		$pay_redirect = false;
		if (!empty($this->options['payment']) && $this->options['payment'] == 'system' && !empty($cart_fields['payment_system'])){
			$pay_redirect = $cart_fields['payment_system'];
		}

		if ($items){
			
			if (!empty($items['delivery'])){
				$delivery = $items['delivery'];
				unset($items['delivery']);
			}
			
			$goods = array();
			foreach ($items as $id => $item){
				$user_id = $item['goods']['user_id'];
				if (empty($this->options['off_inctock'])){
					if (!empty($item['variant']) && !empty($item['variant_id'])){
						$this->model->updData('sc_variations', $item['variant_id'], array('in' => ($item['variant']['in'] - (!empty($item['qty']) ? $item['qty'] : 1))));
					} else if (!empty($item['goods']['in_stock'])) {
						$this->model->editGoods($this->ctype_name, (!empty($item['goods']['id']) ? $item['goods']['id'] : $id), array('in_stock' => ($item['goods']['in_stock'] - (!empty($item['qty']) ? $item['qty'] : 1))));
					}
				}
				if (!empty($item['goods'])){
					$targets[$id] = $item['goods'];
				}
				unset($item['goods'], $item['variant']);
				$goods[$id] = $item;
				$goods[$id]['ctype_name'] = $this->ctype_name;
				$goods[$id]['item_id'] = $id;
			}

			$checkout = array(
				'goods' => $goods,
				'price' => $summ,
				'user_id' => $this->cms_user->id,
				'fields' => $cart_fields,
				'delivery' => isset($delivery) ? $delivery : null,
				'paid' => !empty($cart_fields['paid']) ? $cart_fields['paid'] : null
			);

			list($checkout, $targets) = cmsEventsManager::hook('sc_before_add_checkout', array($checkout, $targets));

			$result = $this->model->saveData('sc_checkouts', $checkout);
			if ($result){
				
				$messenger = cmsCore::getController('messages');
				
				if ($this->managers){
					$text = 'Новый заказ ждет обработки';
					foreach ($this->managers as $idx => $manager){
						if (!$this->model->filterEqual('i.user_id', $manager)->filterEqual('i.content', $text)->getItem('users_notices')){
							$messenger->addRecipient($manager);
						}
					}
					$messenger->sendNoticePM(array(
						'content' => $text,
						'actions' => array(
							'view' => array(
								'title' => LANG_SHOW,
								'href'  => href_to('showcase', 'orders', array(0, 1))
							)
						)
					));
				}
				
				if (!empty($this->options['email'])){
					$emails = trim($this->options['email']);
					if (mb_stripos($emails, ',') !== FALSE){
						$emails = explode(',', $emails);
					}
					if ($emails){
						$mail_data = array(
							'order_id' => $result,
							'summ' => $this->getPriceFormat($summ),
							'url' => href_to_abs('showcase', 'orders', array($result, 1)),
						);
						if (is_array($emails)){
							foreach ($emails as $email){
								if (!$email){ continue; }
								$to = array('email' => $email, 'name' => 'Администратор');
								$messenger->sendEmail($to, array('name' => 'sc_order_new'), $mail_data);
							}
						} else {
							$to = array('email' => $emails, 'name' => 'Администратор');
							$messenger->sendEmail($to, array('name' => 'sc_order_new'), $mail_data);
						}
					}
				}
				
				if ($this->cms_user->id){
					$to = array('email' => $this->cms_user->email, 'name' => $this->cms_user->nickname);
					$mail_data = array(
						'nickname' => $this->cms_user->nickname,
						'order_id' => $result,
						'summ' => $this->getPriceFormat($summ),
						'url' => href_to_abs('showcase', 'orders', array($result, 1)),
					);
					$messenger->sendEmail($to, array('name' => 'sc_order_accept'), $mail_data);
				} else if (!$this->cms_user->id && !empty($cart_fields['email']) && !empty($cart_fields['name'])){
					$to = array('email' => $cart_fields['email'], 'name' => $cart_fields['name']);
					$hash = hash("adler32", $result . $cart_fields['email']);
					$mail_data = array(
						'nickname' => $cart_fields['name'],
						'order_id' => $result,
						'summ' => $this->getPriceFormat($summ),
						'url' => href_to_abs('showcase', 'orders', array($result, 1)) . '?access=' . $hash
					);
					$messenger->sendEmail($to, array('name' => 'sc_order_accept'), $mail_data);
				}

				$session_name = 'sc-' . $this->ctype_name;
				if (cmsUser::isSessionSet($session_name)){ 
					cmsUser::sessionUnset($session_name);
				}

				cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');
				cmsEventsManager::hook('sc_after_add_checkout', array($checkout, $targets));

				if ($pay_redirect){
					$this->redirectTo('showcase', 'payment', array($result . (isset($hash) ? '?access=' . $hash : '')));
				} else {
					$this->redirectTo('showcase', 'orders', array($result, 1 . (isset($hash) ? '?access=' . $hash : '')));
				}

			} else {
				cmsUser::addSessionMessage('Не удалось сохранить данные.', 'error');
				$this->redirectTo('showcase', 'cart');
			}

		}
		
		cmsUser::addSessionMessage('Неизвестная ошибка', 'error');
		$this->redirectTo('showcase', 'cart');

    }

}
