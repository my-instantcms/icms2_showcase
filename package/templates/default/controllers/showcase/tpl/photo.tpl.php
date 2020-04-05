<?php 
	$photos = cmsModel::yamlToArray($item['photo']);
	$variant_photo = !empty($item['variant_photo']) ? cmsModel::yamlToArray($item['variant_photo']) : false;
	$variant_attached = !empty($item['variant_attached']) ? true : false;
	$view_pos = !empty($showcase->options['view_pos']) ? $showcase->options['view_pos'] : 'center';
	$view_bg = !empty($showcase->options['view_bg']) ? $showcase->options['view_bg'] : '#fff';
	$cover_size = !empty($showcase->options['cover_size']) ? $showcase->options['cover_size'] : 'original';
	if ($photos) {
		$this->addCSS($this->getTplFilePath('controllers/showcase/css/fields.css', false));
		$this->addCSS($this->getTplFilePath('controllers/showcase/libs/slick/slick.min.css', false));
		$this->addCSS($this->getTplFilePath('controllers/showcase/libs/slick/slick-theme.min.css', false));
		$this->addCSS($this->getTplFilePath('controllers/showcase/libs/lightgallery/css/lightgallery.min.css', false));
		$this->addJS($this->getTplFilePath('controllers/showcase/libs/lightgallery/js/lightgallery.min.js', false));
		$this->addJS($this->getTplFilePath('controllers/showcase/libs/lightgallery/js/lg-zoom.min.js', false));
		$this->addJS($this->getTplFilePath('controllers/showcase/js/imgLiquid.min.js', false));
?>

	<div class="field ft_<?php echo $photo['type']; ?> f_photo <?php echo $photo['options']['wrap_type']; ?>_field" <?php if($photo['options']['wrap_width']){ ?> style="width: <?php echo $photo['options']['wrap_width']; ?>;"<?php } ?>>
		<?php if ($photo['options']['label_in_item'] != 'none') { ?>
			<div class="title_<?php echo $photo['options']['label_in_item']; ?>"><?php html($photo['title']); ?>: </div>
		<?php } ?>
		<div class="value">
			
			<div id="sync1">
				<?php if ($variant_photo && !$variant_attached){ ?>
					<div class="item mgLiquidNoFill imgLiquid miwpb_<?php html($view_pos); ?>" style="width:100%;height:400px;background-color:<?php html($view_bg); ?>">
						<div class="sc_gallery_selector" data-src="<?php echo html_image_src($variant_photo, 'big', true); ?>"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>
						<img data-lazy="<?php echo html_image_src($variant_photo, 'big', true); ?>" itemprop="image" alt="<?php html($item['title']); ?>" />
					</div>
				<?php } ?>
				<?php foreach ($photos as $id => $img){ ?>
					<div class="item mgLiquidNoFill imgLiquid miwpb_<?php html($view_pos); ?>" style="width:100%;height:400px;background-color:<?php html($view_bg); ?>">
						<div class="sc_gallery_selector" data-src="<?php echo html_image_src($img, $cover_size, true); ?>"><i class="fa fa-arrows-alt" aria-hidden="true"></i></div>
						<img data-lazy="<?php echo html_image_src($img, 'big', true); ?>" itemprop="image" alt="<?php html($item['title']); ?>" />
					</div>
				<?php } ?>
			</div>
				
			<?php if (!empty($item['sale']) || !empty($item['label'])){ ?>
				<div class="sc_label_box">
					<?php if (!empty($item['sale'])){ ?><span class="sc_label_sale">Скидка</span><?php } ?>
					<?php if (!empty($item['sold'])){ ?><span class="sc_label_sold">Продано</span><?php } ?>
					<?php if (!empty($item['label'])){ ?><span class="sc_label"><?php html($item['label']); ?></span><?php } ?>
				</div>
			<?php } ?>
			
			<div id="sync2">
				<?php if ($variant_photo && !$variant_attached){ ?>
					<div class="item is_v_<?php html($item['variant_id']); ?>">
						<img src="<?php echo html_image_src($variant_photo, 'small', true); ?>" itemprop="image" alt="<?php html($item['title']); ?>" />
					</div>
				<?php } ?>
				<?php foreach ($photos as $id => $img){ ?>
					<div class="item">
						<img src="<?php echo html_image_src($img, 'small', true); ?>" itemprop="image" alt="<?php html($item['title']); ?>" />
					</div>
				<?php } ?>
			</div>

			<script src="/<?php html($this->getTplFilePath('controllers/showcase/libs/slick/slick.min.js', false)); ?>"></script>
			<script>

				icms.showcase.sync1 = $('#sync1').slick({
					slidesToShow: 1,
					slidesToScroll: 1,
					fade: false,
					lazyLoad: 'ondemand',
					asNavFor: '#sync2'
				});
				icms.showcase.sync2 = $('#sync2').slick({
					slidesToShow: 4,
					slidesToScroll: 1,
					variableWidth: true,
					asNavFor: '#sync1',
					focusOnSelect: true
				});
				
				$('#sync1').on('lazyLoaded', function(event){
					$(".slick-current", event.target).imgLiquid({
						fill: true,
						horizontalAlign: "center",
						verticalAlign: "<?php html($view_pos); ?>"
					});
				});

				$(document).ready(function(){
					
					icms.showcase.light_gallery = $('#sync1').lightGallery({
						selector: '.item:not(.slick-cloned) .sc_gallery_selector',
						download: false
					});

					<?php if ($variant_attached){ ?>
						/* Когда вариант связан а не загружен, открываем его по умолчанию*/
						if ($('#sync2 .slick-slide:not(.slick-cloned) img[src="<?php echo html_image_src($variant_photo, 'small', true); ?>"]').length){
							var current = $('#sync2 .slick-slide:not(.slick-cloned) img[src="<?php echo html_image_src($variant_photo, 'small', true); ?>"]').parents('.item').data('slick-index');
							icms.showcase.sync2.slick('slickGoTo', current);
						}
					<?php } ?>
					
					<?php if (!empty($item['seted_variant'])){ ?>
						icms.showcase.seted_variant = <?php html($item['seted_variant']); ?>;
					<?php } ?>
					
				});

			</script>
		</div>
	</div>
	
	
<?php } ?>