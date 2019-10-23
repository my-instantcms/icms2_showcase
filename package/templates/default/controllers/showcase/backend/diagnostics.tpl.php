<?php $this->addBreadcrumb('Диагностика'); ?>

<p>В данном разделе отображается данные о таблицах и поля после обновления компонента.</p>
<p>Если по каким то причинам не были созданы таблицы или поля, здесь они отображаются и можно будет починить в один клик.</p>
<p>Если видите текст <b style="color:green">Да</b> - значить всё нормально, иначе система предлагает <b style="color:red">Исправить</b></p>
<p>
	<div class="btn_showcase_tpl">
		<a href="fix/showcase_item">Пересоздать файл <?php html($this->controller->ctype_name); ?>_item.tpl.php</a>
	</div>
	<div class="btn_showcase_tpl">
		<a href="fix/index_showcase">Пересоздать файл index_<?php html($this->controller->ctype_name); ?>.tpl.php</a>
	</div>
</p>
<h3>Версия 1.2.0</h3>
<ul class="diagnostics">
	<li>Таблица sc_aggregators: <?php echo $sc_aggregators ? '<b>Да</b>' : '<a href="fix/sc_aggregators">Исправить</a>'; ?></li>
	<li>Задача yml планировщика: <?php echo $yml ? '<b>Да</b>' : '<a href="fix/yml">Исправить</a>'; ?></li>
</ul>
<h3>Версия 1.1.7</h3>
<ul class="diagnostics">
	<li>Таблица sc_tabs: <?php echo $sc_tabs ? '<b>Да</b>' : '<a href="fix/sc_tabs">Исправить</a>'; ?></li>
	<li>Поле receipt в sc_pay_systems: <?php echo $receipt ? '<b>Да</b>' : '<a href="fix/receipt">Исправить</a>'; ?></li>
	<li>Поле tax в sc_pay_systems: <?php echo $tax ? '<b>Да</b>' : '<a href="fix/tax">Исправить</a>'; ?></li>
	<li>Поле nds в sc_pay_systems: <?php echo $nds ? '<b>Да</b>' : '<a href="fix/nds">Исправить</a>'; ?></li>
</ul>
<h3>Версия 1.1.5</h3>
<ul class="diagnostics">
	<li>Поле v_sale в sc_variations: <?php echo $v_sale ? '<b>Да</b>' : '<a href="fix/v_sale">Исправить</a>'; ?></li>
	<li>Поле v_seo_keys в sc_variations: <?php echo $v_seo_keys ? '<b>Да</b>' : '<a href="fix/v_seo_keys">Исправить</a>'; ?></li>
	<li>Поле v_seo_desc в sc_variations: <?php echo $v_seo_desc ? '<b>Да</b>' : '<a href="fix/v_seo_desc">Исправить</a>'; ?></li>
	<li>Поле v_seo_title в sc_variations: <?php echo $v_seo_title ? '<b>Да</b>' : '<a href="fix/v_seo_title">Исправить</a>'; ?></li>
</ul>
<h3>Версия 1.1.0</h3>
<ul class="diagnostics">
	<li>Запись paid: <?php echo $paid ? '<b>Да</b>' : '<a href="fix/paid">Исправить</a>'; ?></li>
	<li>Поле price имеет тип float: <?php echo $price_float ? '<b>Да</b>' : '<a href="fix/price_float">Исправить</a>'; ?></li>
	<li>Поле ordering в sc_variations: <?php echo $v_ordering ? '<b>Да</b>' : '<a href="fix/v_ordering">Исправить</a>'; ?></li>
</ul>
<style>
	.diagnostics{width:320px}
	.diagnostics li b{color:green;float:right}
	.diagnostics li a{color:red;font-weight:bold;float:right}
	.btn_showcase_tpl{
		border: 1px solid #ccc;
		background: #eee;
		border-radius: 3px;
		display: inline-block;
		margin: 0 5px 5px 0;
	}
	.btn_showcase_tpl a{
		text-decoration: none;
		color: #444;
		padding: 8px;
		display: block;
	}
</style>