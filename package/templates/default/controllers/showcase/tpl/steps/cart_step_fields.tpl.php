<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/cart.css', false));
	$this->addBreadcrumb($steps['current']['title']);
	$next = !empty($steps['next']['id']) ? $steps['next']['id'] : 'checkout';
?>
<h1 id="sc_cart_title">Корзина (<?php echo html_spellcount((isset($count) ? $count : 0), 'товар|товара|товаров'); ?>)</h1>
<div class="sc_cart_fields wd_sc_cart sc_style_big">
	<?php if ($cart_fields){ ?>
		<?php foreach ($cart_fields as $cField){ ?>
			<?php 
				$cField['attributes'] = !empty($cField['attributes']) ? string_explode_list($cField['attributes']) : false;
				$cField['options'] = !empty($cField['options']) ? string_explode_list($cField['options']) : false;
			?>
			<div class="sc_cart_field sc_field_<?php html($cField['name']); ?>">
				<?php if (empty($cField['options']['title_off'])){ ?>
					<div class="sc_cField_title">
						<label for="<?php html($cField['name']); ?>"><?php html($cField['title']); ?></label>
						<?php if ($cField['hint']){ ?><span><?php html($cField['hint']); ?></span><?php } ?>
					</div>
				<?php } ?>
				<div class="sc_cField_value">
					<?php 
						echo $this->controller->getFormFields($cField, $values);
					?>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<p>Нет доступных полей</p>
	<?php } ?>
	<br />
	<div class="wd_scl_footer">
		<div class="wd_sclf_summ">
			Итого: <b><?php echo $this->controller->getPriceFormat($summ); ?></b>
		</div>
		<a class="wd_sclf_checkout" rel="nofollow" onClick="scValidateCartForm(this)">Оформить заказ</a>
	</div>
</div>
<script>
	
	var data = {};
	$(document).ready(function() {
		$(".sc_cart_fields .sc_cField_value *[name]").each(function( index, field ) {
			if ($(field).attr('name')){
				data[$(field).attr('name')] = $(field).val();
			}
		});
		$.post('/showcase/save_cart_data', data, false, 'json');
	});
	
	$(".sc_cart_fields .sc_cField_value").on("change blur", "*[name]", function(){
		if ($(".sc_cart_fields .sc_cField_value *[name]").length){
			$(".sc_cart_fields .sc_cField_value *[name]").each(function( index, field ) {
				if ($(field).attr('name')){
					data[$(field).attr('name')] = $(field).val();
				}
			});
			$.post('/showcase/save_cart_data', data, false, 'json');
		}
	});

	function scValidateCartForm(btn){
		btn = $(btn);
		var errors = {};
		if ($(".sc_cart_fields .sc_cField_value *[required]").length){
			$(".sc_cart_fields .sc_cField_value *[required]").each(function() {
				if (!$(this).val()){
					if ($(this).attr('id') == 'agreement'){
						if (!$(this).prop('checked')){
							$('.sc_field_agreement .checkmark').css('background', 'red');
							$('.sc_field_agreement label').css('color', 'red');
							errors[$(this).attr('id')] = 1;
						} else {
							$('.sc_field_agreement .checkmark').css('background', '#2196F3');
							$('.sc_field_agreement label').css('color', '#444');
							delete errors[$(this).attr('id')];
						}
					} else {
						$(this).css('border', '1px solid #f79b94');
						errors[$(this).attr('id')] = 1;
					}
				} else {
					if ($(this).attr('id') == 'email'){
						if (validateEmail($(this).val()) == false){
							$(this).css('border', '1px solid #f79b94');
							errors[$(this).attr('id')] = 1;
						} else {
							$(this).css('border', '1px solid #ccc');
							delete errors[$(this).attr('id')];
						}
					} else {
						$(this).css('border', '1px solid #ccc');
						delete errors[$(this).attr('id')];
					}
				}
			});
		}
		if ($('.sc_ps_list .sc_ps_item input[type="checkbox"]').length){
			var sp_error = true;
			$('.sc_ps_list .sc_ps_item input[type="checkbox"]').each(function() {
				if ($(this).is(':checked')){
					sp_error = false;
				}
			});
			if (sp_error){
				$('.sc_ps_list .sc_ps_item').css('border', '1px solid red');
				errors['payment_system'] = 1;
			} else {
				$('.sc_ps_list .sc_ps_item').css('border', 'none');
				delete errors['payment_system'];
			}
		}
		if (Object.keys(errors).length){
			return;
		} else {
			window.location.href = '<?php echo href_to('showcase', 'cart', array($next)); ?>', true;
		}
	}

	function validateEmail(email) {
		var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(String(email).toLowerCase());
	}
	
	if ($('.sc_ps_list .sc_ps_item input[type="checkbox"]').length){
		$('.sc_ps_list .sc_ps_item input[type="checkbox"]').on('change', function() {
			$('.sc_ps_list .sc_ps_item input[type="checkbox"]').not(this).prop('checked', false);
			if ($(this).val()){
				data['payment_system'] = $(this).val();
				data['paid'] = 1;
				$(".sc_cart_fields .sc_cField_value *[name]").each(function( index, field ) {
					if ($(field).attr('name')){
						data[$(field).attr('name')] = $(field).val();
					}
				});
				$.post('/showcase/save_cart_data', data, false, 'json');
			}
		});
	}
	
</script>