<?php if ($field->title) { ?><label for="<?php echo $field->id; ?>"><?php echo $field->title; ?></label><?php } ?>
<?php 
	$icon = empty($field->item['sc_icon']) ? 'default.png' : $field->item['sc_icon'];
	$path = '/templates/default/controllers/showcase/img/icons/';
	$papka = $path . "/%s";
	$list = cmsCore::getFilesList($path, '*.png');
?>
<style>.ft_mycaticon{overflow:hidden}.showcase_form_icon .value{overflow:hidden;}.showcase_form_icon.<?php html($field->name); ?> .value .icon{width:40px;height:40px;border:1px solid #ccc;padding:3px;background-repeat:no-repeat;background-position:center;float:left;margin-right:10px;}#showcase_form_icon ul{list-style:none;margin:0;display:none;}#showcase_form_icon ul li.is_actv,.showcase_form_icon .value .icon,#showcase_form_icon ul li:hover{background-color:#E1FDFD;}#showcase_form_icon.<?php html($field->name); ?> ul li{width:40px;height:40px;margin:5px 4px 0 0;float:left;border:1px solid #ccc;}#showcase_form_icon ul li a{display:block;margin:3px;}#showcase_form_icon ul li a,.showcase_form_icon .value a{cursor: pointer;}</style>
<?php ob_start(); ?>
<script>
	$(document).ready(function(){
		$('#sc_fa').iconpicker("#sc_fa");
	});
    function sett(name, icon, id) {
        $('#' + name).attr("value", icon);
		$('#showcase_form_icon.'+name+' li').removeClass('is_actv');
        $('#showcase_form_icon.'+name+' #'+id).parent().addClass('is_actv');        
		$('.showcase_form_icon.'+name+' .value .icon').html('<img src="<?php html($path); ?>' + icon+'">');
    }
	function show(name) {$('#showcase_form_icon.' + name + ' ul').toggle();}
</script> 
<?php $this->addBottom(ob_get_clean()); ?>
<div class="showcase_form_icon <?php html($field->name); ?>">		
	<div class="value">
		<div class="icon"><img src="<?php printf($papka, $icon); ?>"></div>
		<a onClick="show('<?php html($field->name); ?>')">Показать/Скрыть иконки</a>
	</div>
		<div id="showcase_form_icon" class="<?php html($field->name); ?>">
			<?php if ($list) { $i = 1; ?>
				<ul>
					<?php foreach($list as $file){ ?>
					<li <?php if($file == $icon) { echo 'class="is_actv"';} ?>>
						<a id="<?php echo $i; ?>" onClick="sett('<?php echo $field->name . "', '" . $file."','".$i; ?>')">
							<img src="<?php printf($papka, $file); ?>">
						</a>
					</li>
					<?php $i++; } ?>
				</ul>
			<?php } ?>
		</div>
	<input type="hidden" name="<?php html($field->name); ?>" value="<?php html($icon); ?>" id="<?php html($field->name); ?>">
</div>