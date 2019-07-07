<?php
	$bookmarks_css = $tpl->addCSS($tpl->getTplFilePath('controllers/bookmarks/css/style.css', false));
	$url = href_to('bookmarks', 'add', array($this->name, $this->item['ctype_name']));
?>
<a class="sc_fav_btn dsct_top_left" onClick="toggleBookmark(this, <?php html($this->item['id']); ?>)" data-sc-tip="<?php echo $is_added ? 'Удалить из закладки' : 'Добавить в закладки'; ?>">
	<i class="fa <?php echo $is_added ? 'fa-heart' : 'fa-heart-o'; ?>" aria-hidden="true"></i>
</a>
<?php if ($bookmarks_css){ ?>
<script>
	function toggleBookmark(button, id){
		<?php if ($user_id){ ?>
			button = $(button);
			$('.fa', button).removeClass('fa-heart fa-heart-o').addClass('fa-refresh fa-spin');
			$.post('<?php echo $url; ?>', {id : id}, function(result){
				if(result.error){
					icms.modal.alert(result.message);
				} else {
					if (result.message == 'Закладка удалена'){
						$('.fa', button).removeClass('fa-refresh fa-spin fa-heart').addClass('fa-heart-o');
						button.attr('data-sc-tip', 'Добавить в закладки');
					} else {
						$('.fa', button).removeClass('fa-refresh fa-spin fa-heart-o').addClass('fa-heart');
						button.attr('data-sc-tip', 'Удалить из закладки');
					}
					icms.modal.alert(result.message);
				}			
			}, 'json');
		<?php } else { ?>
			icms.modal.openAjax('<?php echo href_to('auth'); ?>', false, false, 'Авторизация');
		<?php } ?>
	}
</script>
<?php } ?>