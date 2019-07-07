<div class="is_volumePicker" id="is_<?php html($this->element_name); ?>">
	<?php if ($volumes){ ?>
		<?php foreach ($volumes as $key => $volume){ ?>
			<div class="scvolume_item"><?php html($volume); ?></div>
		<?php } ?>
	<?php } ?>
</div>
<style>
	.is_volumePicker, .is_volumePicker * {-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
	.is_volumePicker .scvolume_item{
		display: inline-block !important;
		position: relative;
		margin: 2px;
		border: 1px solid #ddd;
		border-radius: 2px;
		padding: 0 3px
	}
	.is_volumePicker .scvolume_item:hover{border-color:#999}
	.is_volumePicker .scvolume_item.active{border-color:#000}
	.is_volumePicker .scvolume_item.active:before {
		content: '\f00c';
		display: block;
		float: left;
		font-family: 'fontawesome';
		font-size: 12px;
		color: #222;
		height: 18px;
		line-height: 22px;
		border: none;
		margin-right: 2px;
	}
	.is_volumePicker .scvolume_item.active.set_process{background:#fff !important}
	.is_volumePicker .scvolume_item.active.set_process:before{content: none}
	.is_volumePicker .fa{
		width: 14px;
		height: 14px;
		margin: 3px 0 0 3px;
		vertical-align: top;
	}
</style>