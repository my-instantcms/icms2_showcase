<?php 
	$value = !empty($values[$field['name']]) ? $values[$field['name']] : '';
	$systems = !empty($field['systems']) ? $field['systems'] : false;
?>
<?php if ($systems){ ?>
	<div class="sc_ps_list">
		<?php foreach ($systems as $system){ ?>
			<div class="sc_ps_item">
				<input type="checkbox" value="<?php html($system['id']); ?>" />
				<div class="sc_ps_box">
					<?php echo !$system['id'] ? '<img src="' . $system['icon'] . '" alt="' . $system['title'] . '" />' : html_image($system['icon'], 'original', $system['title']); ?>
					<?php html($system['title']); ?>
				</div>
			</div>
		<?php } ?>
	</div>
<?php } else { ?>
	<p>Платежные системы не найдены</p>
<?php } ?>