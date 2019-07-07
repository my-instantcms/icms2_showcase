<?php
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/showcase.css', false), false);
	$this->addBreadcrumb(LANG_HELP);
	$this->setPageTitle(LANG_HELP);

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('help'); ?>
	<div class="page-content">
		<div class="sc_admin_help">
			<div class="sc_help_search">
				<div class="sc_help_search_box">
					<input type="text" id="sc_searche_input" onkeyup="myFunction()" placeholder="Что ищем?">
					<button type="submit" class="sc_searchButton" onclick="myFunction()">
						<i class="glyphicon glyphicon-search"></i>
					</button>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-4">
					<div class="panel panel-primary">
						<div class="panel-heading">
							<h3 class="panel-title">Товары</h3>
						</div>
						<div class="panel-body scHelpSelector" data-action="goods">
							<div class="overlay"><i class="glyphicon glyphicon-refresh"></i></div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="panel panel-info">
						<div class="panel-heading">
							<h3 class="panel-title">Корзина и заказы</h3>
						</div>
						<div class="panel-body scHelpSelector" data-action="cart">
							<div class="overlay"><i class="glyphicon glyphicon-refresh"></i></div>
						</div>
					</div>
				</div>
				<div class="col-lg-4">
					<div class="panel panel-success">
						<div class="panel-heading">
							<h3 class="panel-title">Работа с полями</h3>
						</div>
						<div class="panel-body scHelpSelector" data-action="fields">
							<div class="overlay"><i class="glyphicon glyphicon-refresh"></i></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		if ($('.scHelpSelector').length){
			$(".scHelpSelector").each(function() {
				var block = $(this);
				var action = block.data('action');
				if (action && typeof(action) !== 'undefined'){
					$.post('<?php echo $this->href_to('help'); ?>/' + action, false, function(result){
						block.html(result.html);	
					}, 'json');
				}
			});
		}
	});
	function myFunction() {
		var filter, txtValue;
		filter = $('#sc_searche_input').val().toUpperCase();
		$(".panel .nav-stacked li > a[data-search]").each(function() {
			txtValue = $(this).data('search');
			if (txtValue && txtValue.toUpperCase().indexOf(filter) > -1) {
				$(this).parents('li').show();
			} else {
				$(this).parents('li').hide();
			}
		});
	}
</script>