<?php

	$this->addCSS($this->getTplFilePath('controllers/showcase/css/fields.css', false));
	$this->addJSFromContext( $this->getJavascriptFileName('fileuploader') );
	$this->addJSFromContext( $this->getJavascriptFileName('images-upload') );
    $this->addCSSFromContext( $this->getStylesFileName('images') );
	$paths = !empty($variations['photo']) ? (is_array($variations['photo']) ? $variations['photo'] : cmsModel::yamlToArray($variations['photo'])) : false;
	$item['photo'] = !empty($item['photo']) ? cmsModel::yamlToArray($item['photo']) : false;
	$upload = $this->controller->cms_config->upload_host . '/';
?>
<div class="sc_fields_variations">
	<div class="sfv_field_box sfv_title">
		<b>Заголовок:</b> <input id="sfv_title" type="text" class="sfv_field" value="<?php echo !empty($variations['title']) ? $variations['title'] : $item['title']; ?>">
	</div>
	<div class="sfv_field_box sfv_photo">
		<b>Фото:</b> 
		<div id="widget_image_photo" class="widget_image_single">
			<div class="data" style="display:none">
				<?php if (!empty($paths)) { ?>
					<?php foreach($paths as $type=>$path){ ?>
						<?php echo html_input('hidden', "photo[{$type}]", $path); ?>
					<?php } ?>
				<?php } ?>
			</div>
			<div class="preview block" <?php if (empty($paths)) { ?>style="display:none"<?php } ?>>
				<img src="<?php if (!empty($paths)) { echo cmsConfig::get('upload_host') . '/' . reset($paths); } ?>" />
				<a href="javascript:" onclick="icms.images.remove('photo')"><?php echo LANG_DELETE; ?></a>
			</div>
			<div class="upload block" <?php if (!empty($paths)) { ?>style="display:none"<?php } ?>>
				<div id="file-uploader-photo"></div>
			</div>
			<div class="loading block" style="display:none">
				<?php echo LANG_LOADING; ?>
			</div>
		</div>

		<?php if ($item['photo']){ ?>
			<div class="sfv_photo_select" onclick="$('.sfv_photo .sfv_photo_list').toggle()">Связать</div>
			<div class="sfv_photo_list">
				<?php foreach ($item['photo'] as $image){ ?>
					<img src="<?php html($upload . $image['small']); ?>" onclick="sfvPhotoSet('<?php html($image['small']); ?>', '<?php html($image['big']); ?>')" />
				<?php } ?>
			</div>
		<?php } ?>
	</div>
	<?php if ($fields){ ?>
		<?php foreach ($fields as $name => $field){ ?>
			<?php if ($field['type'] == 'scvariations' || $field['type'] == 'scprice'){ continue; } ?>
			<div class="sfv_field_box sfv_<?php html($name); ?>">
				<b><?php html($field['title']); ?>:</b> <?php echo html_select('sfv_' . $name, array('' => '') + $field['handler']->getListItems(1), (!empty($variations[$name]) ? $variations[$name] : false), array('id' => 'sfv_' . $name, 'data-name' => $name)); ?> 
			</div>
		<?php } ?>
	<?php } ?>
	<div class="sfv_field_box sfv_price">
		<b>Цена:</b> <input id="sfv_price" type="number" class="sfv_field" value="<?php echo !empty($variations['price']) ? $variations['price'] : $item['price']; ?>"> <span><?php echo !empty($this->controller->options['cerrency']) ? $this->controller->options['cerrency'] : LANG_CURRENCY; ?></span>
	</div>
	<div class="sfv_field_box sfv_sale">
		<b>Скидка:</b> <input id="sfv_sale" type="number" class="sfv_field" placeholder="Цена со скидкой" value="<?php echo !empty($variations['sale']) ? $variations['sale'] : $item['sale']; ?>"> <span><?php echo !empty($this->controller->options['cerrency']) ? $this->controller->options['cerrency'] : LANG_CURRENCY; ?></span>
	</div>
	<div class="sfv_field_box sfv_in">
		<b>В наличии:</b> <input id="sfv_in" type="number" class="sfv_field" value="<?php echo !empty($variations['in']) ? $variations['in'] : (!empty($item['in_stock']) ? $item['in_stock'] : ''); ?>">
	</div>
</div>
<div onclick="addVariationToItem(this)" class="sc_add_variation_to_item"><?php echo $id ? LANG_SAVE : LANG_ADD; ?> вариант</div>
<script type="text/javascript">
	
	$(document).ready(function() {
		$('.sfv_photo .widget_image_single .preview a').html('<i class="fa fa-trash"></i>');
		<?php if (!empty($variations['attached'])){ ?>
			$('.sfv_photo .widget_image_single .data').append('<input type="hidden" class="input" name="photo[attached]" value="1">');
		<?php } ?>
		icms.images.uploadCallback = function(field_name, result){
			icms.modal.resize();
		};
		
		var uploader = new qq.FileUploader({
            element: document.getElementById('file-uploader-photo'),
            action: '<?php echo href_to('images', 'upload', 'photo') . '?sizes=small,big'; ?>',
            multiple: false,
            debug: false,
            showMessage: function(message){
                icms.modal.alert(message);
            },
			template: '<div class="qq-uploader">' + 
                '<div class="qq-upload-drop-area"><span></span></div>' +
                '<div class="qq-upload-button">Загрузить</div>' +
                '<ul class="qq-upload-list"></ul>' + 
             '</div>',
            onSubmit: function(id, fileName){
                var ftitle = $('#title').val();
                if(ftitle){
                    this.params = {
                        file_name: $('#title').val()+' photo'
                    };
                }
                icms.images._onSubmit('photo');
            },

            onComplete: function(id, file_name, result){
                icms.images._onComplete('photo', result);
            }

        });
		
	});

	function sfvPhotoSet(small, big){
		if (small && big){
			var html = '<input type="hidden" class="input" name="photo[small]" value="' + small + '">';
			html += '<input type="hidden" class="input" name="photo[big]" value="' + big + '">';
			html += '<input type="hidden" class="input" name="photo[attached]" value="1">';
			$('.sfv_photo .widget_image_single > .data').html(html);
			$('.sfv_photo .widget_image_single .upload').hide();
			$('.sfv_photo .widget_image_single .preview img').attr('src', '<?php html($upload); ?>' + small);
			$('.sfv_photo .widget_image_single .preview').show();
			icms.modal.resize();
		}
		$('.sfv_photo .sfv_photo_list').hide();
	}

	function addVariationToItem(btn, action){
		btn = $(btn);
		if (btn.text() == 'Подождите...'){ return; }
		btn.text('Подождите...');
		var title = $('#sfv_title').val();
		var photo_big = $('.sfv_photo .widget_image_single .data input[name="photo[big]"]').val();
		var photo_small = $('.sfv_photo .widget_image_single .data input[name="photo[small]"]').val();
		var attached = $('.sfv_photo .widget_image_single .data input[name="photo[attached]"]').length ? 1 : 0;
		var price = $('#sfv_price').val();
		var sale = $('#sfv_sale').val();
		var in_stock = $('#sfv_in').val();
		if (!title || !price || !in_stock){
			if (!title){
				$('#sfv_title').css('border-color', 'red');
			} else {
				$('#sfv_title').css('border-color', '#aaa');
			}
			if (!price){
				$('#sfv_price').css('border-color', 'red');
			} else {
				$('#sfv_price').css('border-color', '#aaa');
			}
			if (!in_stock){
				$('#sfv_in').css('border-color', 'red');
			} else {
				$('#sfv_in').css('border-color', '#aaa');
			}
			btn.text('<?php echo $id ? LANG_SAVE : LANG_ADD; ?> вариант');
			return;
		}
		var data = {
			"title" : title,
			"photo_big" : photo_big,
			"photo_small" : photo_small,
			"attached" : attached,
			"price" : price,
			"sale" : sale,
			"in" : in_stock
		};
		if ($('.sfv_field_box select')){
			$('.sfv_field_box select').each(function() {
				if ($(this).data('name') && $(this).val()){
					data[$(this).data('name')] = $(this).val();
				}
			});
		}
		$.post('<?php echo $this->href_to('save_variations', array($item_id, $id)); ?>', data, function(result){
			if(result.error){
				icms.modal.alert(result.error, 'ui_error');
			} else {
				if (result.do == 'edit'){
					$('#sc_field_variations_box li#sc_variant_<?php html($id); ?>').html(result.html).hide().fadeIn(1000);
					icms.modal.close();
				} else {
					if ($('#sc_field_variations_box').length){
						$('#sc_field_variations_box').append(result.html);
					} else {
						$('.sc_field_variations').html('<ul id="sc_field_variations_box"></ul>');
						$('#sc_field_variations_box').append(result.html);
					}
					icms.modal.alert('Вариант успешно добавлен. Не забудте сохранить запись');
				}
				icms.modal.bind('a.ajax-modal');
			}
		}, 'json');
	}
	
</script>