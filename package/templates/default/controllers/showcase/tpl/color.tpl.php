<?php 

	/*
		Доступные данные
		$value 		- это значение поле (Без обработки)
		$colors		- это значение поле (C обработкой)
		$items		- это список всех предустановленных значении
		$this 		- это объект поле, например что бы получить заголовок поля: $this->title
		$this->item - это массив данных текущего товара, например что бы получить заголовок товара: $this->item['title']
		$tpl		- это объект текущего шаблона, например что бы css файл: $tpl->addCSS('ПУТЬ_К_ФАЙЛУ.css');
	*/

?>
<div class="is_colorPicker" id="is_<?php html($this->element_name); ?>">
	<?php if ($colors){ ?>
		<?php foreach ($colors as $id => $color){ ?>
			<div class="sccolor_item" data-sc-tip="<?php html($color['title']); ?>"><div class="sc_colorbox" style="background:<?php html($color['color']); ?>"></div></div>
		<?php } ?>
	<?php } ?>
</div>
<style>
	.is_colorPicker, .is_colorPicker * {-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
	.is_colorPicker .sccolor_item{
		display: inline-block !important;
		position: relative;
		margin: 1px;
		border: 1px solid #ddd;
		padding: 2px;
	}
	.is_colorPicker .sccolor_item:hover{border-color:#999}
	.is_colorPicker .sccolor_item.active{border-color:#000}
	.is_colorPicker .sccolor_item.active .sc_colorbox:before {
		content: '\f00c';
		display: block;
		position: absolute;
		font-family: 'fontawesome';
		top: 2px;
		left: 5px;
		font-size: 12px;
		color: #222;
		height: 18px;
		line-height: 18px;
		border: none;
	}
	.is_colorPicker .sccolor_item.active.set_process .sc_colorbox{background:#fff !important}
	.is_colorPicker .sccolor_item.active.set_process .sc_colorbox:before{content: none}
	.is_colorPicker .sc_colorbox{
		width: 18px;
		height: 18px;
		overflow: hidden;
	}
	.is_colorPicker .sc_colorbox .fa{
		width: 14px;
		height: 14px;
		margin: 2px 0 0 2px;
		vertical-align: top;
	}
	.is_colorPicker .sccolor_item[data-sc-tip]:before{left:4px}
	.is_colorPicker .sccolor_item[data-sc-tip]:hover:before{
		border-top-color: #1a1a1a;
		border-right-color: transparent;
		border-bottom-color: transparent;
		border-left-color: transparent;
	}
	.goods_item_props .is_colorPicker .sccolor_item:last-child[data-sc-tip]:after{left:auto;right:-10px}
</style>