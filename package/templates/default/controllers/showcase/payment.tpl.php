<?php

	$this->setPageTitle('Переход на страницу оплаты');
	$this->addBreadcrumb('Переход на страницу оплаты');
	
?>
<?php if ($error){ ?>
	<h1><?php html(LANG_ERROR); ?></h1>
	<p><?php html($error); ?></p>
<?php } else { ?>

	<p>Платежная система не до конца настроена</p>

<?php } ?>