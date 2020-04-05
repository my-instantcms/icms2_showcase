<?php if (!empty($item['price'])){ ?>
	<button class="sc_cart_btn" data-item_id="<?php html($item['id']); ?>" data-variant="<?php echo !empty($item['seted_variant']) ? $item['seted_variant'] : 0; ?>">
		<svg class="sc_cart_btn_swiper" width="202" height="48px" version="1.1" xmlns="http://www.w3.org/2000/svg">
			<path d="M 0 0 L 202 0 L 192 48 L 0 48" stroke="none"></path>
		</svg>
	  <span class="sc_cart_btn_icon"><i class="fa fa-shopping-cart fa-fw"></i></span>
	  <span class="sc_cart_btn_label">В корзину</span>
	</button>
<?php } else if (!empty($item['preorder'])){ ?>
	<a href="<?php echo href_to('showcase', 'add_preorder', array($item['id'], (!empty($item['seted_variant']) ? $item['seted_variant'] : 0))); ?>" class="sc_preorder_btn ajax-modal" title="Предварительный заказ">
		<svg class="sc_cart_btn_swiper" width="202" height="48px" version="1.1" xmlns="http://www.w3.org/2000/svg">
			<path d="M 0 0 L 202 0 L 192 48 L 0 48" stroke="none"></path>
		</svg>
	  <span class="sc_preorder_btn_icon"><i class="fa fa-shopping-cart fa-fw"></i></span>
	  <span class="sc_preorder_btn_label">Под заказ</span>
	</a>
<?php } ?>