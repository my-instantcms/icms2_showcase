<?php 

	/*
		Доступные данные
		$value 		- это значение поле Цена
		$this 		- это объект поле Цена, например что бы получить заголовок поля: $this->title
		$this->item - это массив данных текущего товара, например что бы получить заголовок товара: $this->item['title']
		$tpl		- это объект текущего шаблона, например что бы css файл: $tpl->addCSS('ПУТЬ_К_ФАЙЛУ.css');
	*/

	$old_price = $value;
	if (!empty($this->item['sale']) && $this->item['sale'] > 0){
		$value = $this->item['sale'];
	}
	
	$showcase = cmsCore::getController('showcase');
	$show_instock = !empty($showcase->options['show_instock']) ? $showcase->options['show_instock'] : 'counter';

?>
<div class="field ft_scprice f_price auto_field">
	<div class="value">
		<div itemprop="offers" itemscope itemtype="http://schema.org/Offer" class="is_scPrice" data-current="<?php html($value); ?>" data-old_price="<?php echo $showcase->getPriceFormat($old_price, false, false); ?>">
			<?php if (!empty($this->item['sale']) && $this->item['sale'] > 0){ ?><s class="is_scOldPrice"><?php echo $showcase->getPriceFormat($old_price, false, false); ?></s> <?php } ?>
			<span itemprop="price" content="<?php html($value); ?>"><?php echo $showcase->getPriceFormat($value, true); ?> </span>
			
			<meta content="<?php echo $_SERVER['REQUEST_URI']; ?>" itemprop="url" />
			<?php if ($show_instock != 'none'){ ?>
				<?php if (!empty($this->item['in_stock'])){ ?>
					<link itemprop="availability" href="http://schema.org/InStock" />
					<p class="sc_inStock_box scis_yes"><i class="fa fa-check-square-o"></i> Есть в наличии 
					<?php if ($show_instock == 'counter'){ ?>(<?php html($this->item['in_stock']); ?>)<?php } ?></p>
				<?php } else { ?>
					<p class="sc_inStock_box scis_not"><i class="fa fa-warning"></i> Нет в наличии</p>
				<?php } ?>
			<?php } ?>
		</div>
	</div>
</div>