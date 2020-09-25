<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/cart.css', false));
?>

<div class="wd_sc_cart sc_style_icon">
	
	<?php ob_start(); ?><script>icms.showcase.cart_styles.icon = 1;</script><?php $this->addBottom(ob_get_clean()); ?>
	
	<a class="wd_sc_cart_icon" href="<?php echo href_to('showcase', 'cart') ?>" rel="nofollow">
		<i class="<?php echo !empty($widget->options['fa']) ? $widget->options['fa'] : 'fa fa-shopping-cart'; ?>" <?php if (!empty($widget->options['color'])){ ?>style="color:<?php html($widget->options['color']); ?>"<?php } ?>></i>
		<div class="sc_cart_counter"><?php echo (isset($count) && $count) ? $count : '0'; ?></div>
	</a>

</div>