<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addJSFromContext($this->getJavascriptFileName('jquery-chosen'));
	$this->addCSSFromContext($this->getTemplateStylesFileName('jquery-chosen'));
	$this->addBreadcrumb('Товары', $this->href_to('goods'));
	$this->addBreadcrumb('Импорт товаров');
	$this->setPageTitle('Импорт товаров');
	
	$default_fields = array(
		'' => 'Не импортировать',
		'id' => 'ID',
		'sc_props' => '[Обработчик свойств]'
	);
	$fields_list = ($default_fields + array_collection_to_list($fields, 'name', 'title'));
	$fields_list = ($fields_list + $columns);
	
	$artikul_names = array('Product code', 'artikul', 'Артикул', 'Код', 'Артикул / код');
	$category_names = array('Category', 'category', 'category_id', 'Категория');
	$props_names = array('Features', 'features', 'props', 'sc_props', 'Характеристики');
	$price_names = array('Price', 'price', 'Цена', 'цена');
	$photo_names = array('Detailed image', 'Image URL', 'Фото', 'Фотографии', 'photo', 'Изображение');
	$title_names = array('Product name', 'title', 'Заголовок', 'Название', 'Наименование');
	$content_names = array('Description', 'content', 'Описание');
	$keys_names = array('Meta keywords', 'keys', 'seo_keys', 'Ключевые слова');
	$desc_names = array('Meta description', 'desc', 'seo_desc', 'SEO описание');
	$seo_title_names = array('Page title', 'seo_title');
	$tags_title_names = array('Search words', 'tags', 'Теги');
	
?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<div class="sc_admin_import">
			<div class="f1-steps" style="width:60%;margin:20px auto">
				<div class="f1-progress">
					<div class="f1-progress-line" style="width: 38%;"></div>
				</div>
				<a href="<?php echo $this->href_to('import', 1); ?>" class="f1-step activated">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-download-alt"></i></div>
					<p>Загрузить файл</p>
				</a>
				<div class="f1-step active">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-cog"></i></div>
					<p>Назначение столбцов</p>
				</div>
				<div class="f1-step">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-duplicate"></i></div>
					<p>Импорт</p>
				</div>
				<div class="f1-step">
					<div class="f1-step-icon"><i class="glyphicon glyphicon-ok-sign"></i></div>
					<p>Готово</p>
				</div>
			</div>
			<table style="width:60%;margin:20px auto 190px">
				<tr>
					<td colspan="2" style="padding:15px 10px;text-align: center;">Выберите поля в CSV файле для назначения полям товара, или для игнорирования их при импорте.</td>
				</tr>
				<tr>
					<th>
						Имя столбца
						<p>Если имя столбца ровно</p>
					</th>
					<th>
						Назначить полю
						<p>Сохранить значение этого столбца в поле</p>
					</th>
				</tr>
				<?php if ($item && $rows){ ?>
					<?php foreach($item as $key => $value){ ?>
						<?php 
							if (empty($rows[$key])){ continue; }
							$select = false;
							if (in_array($rows[$key], $fields_list)){
								if (false !== $rus = array_search($rows[$key], $fields_list)) {
									$select = $rus;
								} else {
									$select = $rows[$key];
								}
							} else if(!empty($fields_list[$rows[$key]])){
								$select = $rows[$key];
							}
							if (!$select){
								if (in_array($rows[$key], $artikul_names)){
									$select = 'artikul';
								} else if (in_array($rows[$key], $category_names)){
									$select = 'category_id';
								} else if (in_array($rows[$key], $price_names)){
									$select = 'price';
								} else if (in_array($rows[$key], $photo_names)){
									$select = 'photo';
								} else if (in_array($rows[$key], $title_names)){
									$select = 'title';
								} else if (in_array($rows[$key], $content_names)){
									$select = 'content';
								} else if (in_array($rows[$key], $keys_names)){
									$select = 'seo_keys';
								} else if (in_array($rows[$key], $desc_names)){
									$select = 'seo_desc';
								} else if (in_array($rows[$key], $seo_title_names)){
									$select = 'seo_title';
								} else if (in_array($rows[$key], $tags_title_names)){
									$select = 'tags';
								} else if (in_array($rows[$key], $props_names)){
									$select = 'sc_props';
								}
							}
						?>
						<tr>
							<td style="width:50%">
								<p><b>Имя столбца:</b> <?php html($rows[$key]); ?></p>
								<p><b>Пример значения:</b> 
								<?php
									$warring = '<i style="color:red" class="glyphicon glyphicon-warning-sign" title="Возможно тут есть ошибки"></i> ';
									if ($select == 'category_id'){
										$cat_sep = !empty($data['cat_sep']) ? $data['cat_sep'] : "///";
										if(preg_match("#{$cat_sep}#", $value)){
											$path = preg_split("#{$cat_sep}#", $value);
											if ($path){
												$c_html = '<ul class="sc_i_cat">';
												foreach ($path as $c_key => $c_val){
													$c_html .= '<li>' . $c_val . '</li>';
												}
												$c_html .= '</ul>';
												if (count($path) > 3){
													$c_html .= '<a onClick="$(\'.sc_i_cat li:nth-child(n+4)\').toggle()">Показать все</a>';
												}
												echo $c_html;
											} else {
												echo $warring . string_short($value, 180, '...');
											}
										} else {
											echo $warring . string_short($value, 180, '...');
										}
									} else if ($select == 'photo'){
										$img_sep = !empty($data['img_sep']) ? $data['img_sep'] : ",";
										if(preg_match("#{$img_sep}#", $value)){
											$f_path = preg_split("#{$img_sep}#", $value);
											if ($f_path){
												$f_html = '<ul class="sc_i_photo">';
												foreach ($f_path as $f_key => $f_val){
													$f_html .= '<li>' . $f_val . '</li>';
												}
												$f_html .= '</ul>';
												if (count($f_path) > 3){
													$f_html .= '<a onClick="$(\'.sc_i_photo li:nth-child(n+4)\').toggle()">Показать все</a>';
												}
												echo $f_html;
											} else {
												echo $warring . string_short($value, 180, '...');
											}
										} else {
											echo $value ? $warring . string_short($value, 180, '...') : '';
										}
									} else if ($select == 'sc_props'){
										$prop_sep = !empty($data['props_sep']) ? $data['props_sep'] : "///";
										if(preg_match("#{$prop_sep}#", $value)){
											$props = preg_split("#{$prop_sep}#", $value);
											if ($props){
												$p_html = '<ul class="sc_i_prop">';
												foreach ($props as $p_key => $p_val){
													if (!$p_val){ continue; }
													if (mb_stripos($p_val, ':') !== FALSE){
														$str = explode(':', $p_val);
														if (!empty($str[0])){
															$str[0] = ltrim(preg_replace("/\([^)]+\)\s/", "", $str[0]));
														}
														if (!empty($str[1])){
															$str[1] = ltrim($str[1]);
														}
														if (!empty($str[0]) && !empty($str[1])){
															$p_html .= '<li>' . $str[0] . ': ' . $str[1] . '</li>';
														}
													}
												}
												$p_html .= '</ul>';
												if (count($props) > 3){
													$p_html .= '<a onClick="$(\'.sc_i_prop li:nth-child(n+4)\').toggle()">Показать все</a>';
												}
												echo $p_html;
											} else {
												echo $warring . string_short($value, 180, '...');
											}
										} else {
											echo $warring . string_short($value, 180, '...');
										}
									} else if(!empty($field_colors[$select]) && !empty($value)){
										$color_sep = "///";
										if(preg_match("#{$color_sep}#", $value)){
											$cl_path = preg_split("#{$color_sep}#", $value);
											if ($cl_path){
												$cl_html = '<ul class="sc_i_color">';
												foreach ($cl_path as $cl_key => $cl_val){
													$cl_html .= '<li>' . $cl_val . '</li>';
												}
												$cl_html .= '</ul>';
												if (count($cl_path) > 3){
													$cl_html .= '<a onClick="$(\'.sc_i_color li:nth-child(n+4)\').toggle()">Показать все</a>';
												}
												echo $cl_html;
											} else {
												echo $warring . string_short($value, 180, '...');
											}
										} else {
											echo $warring . string_short($value, 180, '...');
										}
									} else if(!empty($field_volume[$select]) && !empty($value)){
										$vol_sep = "///";
										if(preg_match("#{$vol_sep}#", $value)){
											$vl_path = preg_split("#{$vol_sep}#", $value);
											if ($vl_path){
												$vl_html = '<ul class="sc_i_vol">';
												foreach ($vl_path as $vl_key => $vl_val){
													$vl_html .= '<li>' . $vl_val . '</li>';
												}
												$vl_html .= '</ul>';
												if (count($vl_path) > 3){
													$vl_html .= '<a onClick="$(\'.sc_i_vol li:nth-child(n+4)\').toggle()">Показать все</a>';
												}
												echo $vl_html;
											} else {
												echo $warring . string_short($value, 180, '...');
											}
										} else {
											echo $warring . string_short($value, 180, '...');
										}
									} else {
										echo string_short($value, 180, '...');
									}
								?></p>
							</td>
							<td style="width:30%" class="sc_import_fields">
								<?php echo html_select(
									$rows[$key],
									$fields_list,
									$select,
									array(
										'id' => $rows[$key],
										'data-import' => $rows[$key],
									)); ?>
							</td>
						</tr>
					<?php } ?>
					<tr>
						<td colspan="2" class="sc_importbtn_box">
							<a href="javascript:void(0);" onClick="scImportData(this)">Импортировать</a>
						</td>
					</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</div>
<style>
.sc_importbtn_box{text-align: center;}
.sc_importbtn_box a{
	background: tomato;
    color: #fff;
    display: block;
    padding: 7px;
}
table, th, td {
  border: 1px solid #ccc;
  border-collapse: collapse;
  padding: 5px 10px;
}
th{text-align: center;}
th > p{font-size: 12px;color: #444;margin: 0}
td > p{margin-bottom: 5px}
.f1-steps{overflow:hidden;position:relative;margin-top:20px}.f1-progress,.f1-progress-line{position:absolute;left:0;height:1px}.f1-progress{top:24px;width:100%;background:#ddd}.f1-progress-line{top:0;background:#337ab7}.f1-step{position:relative;float:left;width:25%;padding:0 5px;text-align:center}.f1-step-icon{display:inline-block;width:40px;height:40px;margin-top:4px;background:#ddd;font-size:16px;color:#fff;line-height:44px;-moz-border-radius:50%;-webkit-border-radius:50%;border-radius:50%;text-align:center}.f1-step.activated .f1-step-icon{background:#fff;border:1px solid #337ab7;color:#337ab7;line-height:38px}.f1-step.active .f1-step-icon{width:48px;height:48px;margin-top:0;background:#337ab7;font-size:22px;line-height:50px}.f1-step p{color:#ccc}.f1-step.activated p,.f1-step.active p{color:#337ab7}
.sc_importbtn_box .sc_import_pross{background: #f3f3f3;color: #444;letter-spacing: 3px;}
.sc_i_prop li:nth-child(n+4), .sc_i_photo li:nth-child(n+4), .sc_i_cat li:nth-child(n+4){display:none}
.sc_i_prop, .sc_i_photo, .sc_i_cat{padding: 0;
    margin: 0;
    padding-left: 15px;
    font-size: 13px;}
</style>
<script type="text/javascript">
	
	var send_data = {};
	
	$(document).ready(function() {
		
		getSendData();
		
	});
	function getSendData(){
		$(".sc_import_fields select").each(function() {
			if ($(this).val()){
				send_data[$(this).data('import')] = $(this).val();
			} else {
				delete send_data[$(this).data('import')]; 
			}
		});
	}

    $('.sc_import_fields select').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo LANG_SELECT; ?>', placeholder_text_multiple: '<?php echo LANG_SELECT_MULTIPLE; ?>', disable_search_threshold: 8, width: '100%', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>', search_contains: true, hide_results_on_select: false}).change(function(){
        getSendData();
    });

	function scImportData(btn){
		if ($(btn).hasClass('sc_import_pross')){ return; }
		if (Object.keys(send_data).length){
			$(btn).addClass('sc_import_pross').html('<img src="/templates/default/images/loader24.gif" /> Подождите...');
			$('.sc_import_fields select').prop('disabled', true).trigger("chosen:updated");
			$.post('<?php echo $this->href_to('import_data'); ?>', {send_data : send_data}, function(result){
				if(result.error){
					icms.modal.alert(result.message, 'ui_error');
				} else {
					if (result.added || result.updated){
						$(btn).text('Импортировать остальных').removeClass('sc_import_pross');
					} else {
						$(btn).text('Импортировать').removeClass('sc_import_pross');
					}
					icms.modal.alert('Добавлено: ' + result.added + ' | Обновлено: ' + result.updated);
				}
				$('.sc_import_fields select').prop('disabled', false).trigger("chosen:updated");
			}, 'json');
		} else {
			icms.modal.alert('Нет полей для импорта', 'ui_error');
		}
	}
	
</script>