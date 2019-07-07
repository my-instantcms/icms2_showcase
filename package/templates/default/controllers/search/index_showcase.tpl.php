<?php

    $this->setPageTitle(LANG_SEARCH_TITLE);

    $this->addBreadcrumb(LANG_SEARCH_TITLE, $this->href_to(''));
    if($query){
        $this->addBreadcrumb($query);
    }

    $content_menu = array();

    $uri_query = http_build_query(array(
        'q'    => $query,
        'type' => $type,
        'date' => $date
    ));

    if ($results){

        foreach($results as $result){

            $content_menu[] = array(
                'title'    => $result['title'],
                'url'      => $this->href_to('index', array($result['name'])) . '?' . $uri_query,
                'url_mask' => $this->href_to('index', array($result['name'])),
                'counter'  => $result['count']
            );

            if($result['items']){
                $search_data = $result;
            }

        }

        $content_menu[0]['url'] = href_to('search') . '?' . $uri_query;
        $content_menu[0]['url_mask'] = href_to('search');

        $this->addMenuItems('results_tabs', $content_menu);

        $this->setPageTitle($query, $target_title, mb_strtolower(LANG_SEARCH_TITLE));

    }
	
	$showcase = cmsCore::getController('showcase');
	$list_pos = !empty($showcase->options['list_pos']) ? $showcase->options['list_pos'] : 'center';
	$list_height = !empty($showcase->options['list_height']) ? $showcase->options['list_height'] : 200;
	$list_bg = !empty($showcase->options['list_bg']) ? $showcase->options['list_bg'] : '#fff';

?>

<h1><?php echo LANG_SEARCH_TITLE; ?></h1>

<div id="search_form">
    <form action="<?php echo href_to('search'); ?>" method="get">
        <?php echo html_input('text', 'q', $query, array('placeholder'=>LANG_SEARCH_QUERY_INPUT)); ?>
        <?php echo html_select('type', array(
            'words' => LANG_SEARCH_TYPE_WORDS,
            'exact' => LANG_SEARCH_TYPE_EXACT,
        ), $type); ?>
        <?php echo html_select('date', array(
            'all' => LANG_SEARCH_DATES_ALL,
            'w' => LANG_SEARCH_DATES_W,
            'm' => LANG_SEARCH_DATES_M,
            'y' => LANG_SEARCH_DATES_Y,
        ), $date); ?>
        <?php echo html_submit(LANG_FIND); ?>
    </form>
</div>

<?php if ($query && empty($search_data)){ ?>
    <p id="search_no_results"><?php echo LANG_SEARCH_NO_RESULTS; ?></p>
<?php } ?>

<?php if (!empty($search_data)){ ?>

    <div id="search_results_pills">
        <?php $this->menu('results_tabs', true, 'pills-menu-small'); ?>
    </div>

    <?php 
		$this->addCSS($this->getTplFilePath('controllers/showcase/css/list_grid.css', false));
		$this->addJS($this->getTplFilePath('controllers/showcase/js/showcase.js', false));
		$this->addJS($this->getTplFilePath('controllers/showcase/js/imgLiquid.min.js', false));
	?>
	
	<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
	<script src="//yastatic.net/share2/share.js"></script>
	
	<?php $this->block('top_showcase_list'); ?>
	
	<div class="showcase_list_grid" onload="icms.showcase.bulidListGrid(this)">

		<?php foreach($search_data['items'] as $item){ ?>
			<?php
                $ctype = $item['ctype'];
                $item['ctype_name'] = $ctype['name'];
				$url = $item['url'];
				$image = $this->getTplFilePath('controllers/showcase/img/nophoto_normal.png', false);
				$photo_class = false;
				if(!empty($item['photo'])) {
					$image = html_image_src($item['photo'], 'big', true);
					$photo_class = 'sc_is_photo';
					$photos = is_array($item['photo']) ? $item['photo'] : cmsModel::yamlToArray($item['photo']);
				}
			?>			
			<div class="my_default_list_item">
				<div class="miw_preloader">
					<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>
					<span class="sr-only">Loading...</span>
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
					</div>
					
					<div class="miw_photo_block miwpb_<?php html($list_pos); ?>" style="background-color:<?php html($list_bg); ?>">
						<div class="miw_photo" style="width:288px; height:<?php html($list_height); ?>px">
							<img src="<?php html($image); ?>" alt="<?php html($item['title']); ?>" />
						</div>
					</div>
					
					<div class="miw_description">
						<h2 class="miw_title">
							<a href="<?php echo $url; ?>" title="<?php echo $item['title']; ?>">
								<?php echo $item['title']; ?>
							</a>
						</h2>
						<div class="miw_price_box">
							<?php if (!empty($item['price'])){ ?>
								<?php if (!empty($item['sale'])){ ?>
									<div class="miw_price">
										<?php echo $showcase->getPriceFormat($item['sale']); ?>
									</div>
									<s class="sc_old_price"><?php echo $showcase->getPriceFormat($item['price']); ?></s>
								<?php } else { ?>
									<div class="miw_price">
										<?php echo $showcase->getPriceFormat($item['price']); ?>
									</div>
								<?php } ?>
							<?php } ?>
						</div>
						<div class="miw_footer">
							<a href="<?php echo $url; ?>" class="more" rel="nofollow">Купить</a> 
						</div>
					</div>
				
				</div>

            </div>
			
		<?php } ?>
	</div>
	
	<?php $this->block('bottom_showcase_list'); ?>
	
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
	
	
    <?php if ($search_data['count'] > $perpage){ ?>
        <?php echo html_pagebar($page, $perpage, $search_data['count'], $page_url, $uri_query); ?>
    <?php } ?>

<?php }