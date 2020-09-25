<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php $value = $value ? cmsModel::yamlToArray($value) : false; ?>
<div id="related_list">
	<p>Более подробная информация <a href="https://yandex.ru/support/partnermarket/elements/vendor-name-model.html" target="_blank">тут</a></p>
	<table class="datagrid">
		<thead>
			<tr>
				<th>Поле от яндекса</th>
				<th>Поле от сайта</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="50%">typePrefix (Тип или категория товара)</td>
				<td>
					<?php echo html_select('relateds[typePrefix]', $relfields, (!empty($value['typePrefix']) ? $value['typePrefix'] : false)); ?>
				</td>
			</tr>
			<tr>
				<td>vendor (Производителя или бренд "торговую марку")</td>
				<td>
					<?php echo html_select('relateds[vendor]', $relfields, (!empty($value['vendor']) ? $value['vendor'] : false)); ?>
				</td>
			</tr>
			<tr>
				<td>vendorCode</td>
				<td>
					<?php echo html_select('relateds[vendorCode]', $relfields, (!empty($value['vendorCode']) ? $value['vendorCode'] : false)); ?>
				</td>
			</tr>
			<tr>
				<td>model (Модель и важные параметры)</td>
				<td>
					<?php echo html_select('relateds[model]', $relfields, (!empty($value['model']) ? $value['model'] : false)); ?>
				</td>
			</tr>
			<tr>
				<td>manufacturer_warranty (Гарантия от производителя)</td>
				<td>
					<?php echo html_select('relateds[warranty]', array(0 => 'Да', 1 => 'Нет', 3 => 'Не показать'), (!empty($value['warranty']) ? $value['warranty'] : false)); ?>
				</td>
			</tr>
			<tr>
				<td>country_of_origin (Страна производителя)</td>
				<td>
					<?php echo html_select('relateds[country]', $relfields, (!empty($value['country']) ? $value['country'] : false)); ?>
				</td>
			</tr>
		</tbody>
	</table>
</div>
<?php ob_start(); ?>
<script type="text/javascript">

	var datasets_id = <?php echo $value ? count($value) : 2; ?>;
	
	function addCurrencyFields(){
		var html = '<tr>';
		html += '<td>';
		html += '<input type="text" placeholder="Например: RUR, USD, EUR, UAH, KZT" class="input" name="currencies[' + datasets_id + '][currency]">';
		html += '</td>';
		html += '<td>';
		html += '<input type="text" placeholder="Например: 1, 23.98, CBRF, NBU, CB" class="input" name="currencies[' + datasets_id + '][rate]">';
		html += '</td>';
		html += '<td>';
		html += '<a onClick="deleteCurrencyFields(this)" class="delete">Удалить</a>';
		html += '</td>';
		html += '</tr>';
		datasets_id++;
		$('#currency_list tbody').append(html);
	}
	
	function deleteCurrencyFields(button){
		var tr = $(button).parent('td').parent('tr');
		tr.remove();
	}

</script>
<?php $this->addBottom(ob_get_clean()); ?>