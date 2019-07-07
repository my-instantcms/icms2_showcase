<?php

	$this->setPageTitle('Переход на страницу оплаты');
	$this->addBreadcrumb('Переход на страницу оплаты');
	
	if (!empty($system['conversion'])){
		$conversion = is_array($system['conversion']) ? $system['conversion'] : cmsModel::yamlToArray($system['conversion']);
		if (!empty($conversion['status']) && $conversion['status'] == 1){
			$formula = !empty($conversion['formula']) ? $conversion['formula'] : false;
			$course = !empty($conversion['course']) ? $conversion['course'] : false;
			if ($formula && $course){

				cmsCore::includeFile('system/controllers/showcase/libs/FormulaParser/FormulaParser.php');
				
				$phrase = array("{price}", "{course}");
				$healthy = array((float)$order['price'], (float)$course);
				$formula = str_replace($phrase, $healthy, $formula);

				try {
					$parser = new FormulaParser($formula);
					$result = $parser->getResult();
					if (!empty($result[1])){
						$order['price'] = (float)$result[1];
						if(strpos($order['price'], '.') !== false){
							$order['price'] = rtrim(rtrim(number_format($order['price'], 2, '.', 2), '0'), '.');
						}
					}
				} catch (\Exception $e) {
					dump($e->getMessage());
				}

			}
		}
	}

?>
<?php if ($error){ ?>
	<h1><?php html(LANG_ERROR); ?></h1>
	<p><?php html($error); ?></p>
<?php } else { ?>

	<div style="text-align:center">
		<img src="/templates/default/controllers/showcase/img/ajax-loader.gif" alt="Подождите..." />
	</div>
	
	<form method="POST" action="https://merchant.webmoney.ru/lmi/payment_utf.asp" accept-charset="utf-8" id="wm_pay_form">  
		<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?php html($order['price']); ?>">
		<input type="hidden" name="LMI_PAYMENT_DESC" value="Оплата заказа: №<?php echo !empty($order['id']) ? $order['id'] : 'Неизвестно'; ?>">
		<input type="hidden" name="LMI_PAYMENT_NO" value="<?php html($order['id']); ?>">
		<input type="hidden" name="LMI_PAYEE_PURSE" value="<?php html($system['wallet_id']); ?>">
		<?php if (!empty($system['is_test'])){ ?>
			<input type="hidden" name="LMI_SIM_MODE" value="2">
		<?php } ?>
	</form>
	
	<script>
		$(document).ready(function() {
			$('#wm_pay_form').submit();
		});
	</script>

<?php } ?>