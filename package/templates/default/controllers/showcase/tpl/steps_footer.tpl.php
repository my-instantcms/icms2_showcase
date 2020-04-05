<div class="wd_scl_footer">
	<div class="wd_sclf_summ">
		<span>Итого: </span>
		<b>
			<?php echo !empty($sale) ? '<s data-sc-tip="' . $sale['title'] . '">' . $showcase->getPriceFormat($sale['old_summ']) . '</s>' : ''; ?>
			<?php echo $showcase->getPriceFormat((isset($summ) ? $summ : 0)); ?>
		</b>
	</div>
	<a class="wd_sclf_checkout" 
		rel="nofollow"
		<?php if (empty($hide_href)){ ?>href="<?php echo href_to('showcase', 'cart', (!empty($next) ? $next : '')); ?>"<?php } ?> 
		<?php
			if (!empty($attributes) && is_array($attributes)){ 
				foreach($attributes as $key => $val){
					if ($key == 'href' || $key == 'rel'){ continue; }
					echo $key . '="' . $val . '" ';
				}
			}
		?> 
		><?php echo ($next != 'checkout') ? LANG_CONTINUE : 'Оформить заказ'; ?></a>
</div>