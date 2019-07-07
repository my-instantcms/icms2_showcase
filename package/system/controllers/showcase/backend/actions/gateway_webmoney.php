<?php

class actionShowcaseGatewayWebmoney extends cmsAction {
	
	public $page_name = 'pay_systems';
	public $gateway_name = 'webmoney';

    public function run($id = false){
		
		$gateway = $this->model->filterEqual('i.name', $this->gateway_name)->getData('sc_pay_gateways', 0, 1);
		if (!$gateway){ cmsCore::error404(); }
		
		if ($id){
			$item = $this->model->getData('sc_' . $this->page_name, $id);
			if (!$item){ cmsCore::error404(); }
			$item['conversion'] = !empty($item['conversion']) ? cmsModel::yamlToArray($item['conversion']) : null;
		}
		
		$file_form = !empty($gateway['file_form']) ? $gateway['file_form'] : $this->page_name;

        $form = $this->getForm($file_form, array($this->ctype_name));
        $form->addHtmlBlockToBeginning(2, '<fieldset id="fset_0"><legend>Инструкция</legend><p>Что бы начать принимать платежи через Webmoney, вы должны <a href="https://wallet.webmoney.ru/signup?lang=ru" target="_blank">зарегистрироваться</a>, создать кошелек WMR, WMZ или другой валюты.</p><p>Зарегистрироваться в сервисе <a href="https://merchant.webmoney.ru/conf/default.asp" target="_blank">Webmoney Мерчант</a> и получить <a href="https://wiki.webmoney.ru/projects/webmoney/wiki/%D1%84%D0%BE%D1%80%D0%BC%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D0%B9_%D0%B0%D1%82%D1%82%D0%B5%D1%81%D1%82%D0%B0%D1%82" target="_blank">формальный аттестат</a> или выше.</p><p>Потом на странице <a href="https://merchant.webmoney.ru/conf/default.asp" target="_blank">Webmoney Мерчант</a> прейти в вкладку Настройки.<br />Выбрать R, Z или другой кошелек и нажать Настроить<br />На странице настройки нужна заполнить поля:<br /><br /><b>Тестовый/Рабочий режимы</b> - укажите тестовый (если хотите тестировать и <span style="color:red">не забудьте потом убрать</span>) или рабочий.<br /><b>Торговое имя</b> - имя магазина, который будет отображаться на странице оплаты<br /><b>Secret Key</b> - любой набор символов на английском (далее этот код вставить тут, в поле <label for="secret_key" style="color:#68809B;cursor:pointer">Secret Key</label>)<br /><b>Secret Key X20</b> - Оставляем пустой<br /><b>Result URL</b> - укажем <b>' . cmsConfig::get('host') . '/showcase/process_webmoney</b> (так же поставить галочку на "Передавать параметры в предварительном запросе")<br /><b>Proxy для Result URL</b> - не использовать<br /><b>Success URL</b> - укажем <b>' . cmsConfig::get('host') . '/showcase/success_webmoney</b> (POST)<br /><b>Fail URL и метод его вызова</b> - укажем <b>' . cmsConfig::get('host') . '/showcase/fail_webmoney</b> (POST)<br /><b>Позволять использовать URL, передаваемые в форме</b> - пусто<br /><b>Высылать оповещение об ошибке платежа на кипер</b> - включить<br /><b>Метод формирования контрольной подписи</b> - SHA256<br /><i>Остальные поля выкл.</i></p></fieldset>');

        if ($this->request->has('submit')) {
            $item = $form->parse($this->request, true);
            $errors = $form->validate($this, $item);
            if(!$errors) {
				$item['gateway_name'] = $this->gateway_name;
                $result = $id ? $this->model->updData('sc_' . $this->page_name, $id, $item) : $this->model->saveData('sc_' . $this->page_name, $item);
				if ($result){
					cmsUser::addSessionMessage(LANG_SUCCESS_MSG, 'success');
				} else {
					cmsUser::addSessionMessage('Не удалось сохранить данные.', 'error');
				}
                $this->redirectToAction($this->page_name);
            } else {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/' . $this->page_name . '_form', array(
			'do' 	 => $id ? 'edit' : 'add',
			'form' 	 => $form,
			'item' 	 => isset($item) ? $item : false,
			'errors' => isset($errors) ? $errors : false,
			'id' 	 => $id
		));
    }
}