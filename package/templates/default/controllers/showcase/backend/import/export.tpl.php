<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/libs/multi/multi.min.css', false), false);
	$this->addJS($this->getTplFilePath('controllers/showcase/libs/multi/multi.min.js', false), false);
	$this->addJSFromContext($this->getJavascriptFileName('jquery-chosen'));
	$this->addCSSFromContext($this->getTemplateStylesFileName('jquery-chosen'));
	$this->addBreadcrumb('Товары', $this->href_to('goods'));
	$this->addBreadcrumb('Экспорт товаров');
	$this->setPageTitle('Экспорт товаров');

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<div class="sc_admin_export">
		
			<div class="sc_export_selectbox">
				
				<fieldset>
					<legend>Настройки экспорта</legend>
					<p><b><?php html(LANG_CATEGORY); ?>:</b> <?php echo html_select('cat_id', $cats_list, false, array('id' => 'cat_id'));?></p>
					<p><b>Разделитель столбцов:</b> <?php echo html_input('text', 'sep', ';', array('id' => 'sep'));?></p>
					<p><b>Разделитель категории:</b> <?php echo html_input('text', 'cat_sep', '///', array('id' => 'cat_sep'));?></p>
					<p><b>Разделитель фотографии:</b> <?php echo html_input('text', 'img_sep', ',', array('id' => 'img_sep'));?></p>
					<p><b>Разделитель свойств:</b> <?php echo html_input('text', 'props_sep', '///', array('id' => 'props_sep'));?></p>
				</fieldset>
				<?php echo html_select('export_select', $fields_list, array('category_id', 'title', 'content', 'price'), array('id' => 'export_select', 'multiple' => 'multiple'));?>
				<a href="javascript:void(0);" class="sc_export_btns" onclick="scBulidExport(this)">Экспортировать</a>
				
			</div>
			
		</div>
	</div>
</div>
<?php ob_start(); ?>
<script>
	var send_data = {};
	$(document).ready(function() {
		getSendData();
		$('#export_select').multi({
			'enable_search': true,
			'search_placeholder': 'Найти поле...',
			'non_selected_header': 'Все поля',
			'selected_header': 'Поля для экспорта',
			'limit': -1,
		});
	});
	$('#sep, #cat_sep, #img_sep, #props_sep, #export_select').change(function(){
        getSendData();
    });
	function getSendData(){
		if($('#cat_id').val()){send_data['cat_id']=$('#cat_id').val();}else{delete send_data['cat_id'];}
		if($('#sep').val()){send_data['sep']=$('#sep').val();}else{delete send_data['sep'];}
		if($('#cat_sep').val()){send_data['cat_sep']=$('#cat_sep').val();}else{delete send_data['cat_sep'];}
		if($('#img_sep').val()){send_data['img_sep']=$('#img_sep').val();}else{delete send_data['img_sep'];}
		if($('#props_sep').val()){send_data['props_sep']=$('#props_sep').val();}else{delete send_data['props_sep'];}
		if($('#export_select').val()){send_data['export_select']=$('#export_select').val();}else{delete send_data['export_select'];}
	}
	$('#cat_id').chosen({no_results_text: '<?php echo LANG_LIST_EMPTY; ?>', placeholder_text_single: '<?php echo LANG_SELECT; ?>', placeholder_text_multiple: '<?php echo LANG_SELECT_MULTIPLE; ?>', disable_search_threshold: 8, width: '100%', allow_single_deselect: true, search_placeholder: '<?php echo LANG_BEGIN_TYPING; ?>', search_contains: true, hide_results_on_select: false}).change(function(){
        getSendData();
    });
	function scBulidExport(btn){
		if ($(btn).hasClass('sc_export_pross')){ return; }
		if (Object.keys(send_data).length && send_data.export_select.length){
			$(btn).addClass('sc_export_pross').html('<img src="/templates/default/images/loader24.gif" /> Подождите...');
			$('.multi-wrapper').append('<div class="sc_export_loader"></div>');
			$('#cat_id, #sep, #cat_sep, #img_sep, #props_sep, #export_select').prop('disabled', true).trigger("chosen:updated");
			$.post('<?php echo $this->href_to('export_data'); ?>', {send_data : send_data}, function(result){
				if(result.error){
					icms.modal.alert(result.message, 'ui_error');
				} else {
					$(btn).text('Экспортировать').removeClass('sc_export_pross');
					if ($('.sc_export_selectbox .is_dwl_btn').length){
						$('.sc_export_selectbox .is_dwl_btn').attr('href', result.href);
					} else {
						$('.sc_export_selectbox').append('<a href="' + result.href + '" class="sc_export_btns is_dwl_btn">Скачать</a>');
					}
					$('.multi-wrapper .sc_export_loader').remove();
				}
				$('#cat_id, #sep, #cat_sep, #img_sep, #props_sep, #export_select').prop('disabled', false).trigger("chosen:updated");
			}, 'json');
		} else {
			icms.modal.alert('Нет полей для экспорта', 'ui_error');
		}
	}
</script>
<?php $this->addBottom(ob_get_clean()); ?>
<style>
	.sc_export_selectbox{width:600px;margin:auto}
	.sc_export_selectbox fieldset{border: 1px solid #ddd;padding: 10px;margin-bottom: 10px;}
	.sc_export_selectbox fieldset legend{display: inline-block;
    width: auto;
    border: 1px solid #ccc;
    padding: 0 7px;
    font-size: 16px;
    color: #555;
    margin: 0;}
	.multi-wrapper .item[data-value="sc_props"]{color: red;font-weight: bold;}
	#cat_id,#cat_id_chosen{width:calc(100% - 80px) !important}
	.sc_export_btns{    display: inline-block;
    background: darkcyan;
    color: #fff;
    padding: 7px 10px;
    margin: 5px 5px 0 0;
    border-radius: 2px;}
	.sc_export_btns:hover,.sc_export_btns:focus{background:#04a7a7;color: #fff;}
	.sc_export_btns.sc_export_pross{background:#eee;color: #444;}
	.sc_export_btns.is_dwl_btn{background:darkorange}
	.sc_export_loader{    position: absolute;
    background: rgba(255, 255, 255, 0.4);
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;}
	.multi-wrapper{position: relative;}
</style>