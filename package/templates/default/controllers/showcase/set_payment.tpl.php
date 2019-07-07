<?php if ($systems){ ?>
	<div class="sc_ps_list is_ps_modal">
		<?php foreach ($systems as $system){ ?>
			<div class="sc_ps_item" onclick="set_payment(<?php html($system['id']); ?>)">
				<div class="sc_ps_box">
					<?php echo !$system['id'] ? '<img src="' . $system['icon'] . '" alt="' . $system['title'] . '" />' : html_image($system['icon'], 'original', $system['title']); ?>
					<?php html($system['title']); ?>
				</div>
			</div>
		<?php } ?>
	</div>
	<script>
		function set_payment(id){
			$.post('<?php echo href_to('showcase', 'set_payment', $order['id']); ?>', {id : id}, function(result){
				if(result.error){
					if (result.message){
						icms.modal.alert(result.message, 'ui_error');
					}
				} else {
					$('.sc_order_view .order_fields_td a.order_fields_paid').text(result.title).hide().fadeIn(800);
					icms.modal.alert('Способ оплаты успешно изменена');
				}			
			}, 'json');
		}
	</script>
<?php } else { ?>
	<p>Платежные системы не найдены</p>
<?php } ?>