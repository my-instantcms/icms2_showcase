<div class="is_colorPicker">
	<?php if ($field->data['items']){ ?>
		<?php 
			$selected = false;
			if(is_array($field->data['selected']) && $field->data['selected']){
				foreach ($field->data['selected'] as $k => $v) {
					if(is_numeric($v)){ $selected[$k] = (int)$v; }
				}
			}
		?>
		<?php foreach ($field->data['items'] as $item){ ?>
			<?php $checked = is_array($selected) && in_array((int)$item['id'], $selected, true); ?>
			<div class="iscp_item" data-sc-tip="<?php html($item['title']); ?>">
				<input type="checkbox" name="<?php html($field->element_name); ?>[]" id="cor<?php html($item['id']); ?>" value="<?php html($item['id']); ?>" <?php if ($checked){ echo 'checked="checked"'; } ?> />
				<label for="cor<?php html($item['id']); ?>" style="background:<?php html($item['color']); ?>"></label>
			</div>
		<?php } ?>
	<?php } ?>
</div>
<style>
	.is_colorPicker, .is_colorPicker * {-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
	.is_colorPicker{display: inline-block;}
	.is_colorPicker input{display: none}
	.is_colorPicker input:checked + label:before {
		content: '\f00c';
		display: block;
		position: absolute;
		font-family: 'fontawesome';
		top: 1px;
		left: 4px;
		font-size: 12px;
		color: #222;
		height: 18px;
		line-height: 18px;
		border: none;
	}
	.is_colorPicker label{
		display: inline-block !important;
		padding: 0!important;
		height: 20px;
		line-height: 0;
		width: 20px;
		cursor: pointer;
		position: relative;
	}
	.is_colorPicker .iscp_item{
		float: left;
		margin: 0 4px 4px 0;
		border: 1px solid #ddd;
		padding: 2px;
		height: 26px;
	}
</style>