<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php $value = $value ? cmsModel::yamlToArray($value) : false; ?>
<div id="currency_list">
	<p>Более подробная информация <a href="https://yandex.ru/support/webmaster/goods-prices/technical-requirements.html#concept3__currencies" target="_blank">тут</a></p>
	<table class="datagrid">
		<thead>
			<tr>
				<th>Валюта</th>
				<th>rate</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php if ($value && is_array($value)){ ?>
				<?php foreach ($value as $key => $currency){ ?>
					<tr>
						<td>
							<input type="text" placeholder="Например: CBRF, NBU, NBK, СВ" class="input" name="currencies[<?php html($key); ?>][currency]" value="<?php html($currency['currency']); ?>">
						</td>
						<td>
							<input type="text" placeholder="Например: 1, 23.98, CBRF, NBU, CB" class="input" name="currencies[<?php html($key); ?>][rate]" value="<?php html($currency['rate']); ?>">
						</td>
						<td>
							<a onClick="deleteCurrencyFields(this)" class="delete">Удалить</a>
						</td>
					</tr>
				<?php } ?>
			<?php } else { ?>
				<tr>
					<td>
						<input type="text" placeholder="Например: RUR, USD, EUR, UAH, KZT" class="input" name="currencies[1][currency]">
					</td>
					<td>
						<input type="text" placeholder="Например: 1, 23.98, CBRF, NBU, CB" class="input" name="currencies[1][rate]">
					</td>
					<td>
						<a onClick="deleteCurrencyFields(this)" class="delete">Удалить</a>
					</td>
				</tr>
			<?php } ?>
		</tbody>
		<tfoot id="add_dataset_fields_btn">
			<tr>
				<td colspan="3">
					<a class="add" onClick="addCurrencyFields()">Добавить валюту</a>
				</td>
			</tr>
		</tfoot>
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