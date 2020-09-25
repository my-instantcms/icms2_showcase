<?php 
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/lobilist.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addJS($this->getTplFilePath('controllers/showcase/backend/js/jquery.ui.touch-punch-improved.js', false), false);
	$this->addJS($this->getTplFilePath('controllers/showcase/backend/js/bootstrap.min.js', false), false);
	$this->addJS($this->getTplFilePath('controllers/showcase/backend/js/lobilist.min.js', false), false);
?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('index'); ?>
	<div class="page-content">
		<?php if($counts){ ?>
			<div class="row">
				<?php foreach($counts as $name => $count){ ?>
					<?php 
						$url = $this->href_to($name);
					?>
					<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12">
						<div class="small-box" style="background:<?php html($count['color']); ?>">
							<a href="<?php echo $url ? $url : 'javascript:void(0);'; ?>" class="inner">
								<h3><?php html($count['count']); ?></h3>
								<p><?php html($count['title']); ?></p>
							</a>
							<a href="<?php echo $url ? $url : 'javascript:void(0);'; ?>" class="icon">
								<i class="<?php html($count['icon']); ?>"></i>
							</a>	
							<a href="<?php html($count['url']); ?>" target="_blank" class="small-box-footer">
								<?php html($count['url_title']); ?> <i class="glyphicon glyphicon-link"></i>
							</a>
						</div>
					</div>
				<?php } ?>
			</div>
		<?php } ?>
		<div class="row">
			<div class="col-md-6">
                <div id="actions-by-ajax"></div>
				<?php ob_start(); ?>
				<script>
				   $(function(){
						$('[data-toggle="tooltip"]').tooltip();
						$('#actions-by-ajax').lobiList({
							name: '<?php html($this->controller->cms_user->id); ?>',
							actions: {
								load: '<?php echo $this->href_to('todo', 'load'); ?>',
								insert: '<?php echo $this->href_to('todo', 'add'); ?>',
								delete: '<?php echo $this->href_to('todo', 'delete'); ?>',
								update: '<?php echo $this->href_to('todo', 'update'); ?>',
							},
							beforeItemDelete: function(){
								if (!confirm('<?php html(LANG_DELETE); ?>?')){ return false; }
							},
							beforeListRemove: function(list){
								if (!confirm('<?php html(LANG_DELETE); ?> задачу и все её записи?')){ return false; }
								$.post('<?php echo $this->href_to('todo', 'delete'); ?>', {id: list.$options.id, table : 1}, false, 'json');
							},
							afterMarkAsDone: function(list, obj){console.log(arguments);
								$.post('<?php echo $this->href_to('todo', 'toggle'); ?>', {id : obj.id}, false, 'json');
							},
							afterMarkAsUndone: function(list, obj){
								$.post('<?php echo $this->href_to('todo', 'toggle'); ?>', {id : obj.id}, false, 'json');
							},
							beforeListAdd: function(lobiList, list){
								$.post('<?php echo $this->href_to('todo', 'list'); ?>', false, function(result){
									if(result.success && result.success > 0){
										list.$options.id = result.success;
									} else {
										alert('Не удалось создать новую задачу');
										return false;
									}
								}, 'json');
							},
							afterItemReorder: function(list){
								var items = {};
								var listid = list.$options.id;
								$("#" + listid + " .lobilist-items li").each(function(index) {
									items[(index + 1)] = $(this).data('id');
								});
								$.post('<?php echo $this->href_to('todo', 'reorder'); ?>', {id : listid, items : items, table : 1}, false, 'json');
							},
							afterListReorder: function(){
								var items = {};
								$(".lobilists .lobilist").each(function(index) {
									items[(index + 1)] = $(this).data('db-id');
								});
								$.post('<?php echo $this->href_to('todo', 'reorder'); ?>', {items : items}, false, 'json');
							},
							titleChange: function(list, old, title){
								if (old == title) {return false;}
								$.post('<?php echo $this->href_to('todo', 'title'); ?>', {id : list.$options.id, title : title}, false, 'json');
							},
							styleChange: function(list, old, defaultStyle){
								if (old == defaultStyle) {return false;}
								$.post('<?php echo $this->href_to('todo', 'style'); ?>', {id : list.$options.id, defaultStyle : defaultStyle}, function(result){
									if(!result.success){
										return false;
									}		
								}, 'json');
							}
						});
						
						$('input[name="dueDate"]').datepicker({
							dateFormat: 'dd-mm-yy',
							minDate: '0m',
							changeMonth: true,
							changeYear: true,
							yearRange: "2016:2020"
						});
					});
					
				</script>
				<?php $this->addBottom(ob_get_clean()); ?>
			</div>
			<div class="col-md-6">
			
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h3 class="panel-title">Лог действий</h3>
						<div class="box-tools pull-right">
							<a href="<?php html($this->href_to('clear')); ?>" onclick="return confirm('Вы уверены что хотите удалить все записи лога?')" class="btn btn-box-tool" data-toggle="tooltip" title="Очистить"><i class="glyphicon glyphicon-trash"></i></a>
						</div>
					</div>
					<div class="panel-body is_bg_gray">
						<div class="row">
						
							<?php if ($logs) { ?>
								<?php foreach($logs as $log) { ?>
									<div class="col-md-12">
									  <div class="update-nag">
										<div class="update-split <?php echo $log['style'] ? 'update-' . $log['style'] : ''; ?>" data-toggle="tooltip" data-placement="right" title="<?php html(string_date_age_max($log['date'], true)); ?>"><i class="<?php html($log['icon']); ?>"></i></div>
										<div class="update-text"><?php echo ($log['text']); ?></div>
									  </div>
									</div>
								<?php } ?>

							<?php } else { ?>
								<p style="text-align:center">Данные не найдены</p>
							<?php } ?>
							
						</div>
					</div>
				</div>
			</div>
			</div>
	</div>
</div>