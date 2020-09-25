<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php echo html_input('text', $field->element_name, $value, array('id' => $field->element_name)); ?>
<button type="button" onClick="scGetCourse(this)">Спарсить текущий курс</button>
<?php ob_start(); ?>
<script>
	var course = 0;
	function scGetCourse(btn){
		
		btn = $(btn);
		
		if (btn.text() == 'Подождите...'){ return; }
		
		btn.text('Подождите...');

		var url = $('input[name="conversion[url]"]');
		var selector = $('input[name="conversion[selector]"]');

		if (!url.val()){ url.css('border', '1px solid red'); return; } else { url.css('border', '1px solid #aaa'); }
		if (!selector.val()){ selector.css('border', '1px solid red'); return; } else { selector.css('border', '1px solid #aaa'); }

		$.post('<?php echo href_to('showcase', 'conversion'); ?>', {url : url.val(), selector : selector.val()}, function(result){
			btn.text('Спарсить текущий курс');
			if(result.error){
				icms.modal.alert(result.message, 'ui_error');
				if (result.url){
					selector.css('border', '1px solid red');
					return;
				}
				if (result.selector){
					selector.css('border', '1px solid red');
					return;
				}
			} else {
				$('input[name="<?php html($field->element_name); ?>"]').val(result.course);
				course = result.course;console.log(course);
				var zprice = Math.round((100 / course) * 100) / 100;console.log(zprice);
			}			
		}, 'json');

	}
</script>
<?php $this->addBottom(ob_get_clean()); ?>