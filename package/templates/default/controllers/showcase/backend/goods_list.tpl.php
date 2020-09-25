<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/libs/tabulator/css/tabulator.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/libs/tabulator/css/semantic-ui/tabulator_semantic-ui.min.css', false), false);
	$this->addJS($this->getTplFilePath('controllers/showcase/libs/tabulator/js/tabulator.min.js', false), false);
	$this->addBreadcrumb('Товары', $this->href_to('goods'));
	$this->addBreadcrumb('Список товаров');
	$this->setPageTitle('Список товаров');

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('goods'); ?>
	<div class="page-content">
		<div class="tabulator_loader"><img src="/templates/default/controllers/showcase/img/ajax-loader.gif"> </div>
		<div id="example-table"></div>
	</div>
</div>
<?php ob_start(); ?>
<script>
	var variants = 1;
	var filter = {};
	var table;
	var cats_all = <?php echo ($cats_all && json_encode($cats_all)) ? json_encode($cats_all) : '{}'; ?>;
	var cats_list = <?php echo ($cats_list && json_encode($cats_list)) ? json_encode($cats_list) : '{}'; ?>;
	var fields = <?php echo json_encode($fields) ? json_encode($fields) : '{}'; ?>;
	var variant_cols = [
		{
			title: "ID",
			field: "id",
			width: 70,
			headerSort:false,
			align: "center"
		},
		{
			title:"Артикул",
			field:"artikul",
			width: 110,
			headerSort:false,
			align: "center",
		},
		{
			title:"Заголовок",
			field:"title",
			headerSort:false,
		},
		{
			title:"Цена",
			field:"price",
			width: 150,
			headerSort:false,
			formatter:function(cell, formatterParams){
			   return cell.getValue() + ' <?php html(!empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY); ?>';
			},
		}
	];
	<?php if ($fields){ ?>
		<?php foreach($fields as $name => $field){ ?>
			<?php 
				if ($field['type'] == 'scprice' || $field['type'] == 'scvariations'){ continue; }
			?>
			var values_<?php html($name); ?> = <?php echo json_encode($field['handler']->getListItems(1)); ?>;
			variant_cols[variant_cols.length] = {
				title:"<?php html($field['title']); ?>",
				field:"<?php html($name); ?>",
				width: 150,
				formatter:function(cell, formatterParams){
				   return values_<?php html($name); ?>[cell.getValue()];
				},
			};
		<?php } ?>
	<?php } ?>
	variant_cols[variant_cols.length] = {
		title:"В наличии",
		field:"in",
		width: 100,
		headerSort:false
	};
    $(document).ready(function() {

		table = new Tabulator("#example-table", {
			height:"100%",
			ajaxURL:"<?php echo $this->href_to('goods_ajax'); ?>",
			ajaxRequesting:function(url, params){
				$('.tabulator_loader').show();
				return true;
			},
			ajaxResponse:function(url, params, response){
				$('.tabulator_loader').hide();
				return response;
			},
			ajaxProgressiveLoad:"scroll",
			paginationSize:20,
			placeholder:"Нет товаров",
			layout:"fitColumns",
			autoResize:true,
			resizableColumns:false,
			/*groupBy:"category_id",
			groupHeader:function(value, count, data, group){
				return cats_list[value] + "<span style='color:#d00; margin-left:10px;'>(" + count + " товар)</span>";
			},*/
			columns:[
				<?php if (!empty($fields['variants'])){ ?>
				{
					title:"Варианты",	
					field:"variants_count",
					width: 120,
					cellClick:function(e, cell){
						$('#varEl_' + cell.getData().id).toggleClass('variants_open');
					}
				},
				<?php } ?>
				{
					title:"ID",
					field:"id",
					sorter:"number",
					width: 70,
					align: "center",
					headerFilterPlaceholder: 'ID',
					headerFilter:numberFilterEditor,
					responsive:0
				},
				{
					title:"Артикул",
					field:"artikul",
					width: 110,
					sorter:"string",
					align: "center",
					headerFilterPlaceholder: 'Найти',
					headerFilter:inputFilterEditor
				},
				{
					title:"Заголовок",
					field:"title",
					sorter:"string",
					editor:"input",
					headerFilterPlaceholder: 'Найти товар',
					headerFilter:inputFilterEditor
				},
				{
					title:"Категория",
					field:"category_id",
					editor:"select",
					editorParams:{values:cats_list},
					headerFilter:catsFilterEditor,
					formatter:function(cell, formatterParams){
					   var value = cell.getValue();
					   if (typeof cats_list[value] !== "undefined"){
							return cats_list[value];
						}else{
							return value;
						}
					}
				},
				{
					title:"Цена",
					field:"price",
					width: 150,
					editor:"number",
					sorter:"number",
					formatter:function(cell, formatterParams){
					   return cell.getValue() + ' <?php html(!empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY); ?>';
					},
					headerFilter:minMaxFilterEditor,
					bottomCalc:"sum",
					bottomCalcParams:{precision:false}
				},
				{
					title:"Цена скидкой",
					field:"sale",
					width: 150,
					editor:"number",
					sorter:"number",
					formatter:function(cell, formatterParams){
					   return cell.getValue() ? cell.getValue() + ' <?php html(!empty($this->controller->options['currency']) ? $this->controller->options['currency'] : LANG_CURRENCY); ?>' : null;
					},
					headerFilter:minMaxFilterEditor
				},
			],
			rowFormatter:function(row){ 
				
				if (!variants){ return false; }

				if (typeof row.getData().variants === 'undefined' || row.getData().variants == null){ return false; }

				var varContainer = $("<div class='variantsRowFormatter' id='varEl_" + row.getData().id + "'>");
				var varTable = $("<div class='tableRowFormatter'>");
				varContainer.append(varTable);
				row.getElement().appendChild(varContainer.get(0));
				var subTable = new Tabulator(varTable.get(0), {
					layout:"fitColumns",
					resizableColumns:false,
					ajaxURL:"<?php echo $this->href_to('variants_ajax'); ?>",
					ajaxResponse:function(url, params, response){
						window.dispatchEvent(new Event('resize'));
						$(window).trigger('resize');
						return response;
					},
					ajaxParams:row.getData().variants,
					columns: variant_cols
				});

			},
			cellEdited:function(cell){

				var id = cell.getData().id;
				var field = cell.getField();
				var oldValue = cell.getOldValue();
				var newValue = cell.getValue();
				if (id && field && newValue){
					$('.tabulator_loader').show();
					$.post('<?php echo $this->href_to('goods_edit'); ?>', {id : id, field : field, oldValue : oldValue, newValue : newValue}, function(result){
						if(result.error){
							cell.restoreOldValue();
							icms.modal.alert(result.message, 'ui_error');
						} 
						$('.tabulator_loader').hide();
					}, 'json');
				} else {
					cell.restoreOldValue();
				}

			},
			
		});
		
	});
	
	function buildValues(field, el1, el2){
		if (field == 'price'){
			filter['price_min'] = el1.val();
			filter['price_max'] = el2.val();
		} else if (field == 'sale'){
			filter['sale_min'] = el1.val();
			filter['sale_max'] = el2.val();
		} else {
			filter[field] = el1.val();
		}
		table.setData('<?php echo $this->href_to('goods_ajax'); ?>', filter);
	}
	
	function delay(callback, ms) {
		var timer = 0;
		return function() {
			var context = this, args = arguments;
			clearTimeout(timer);
			timer = setTimeout(function () {
				callback.apply(context, args);
			}, ms || 0);
		};
	}
	
	var numberFilterEditor = function(cell, onRendered, success, cancel, editorParams){
		var container = $('<span class="numberFilterEditor">');
		var input = $('<input type="number">');
		input.keyup(delay(function (e, field) { buildValues(cell.getField(), input, ''); }, 500));
		return container.append(input).get(0);
	};

	var inputFilterEditor = function(cell, onRendered, success, cancel, editorParams){
		var container = $('<span class="inputFilterEditor">');
		var input = $('<input type="search">');
		input.keyup(delay(function (e, field) { buildValues(cell.getField(), input, ''); }, 500));
		return container.append(input).get(0);
	};
	
	var catsFilterEditor = function(cell, onRendered, success, cancel, editorParams){

		var dropdown = $("<select class='catsFilterEditor'>");
		$("<option value='0'></option>").appendTo(dropdown);
		
		$.each(cats_all, function( id, cat ) {
			var attr = {
				value   : id,
				text    : cat.title
			};
			if (cat.ns_level == 1){
				attr.disabled = true;
			}
			$("<option>", attr).appendTo(dropdown);
		});
		
		dropdown.change(function() { buildValues('category_id', $(this), '');	});

		return dropdown.get(0);
	};
	
	var minMaxFilterEditor = function(cell, onRendered, success, cancel, editorParams){

		var end;
		var container = $('<span class="minMaxHeadFilter">');
		var start = $('<input type="number" placeholder="мин." min="0">');
		start.val(cell.getValue());

		end = start.clone();
		end.attr('placeholder', 'макс.');

		start.keyup(delay(function (e) { buildValues(cell.getField(), start, end); }, 500));
		end.keyup(delay(function (e) { buildValues(cell.getField(), start, end); }, 500));

		container.append(start);
		container.append(end);

		return container.get(0);

	};
	
</script>
<?php $this->addBottom(ob_get_clean()); ?>