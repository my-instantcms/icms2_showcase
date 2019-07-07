<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php

	$tpl->addCSS($tpl->getTplFilePath('controllers/showcase/css/fields.css', false));
	$tpl->addJS($tpl->getTplFilePath('controllers/showcase/js/ddsort.min.js', false));

	$id = !empty($field->item['id']) ? $field->item['id'] : 0;
	
?>
<div class="sc_field_variations">
	<?php if ($value && $variations && $fields){ ?>
		<ul id="sc_field_variations_box">
			<?php foreach ($variations as $index => $variation){ ?>
				<li id="sc_variant_<?php html($index); ?>" data-id="<?php html($index); ?>">
					<div class="sfv_photo">
						<?php if ($variation['photo']){ ?>
							<?php echo html_image($variation['photo'], 'small', $variation['title']); ?>
						<?php } else { ?>
							<img src="/templates\default/controllers/showcase/img/nophoto.png" />
						<?php } ?>
					</div>
					<div class="sfv_title">
						<b><?php html($variation['title']); ?></b> 
						<a href="<?php echo href_to('showcase', 'form_variations', array($id, $index)); ?>" class="ajax-modal" data-sc-tip="<?php html(LANG_EDIT); ?>"><i class="fa fa-pencil-square-o"></i></a>
						<a class="sfv_trash" data-sc-tip="<?php html(LANG_DELETE); ?>" onclick="sc_deleteVariation(this, <?php html($index); ?>)"><i class="fa fa-trash"></i></a>
					</div>
					<div class="sfv_meta">
						<?php foreach ($fields as $name => $field){ ?>
							<?php if (!empty($variation[$name]) && $field['type'] != 'scprice'){ ?>
								<?php 
									$vals = $field['handler']->getListItems(1);
									echo $field['title'] . ': ' . (!empty($vals[$variation[$name]]) ? $vals[$variation[$name]] : ''); ?>, 
							<?php } ?>
						<?php } ?>
						Цена: <?php echo $showcase->getPriceFormat($variation['price']); ?>, 
						В наличии: <?php html($variation['in']); ?>
						<input type="hidden" name="variants[<?php html($index); ?>]" value="<?php html($index); ?>" />
					</div>
				</li>
			<?php } ?>
		</ul>
	<?php } else { ?>
	<p>Нет добавленных вариантов</p>
	<?php } ?>
</div>
<a href="<?php echo href_to('showcase', 'form_variations', array($id)); ?>" class="sc_add_variations ajax-modal" title="Добавить вариант">Добавить вариант товара</a>
<script type="text/javascript">
	$('#sc_field_variations_box').DDSort({
		target: 'li',
		floatStyle: {    
			'border': '1px solid #ccc',
			'background-color': '#fff'
		},
		up: function(){
			var items = {};
			$('#sc_field_variations_box li').each(function(index){
				items[index] = $(this).data('id');
			});
			if (items){
				$.post('<?php echo href_to('admin', 'controllers', array('edit', 'showcase', 'item_reorder', 'sc_variations')); ?>', {items : items}, function(){}, 'json');
			}
		},
	});
	function sc_deleteVariation(btn, id){
		if (!confirm("Вы уверены что хотите удалить вариант?")) {return false;}
		btn = $(btn);
		if (btn.text() == '<i class="fa fa-refresh fa-spin"></i>'){ return; }
		btn.html('<i class="fa fa-refresh fa-spin"></i>');
		$.post('<?php echo href_to('showcase', 'form_variations', array($id)); ?>/0/' + id, false, function(result){
			if(result.error){
				icms.modal.alert(result.error, 'ui_error');
			} else {
				$('#sc_field_variations_box li#sc_variant_' + id).remove();
				icms.modal.alert('Вариант успешно удален');
			}
		}, 'json');
	}
</script>