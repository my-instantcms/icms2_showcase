<?php

	$fields = $ctype['sc_fields'];

	if (isset($fields['fav'])){ $fav = 'fav'; } 
	if (isset($fields['revs'])){ $revs = 'revs'; } 
	
	foreach($fields as $field){
		if (!isset($fav) && $field['type'] == 'bookmarks'){ $fav = $field['name']; }
		if (!isset($revs) && $field['type'] == 'recommendstars'){ $revs = $field['name']; }
	}
	
	$showcase = cmsCore::getController('showcase');
	$list_pos = !empty($showcase->options['list_pos']) ? $showcase->options['list_pos'] : 'center';
	$list_height = !empty($showcase->options['list_height']) ? $showcase->options['list_height'] : 200;
	$list_bg = !empty($showcase->options['list_bg']) ? $showcase->options['list_bg'] : '#fff';

?>

<?php if ($items){ ?>

	<?php 
		$this->addCSS($this->getTplFilePath('controllers/showcase/css/list_grid.css', false));
		$this->addJS($this->getTplFilePath('controllers/showcase/js/showcase.js', false));
		$this->addJS($this->getTplFilePath('controllers/showcase/js/imgLiquid.min.js', false));
	?>
	
	<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
	<script src="//yastatic.net/share2/share.js"></script>
	
	<?php ob_start(); ?>
	<script>
		var cart_data = (typeof cart_data != "undefined") ? cart_data : {};
		var preorder_data = (typeof preorder_data != "undefined") ? preorder_data : {};
	</script>
	<?php $this->addBottom(ob_get_clean()); ?>

	<div class="showcase_list_grid <?php echo $ctype['name']; ?>_list_grid" onload="icms.showcase.bulidListGrid(this)">

		<?php foreach($items as $item){ ?>
			<?php
				$item['ctype'] = $ctype;
                $item['ctype_name'] = $ctype['name'];
				$url = href_to($ctype['name'], $item['slug']) . '.html';
				$image = '/' . $this->getTplFilePath('controllers/showcase/img/nophoto_normal.png', false);
				$photo_class = false;
				if(isset($fields['photo']) && $fields['photo']['is_in_list'] && !empty($item['photo'])) {
					$image = html_image_src($item['photo'], $fields['photo']['options']['size_teaser'], true);
					$photo_class = 'sc_is_photo';
					$photos = is_array($item['photo']) ? $item['photo'] : cmsModel::yamlToArray($item['photo']);
				}
                $is_private = $item['is_private'] && $hide_except_title && !$item['user']['is_friend'];
                if ($is_private) {
                    $image = html_image_src(default_images('private', 'big'), $fields['photo']['options']['size_teaser'], true);
                    $url = 'javascript:void(0);';
                }
			?>			
			<div class="my_default_list_item<?php if (!empty($item['is_vip'])){ ?> is_vip<?php } ?>" id="sc_item_<?php html($item['id']); ?>">
				<div class="miw_preloader">
					<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
				</div>
				<div class="my_item_wrap">
				
					<div class="miw_share_block <?php html($photo_class); ?>" style="height:<?php html($list_height + 20); ?>px">
						<div class="miw_card__social">
							<noindex>
							<strong>Поделиться в социальных сетях</strong>
							<div class="ya-share2" data-services="vkontakte,odnoklassniki,facebook,twitter,moimir,gplus,viber,whatsapp,skype,telegram" data-title="<?php html($item['title']); ?>" data-url="<?php html($this->site_config->host . $url); ?>" data-image="<?php html($this->site_config->host . $image); ?>" data-description="<?php html(mb_strimwidth(strip_tags($item['content']), 0, 60, "...")); ?>" data-title:twitter="<?php html(mb_strimwidth(strip_tags($item['title']), 0, 60, "...")); ?>"></div>
							</noindex>
						</div>
						<?php if ($photo_class){ ?>
							<a href="<?php html($image); ?>" class="miw_meta_photo ajax-modal" rel="<?php echo $ctype['name'].$item['id']; ?>"><i class="fa fa-photo"></i></a>
							<?php 
								if (isset($photos) && count($photos) > 1){
									unset($photos[0]);
									foreach($photos as $photo){
										$photo = html_image_src($photo, 'big', true);
										echo '<a class="sc'.$item['id'].'-photos" href="'.$photo.'" rel="' . $ctype['name'] . '' . $item['id'] . '"></a>';
									}
								} 
							?>
						<?php } ?>
						<a href="<?php html($url); ?>" class="miw_meta_link" rel="nofollow">
							<i class="fa fa-link"></i> &nbsp; <noindex><?php html(LANG_MORE); ?></noindex>
						</a>
						<a class="miw_share-toggle dsct_top_left" onClick="miwShareToggle(this)" data-tc-tip="Поделиться">
							<i class="fa fa-share-alt" aria-hidden="true"></i>
						</a>
						<?php 
							if (isset($fav) && !empty($fields[$fav]) && $fields[$fav]['is_in_list']){
								echo $fields[$fav]['handler']->setItem($item)->parseTeaser($item[$fav]);
							}
						?>
					</div>
					
					<div class="miw_photo_block miwpb_<?php html($list_pos); ?>" style="background-color:<?php html($list_bg); ?>">
						<div class="miw_photo" style="width:288px; height:<?php html($list_height); ?>px">
							<img src="<?php html($image); ?>" alt="<?php html($item['title']); ?>" />
						</div>
						<?php if (!empty($item['variants']) && empty($showcase->options['variants_off'])){ ?>
							<?php
								$i = 1;
								$colors = false;
								if (!empty($fields['color'])){
									$colors = $fields['color']['handler']->getListItems(false);
								}
							?>
							<div class="sc_variants_selector">
							<?php if (empty($showcase->options['variants_list']) || $showcase->options['variants_list'] == 'box'){ ?>
								<?php foreach ($item['variants'] as $variant){ ?>
									<?php
										if (empty($variant['in']) || $variant['in'] == 'none' || (int)$variant['in'] < 1){ continue; }
										if ($i == 4){ 
											echo '<a href="' . $url . '" class="sc_variant_selector scvs_count" data-sc-tip="Все варианты" rel="nofollow">+' . (count($item['variants']) - 3) . '</a>';
											break;
										}
										$photo = !empty($variant['photo']) ? html_image_src($variant['photo'], 'big', true) : 0;
									?>
									<div class="sc_variant_selector" <?php if (!empty($variant['color']) && !empty($colors[$variant['color']]['color'])){ ?>style="background:<?php html($colors[$variant['color']]['color']); ?>"<?php } ?> data-sc-tip="Вариант <?php html($i); ?>" data-title="<?php html($variant['title']); ?>" <?php if (isset($fields['price']) && $fields['price']['is_in_list'] && $this->controller->cms_user->isInGroups($fields['price']['groups_read']) && !empty($item['price'])){ ?>data-price="<?php echo $showcase->getPriceFormat($variant['price']); ?>"<?php } ?> <?php if (isset($fields['sale']) && $fields['sale']['is_in_list'] && $this->controller->cms_user->isInGroups($fields['sale']['groups_read']) && !empty($variant['sale']) && $variant['sale'] > 0){ ?>data-sale="<?php echo $showcase->getPriceFormat($variant['sale']); ?>"<?php } ?> <?php if ($photo){ ?>data-photo="<?php html($photo); ?>"<?php } ?> data-url="<?php echo $url . '?&variant=' . $variant['id']; ?>" onClick="icms.showcase.setListVariant(this, <?php html($item['id']); ?>)"></div> 
									<?php $i++; ?>
								<?php } ?>
							<?php } else { ?>
								<select class="sc_variations_select" data-item_id="<?php html($item['id']); ?>">
									<option>Выбрать вариант</option>
									<?php foreach ($item['variants'] as $variant){ ?>
										<?php
											//if (empty($variant['in']) || $variant['in'] == 'none' || (int)$variant['in'] < 1){ continue; }
											if ($i == 8){ 
												break;
											}
											$photo = !empty($variant['photo']) ? html_image_src($variant['photo'], 'big', true) : 0;
										?>
										<option value="<?php html($variant['id']); ?>" data-title="<?php html($variant['title']); ?>" <?php if (isset($fields['price']) && $fields['price']['is_in_list'] && $this->controller->cms_user->isInGroups($fields['price']['groups_read']) && !empty($variant['price'])){ ?>data-price="<?php echo $showcase->getPriceFormat($variant['price']); ?>"<?php } ?> <?php if (isset($fields['sale']) && $fields['sale']['is_in_list'] && $this->controller->cms_user->isInGroups($fields['sale']['groups_read']) && !empty($variant['sale']) && $variant['sale'] > 0){ ?>data-sale="<?php echo $showcase->getPriceFormat($variant['sale']); ?>"<?php } ?> <?php if ($photo){ ?>data-photo="<?php html($photo); ?>"<?php } ?> data-url="<?php echo $url . '?&variant=' . $variant['id']; ?>" <?php if (empty($variant['in']) || $variant['in'] == 'none' || (int)$variant['in'] < 1){ ?>disabled<?php } ?> data-in_stock="<?php echo (!empty($variant['in']) && $variant['in'] != 'none') ? $variant['in'] : 0; ?>"><?php html($variant['title']); ?></option>
										<?php $i++; ?>
									<?php } ?>
								</select>
							<?php } ?>
							</div>
						<?php } ?>
						<?php if (!empty($item['is_vip'])){ ?>
							<div class="miw_vip"><div class="miw_vip_text">vip</div></div>
						<?php } ?>
					</div>
					
					<?php if (!empty($item['label'])){ ?>
						<div class="miw_block_polosa">
							<div class="miw_polosa">
								<?php html(mb_strimwidth(strip_tags($item['label']), 0, 6)); ?>
							</div>
						</div>
					<?php } else if (!empty($item['sale']) && $item['sale'] > 0 || !empty($variant['sale']) && $variant['sale'] > 0){ ?>
						<div class="miw_block_polosa">
							<div class="miw_polosa is_sc_sale">Скидка</div>
						</div>
					<?php } ?>
					
					<div class="miw_description">
						<h5 class="miw_title">
							<a href="<?php echo $url; ?>" title="<?php html($item['title']); ?>">
								<?php html($item['title']); ?>
							</a>
						</h5>
						<div class="miw_price_box">
							<?php if (isset($fields['price']) && $fields['price']['is_in_list'] && $this->controller->cms_user->isInGroups($fields['price']['groups_read'])){ ?>
								<div class="miw_price">
									<?php echo $fields['price']['handler']->setItem($item)->parseTeaser($item['price']); ?> 
								</div>
								<?php if (!empty($item['sale']) && $item['sale'] > 0 || !empty($variant['sale']) && $variant['sale'] > 0){ ?>
									<s class="sc_old_price"><?php echo $showcase->getPriceFormat($item['price']); ?></s>
								<?php } ?>
							<?php } else { ?>
								<div class="miw_price">&nbsp;</div>
							<?php } ?>
							<?php if (isset($revs) && $fields[$revs]['is_in_list']){ ?>
								<?php echo $fields[$revs]['handler']->setItem($item)->parseTeaser($item[$revs]); ?>
							<?php } ?>
						</div>
						<div class="miw_footer">
							<?php if (!empty($item['rating_widget'])){ ?>
								<span><i class="fa fa-thumbs-up"></i> <?php html($item['rating']); ?></span> 
							<?php } ?>
							<?php if (!empty($ctype['options']['hits_on'])){ ?>
								<span><i class="fa fa-eye"></i> <?php html($item['hits_count']); ?></span> 
							<?php } ?>
							<?php if ($ctype['is_comments'] && $item['is_comments_on'] && !isset($revs)){ ?>
								<span><i class="fa fa-comments-o"></i> <?php html($item['comments']); ?></span> 
							<?php } ?>
							<a href="javascript:void(0)" class="more sc_cart_btn" rel="nofollow" data-in_stock="<?php echo !empty($item['in_stock']) ? $item['in_stock'] : 0; ?>" data-item_id="<?php html($item['id']); ?>" data-variant="<?php echo !empty($item['seted_variant']) ? $item['seted_variant'] : 0; ?>">В корзину</a> 
						</div>
					</div>
				
				</div>

            </div>
			
		<?php } ?>
	</div>
	
	<?php ob_start(); ?>
	<script>
	
		icms.showcase.list_pos = '<?php echo ($list_pos == 'contain') ? 'center' : $list_pos; ?>';
		icms.showcase.list_height = '<?php echo $list_height; ?>';
	
		$(document).ready(function() {
			$(".miw_share_block").hover(function() {
				$(this).parent().find('.miw_photo_block').addClass("miw_hover");
			  }, function() {
				$(this).parent().find('.miw_photo_block').removeClass("miw_hover");
			  }
			);
		});
		
		function miwShareToggle(button){
			$(button).parent().toggleClass('miw_card__social--active');
			if ($(button).parent().hasClass('miw_card__social--active')) {
				$('.miw_card__social', $(button).parent()).css('height', '<?php echo $list_height; ?>px');
			} else {
				$('.miw_card__social', $(button).parent()).removeAttr('style');
			}
			$(button).toggleClass('miw_share-expanded');
		}

	</script>
	<?php $this->addBottom(ob_get_clean()); ?>

<?php } ?>