<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/wd_cat_slider.css', false));
	$this->addJS($this->getJavascriptFileName('jquery-owl.carousel'));
	$this->addCSS('templates/default/css/jquery-owl.carousel.css');
?>
<div class="scs_slider">
	<div class="customNav">            
		<a class="prev"><i class="fa fa-angle-left"></i></a>             
		<a class="next"><i class="fa fa-angle-right"></i></a>
	</div>
	<div class="owl-carousel scs_owl_selector">
		<?php foreach($cats as $id=>$item){ ?>
			<div class="item-cat">
				<a href="<?php echo href_to($ctype_name, $item['slug']); ?>">
					<span class="scs_icon" style="border-color:<?php echo !empty($item['sc_color']) ? $item['sc_color'] : '#ebba16'; ?>">
						<?php if ($icon == 'sc_fa'){ ?>
							<i class="fa <?php echo !empty($item['sc_fa']) ? $item['sc_fa'] : 'fa-folder'; ?> fa-2x" style="color:<?php echo !empty($item['sc_color']) ? $item['sc_color'] : '#ebba16'; ?>"></i>
						<?php } else { ?>
							<img src="/templates/default/controllers/showcase/img/icons/<?php echo !empty($item['sc_icon']) ? $item['sc_icon'] : 'default.png'; ?>" alt="<?php html($item['title']); ?>" />
						<?php } ?>
					</span>
					<span class="scs_title"><?php html($item['title']); ?></span>
				</a>
			</div>
		<?php } ?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
	
		var owl = $('.scs_owl_selector');
		$(".prev").on('click', function () {
			owl.trigger('prev.owl.carousel');
		});
		$(".next").on('click', function () {
			owl.trigger('next.owl.carousel');
		});
		owl.owlCarousel({
			items: <?php echo $slides ? $slides : 6; ?>,
			loop: true,
			autoplay:true,
			autoplayTimeout:5000,
			autoplayHoverPause:true,
			margin: 0,
			responsiveClass:true,
			dots:false,
			nav:false,
			responsive:{
				0:{
					items:1,
				},
				421:{
					items:2,
				},
				640:{
					items:3,
				},
				800:{
					items:4,
				},
				1024:{
					items:5,
				},
				1170:{
					items:<?php echo $slides ? $slides : 6; ?>
				}
			}
		});
	});
</script>