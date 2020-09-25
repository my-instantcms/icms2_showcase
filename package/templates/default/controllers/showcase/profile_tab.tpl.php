<?php 
	if ($is_page == 'profile'){
		$this->addBreadcrumb(LANG_USERS, href_to('users'));
		$this->addBreadcrumb($profile['nickname'], href_to('users', $profile['id']));
	}
    $this->addBreadcrumb(($is_page == 'profile') ? 'Мои заказы' : 'Заказы');
    $this->setPageTitle(($is_page == 'profile') ? 'Мои заказы' : 'Заказы');
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/tab.css', false));
?>

<?php echo html_select('type', $status, $type, array('onchange' => 'scSetType(this)')); ?>

<div class="sc_order_lists"></div>
<?php ob_start(); ?>
<script type="text/javascript">
	$(function () {
		$.post('<?php echo href_to('showcase', 'tabs_data', array($profile['id'], $type)); ?>', false, function(result){
			if(result.error){
				alert('Ошибка данных');
			} else {
				insertParam('type', '<?php html($type); ?>');
				$('.sc_order_lists').html(result.html);
			}			
		}, 'json');
	});
	
	function scSetType(sort){
		if ($(sort).val()){
			$.post('<?php echo href_to('showcase', 'tabs_data', array($profile['id'])); ?>/' + $(sort).val(), false, function(result){
				if(result.error){
					alert('Ошибка данных');
				} else {
					insertParam('type', $(sort).val());
					$('.sc_order_lists').html(result.html);
				}			
			}, 'json');
		}
	}

	function tabMore(button, page, perpage, type, is_more){
		$('img', $(button)).attr('src', '/templates/default/controllers/showcase/img/ajax-loader.gif');
		$.post('<?php echo href_to('showcase', 'tabs_data', array($profile['id'])); ?>/' + type + '/' + is_more, {page : page, perpage: perpage}, function(r){
			if(r.error){
				alert('Ошибка данных');
			} else {
				if (r.html == '<p><?php html(LANG_LIST_EMPTY); ?></p>'){
					$('.sc_order_lists .tab_btn_more').remove();
					insertParam('type', type);
				} else {
					$(button).remove();
					insertParam('type', type);
					$('.sc_order_lists').append(r.html);
				}
			}			
		}, 'json');
	}
	
	function insertParam(key, value){
		<?php if ($is_page == 'orders'){ ?>
			var url = document.location.href;
			url = url.replace(/\/[^\/]*$/, '/' + value);
			window.history.replaceState(null, null, url);
		<?php } ?>
	}
	
	<?php if ($is_page == 'orders'){ ?>
		function scSetStatus(btn, order_id){
			var status_id = $(btn).val();
			if (status_id){
				$.post('<?php echo href_to('showcase', 'set_order_status'); ?>/' + order_id, {status_id : status_id}, function(result){
					if(result.error){
						icms.modal.alert(result.message);
					} else {
						$('.sc_ol_item#item_' + order_id).remove();
						icms.modal.alert('Успешно сохранено');
					}
				}, 'json');
			}
		}
	<?php } ?>

</script>
<?php $this->addBottom(ob_get_clean()); ?>