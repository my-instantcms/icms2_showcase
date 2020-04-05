<?php
	$user = cmsUser::getInstance();
	$showcase = cmsCore::getController('showcase');
	$this->addCSS($this->getTplFilePath('controllers/showcase/css/item_view.css', false));
	$allFields = $fields;
	$myFields = array();
	$position_1 = array();
	$position_2 = array();
	$position_5 = array();
	$tabs_list = array();
	$tabs_active = array();
	if (!empty($ctype['sc_tabs'])){
		foreach ($ctype['sc_tabs'] as $tabId => $scTab){
			if (current($ctype['sc_tabs']) == $scTab){ $tabs_active = $tabId; }
			if ($scTab['type'] == 'fields'){ 
				$tFields = cmsModel::yamlToArray($scTab['fields']);
				$scTab['fields'] = array();
				if ($tFields){
					foreach ($tFields as $tfID => $tfVal){
						if (!empty($fields[$tfVal])){
							$scTab['fields'][$tfVal] = $fields[$tfVal];
							unset($fields[$tfVal]);
						} else if($tfVal == 'sc_tag_list'){
							$scTab['fields']['sc_tag_list'] = 1;
						} else if($tfVal == 'sc_prop_list'){
							$scTab['fields']['sc_prop_list'] = 1;
						}
					}
				}
			}
			$tabs_list[$tabId] = $scTab;
		}
	}
	if (!empty($fields)) {
		foreach ($fields as $myId => $myField) {
			
			if (!empty($myField['options']['sc_position'])){
				if ($myField['options']['sc_position'] == 1){
					$position_1[$myId] = $myField;
					unset($fields[$myId]);
				} else if($myField['options']['sc_position'] == 2){
					$position_2[$myId] = $myField;
					unset($fields[$myId]);
				} else if($myField['options']['sc_position'] == 5){
					$position_5[$myId] = $myField;
					unset($fields[$myId]);
				}
			}

		}
	}
?>

<?php $this->block('top_showcase_item'); ?>

<?php if (!empty($fields['title']['is_in_item'])){ ?>
    <h1>
        <?php html($item['title']); ?>
        <?php if ($item['is_private']) { ?>
            <span class="is_private" title="<?php html(LANG_PRIVACY_HINT); ?>"></span>
        <?php } ?>
    </h1>
    <?php if ($item['parent_id'] && !empty($ctype['is_in_groups'])){ ?>
        <h2 class="parent_title item_<?php echo $item['parent_type']; ?>_title">
            <a href="<?php echo rel_to_href($item['parent_url']); ?>"><?php html($item['parent_title']); ?></a>
        </h2>
    <?php } ?>
    <?php unset($fields['title']); ?>
<?php } ?>
<?php if (empty($ctype['showcase']['hide_artikul']) && !empty($item['artikul'])){ ?>
	<div id="sc_goods_artikul">Артикул: <b><?php html($item['artikul']); ?></b></div>
<?php } ?>

<?php if ($this->hasMenu('item-menu')){ ?>
	<div id="content_item_tabs">
		<div class="tabs-menu">
			<?php $this->menu('item-menu', true, 'tabbed'); ?>
		</div>
	</div>
<?php } ?>

<div class="content_item <?php echo $ctype['name']; ?>_item sc_item_view" itemscope itemtype="http://schema.org/Product">
	
	<meta content="<?php html($item['title']); ?>" itemprop="name" />
	<?php if (empty($ctype['showcase']['hide_artikul']) && !empty($item['artikul'])){ ?>
		<meta content="<?php html($item['artikul']); ?>" itemprop="sku" />
	<?php } ?>

	<div class="sc_item_view_header">
		<div class="sc_item_view_loader"><img src="/templates/default/controllers/showcase/img/ajax-loader.gif" /></div>
		<?php 
			if (!empty($item['photo']) && $fields['photo']['is_in_item'] && $user->isInGroups($fields['photo']['groups_read'])){
				$this->renderControllerChild('showcase', 'tpl/photo', array(
					'item' => $item,
					'photo' => $fields['photo'],
					'user' => $user,
					'ctype_name' => $ctype['name'],
					'showcase' => $showcase
				));
				unset($fields['photo']);
			}
		?>
		
		<div class="sc_right_box" id="item_<?php html($item['id']); ?>">
			<?php if ($fields['price']['is_in_item'] && $user->isInGroups($fields['price']['groups_read'])){ ?>
				<?php echo $fields['price']['handler']->setItem($item)->parse($item['price']); ?>
				<?php unset($fields['price']); ?>
			<?php } ?>
			<?php if (!empty($item['variations'])){ ?>
				<div class="sc_variations_select <?php if (!empty($ctype['showcase']['variants_opened'])){ ?>variants_opened<?php } ?>">
					<select class="makeMeFancy">
						<option value="0" selected="selected" data-skip="1">Выберите вариант</option>
						<?php foreach ($item['variations'] as $v => $variant){ ?>
							<?php 
								$selected = ($v == $item['seted_variant']) ? 'selected' : '';
								$disabled = ($variant['in'] <= 0) ? 'disabled' : '';
								$attached = !empty($variant['attached']) ? 'data-attached="1"' : 'data-attached="0"';
							?>
							<option value="<?php html($v); ?>" data-icon="<?php echo !empty($variant['photo']) ? html_image_src($variant['photo'], 'small', true) : '/templates\default/controllers/showcase/img/nophoto.png'; ?>" <?php html($selected . ' ' . $disabled); ?> <?php html($attached); ?>><?php html($variant['title']); ?></option>
						<?php } ?>
					</select>
				</div>
				<script src="/<?php html($this->getTplFilePath('controllers/showcase/js/makeMeFancy.js', false)); ?>"></script>
			<?php } ?>
			<div class="sc_right_footer">
				<script>
					var cart_data = {};
					var preorder_data = {};
					<?php if (!empty($ctype['showcase']['price_format']) && $ctype['showcase']['price_format'] == 2){ ?>
						icms.showcase.price_round = false;
					<?php } ?>
				</script>
				<?php if (!empty($fields['in_stock']) && !empty($fields['in_stock']['is_in_item'])){ ?>
					<div class="sc_buy_qty" data-tc-tip="Количество">
						<div class="sc_qty_count"><input type="text" value="<?php echo ((isset($item['in_stock']) && $item['in_stock'] > 0) || (!empty($item['variant_in']) && $item['variant_in'] != 'none')) ? 1 : 0; ?>"></div>
						<button class="sc_qty_btn_plus" onClick="icms.showcase.scSetQty(this, '<?php html($item['id']); ?>', false)" <?php if (empty($ctype['showcase']['off_inctock'])  && isset($item['in_stock']) || empty($ctype['showcase']['off_inctock']) && !empty($item['variant_in'])){ ?>data-max="<?php echo (!empty($item['variant_in']) && $item['variant_in'] != 'none') ? $item['variant_in'] : (isset($item['in_stock']) ? $item['in_stock'] : 0); ?>"<?php } ?>><i class="fa fa-plus fa-fw"></i></button>
						<button class="sc_qty_btn_minus" onClick="icms.showcase.scSetQty(this, '<?php html($item['id']); ?>', false)"><i class="fa fa-minus fa-fw"></i></button>
					</div>
				<?php } ?>
				<?php 
					$this->renderControllerChild('showcase', 'tpl/addToCart', array(
						'item' => $item,
						'fields' => $fields,
						'user' => $user,
						'ctype' => $ctype
					));
				?>
				<?php if ($position_1){ ?>
					<?php foreach ($position_1 as $p1_name => $p1_field){ ?>
						<?php if ((empty($item[$p1_name]) || empty($p1_field['html'])) && $item[$p1_name] !== '0' || !$p1_field['is_in_item']){ continue; } ?>
						<div class="field ft_<?php echo $p1_field['type']; ?> f_<?php echo $p1_name; ?> <?php echo $p1_field['options']['wrap_type']; ?>_field" <?php if($p1_field['options']['wrap_width']){ ?> style="width: <?php echo $p1_field['options']['wrap_width']; ?>;"<?php } ?>>
							<?php if ($p1_field['options']['label_in_item'] != 'none') { ?>
								<div class="title_<?php echo $p1_field['options']['label_in_item']; ?>"><?php html($p1_field['title']); ?>: </div>
							<?php } ?>
							<div class="value"><?php echo $p1_field['html']; ?></div>
						</div>
					<?php } ?>
				<?php } ?>
			</div>
			<?php if ($position_2){ ?>
				<?php foreach ($position_2 as $p2_name => $p2_field){ ?>
					<?php if ((empty($item[$p2_name]) || empty($p2_field['html'])) && $item[$p2_name] !== '0' || !$p2_field['is_in_item']){ continue; } ?>
					<?php if ($p2_field['groups_read'] && !$user->isInGroups($p2_field['groups_read'])) { continue; } ?>
					<div class="field ft_<?php echo $p2_field['type']; ?> f_<?php echo $p2_name; ?> <?php echo $p2_field['options']['wrap_type']; ?>_field" <?php if($p2_field['options']['wrap_width']){ ?> style="width: <?php echo $p2_field['options']['wrap_width']; ?>;"<?php } ?>>
						<?php if ($p2_field['options']['label_in_item'] != 'none') { ?>
							<div class="title_<?php echo $p2_field['options']['label_in_item']; ?>"><?php html($p2_field['title']); ?>: </div>
						<?php } ?>
						<div class="value"><?php echo $p2_field['html']; ?></div>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
	</div>

    <?php if (!empty($fields)) { ?>

        <?php $fields_fieldsets = cmsForm::mapFieldsToFieldsets($fields, function($field, $user) use ($item) {
            if (!$field['is_in_item'] || $field['is_system']) { return false; }
            if ((empty($item[$field['name']]) || empty($field['html'])) && $item[$field['name']] !== '0') { return false; }
            if ($field['groups_read'] && !$user->isInGroups($field['groups_read'])) { return false; }
            return true;
        } ); ?>
		<div class="sc_item_fields">
        <?php foreach ($fields_fieldsets as $fieldset_id => $fieldset) { ?>

            <?php $is_fields_group = !empty($ctype['options']['is_show_fields_group']) && $fieldset['title']; ?>

            <?php if ($is_fields_group) { ?>
                <div class="fields_group fields_group_<?php echo $ctype['name']; ?>_<?php echo $fieldset_id ?>">
                    <h3 class="group_title"><?php html($fieldset['title']); ?></h3>
            <?php } ?>

            <?php if (!empty($fieldset['fields'])) { ?>
                <?php foreach ($fieldset['fields'] as $field) { ?>

                    <div class="field ft_<?php echo $field['type']; ?> f_<?php echo $field['name']; ?> <?php echo $field['options']['wrap_type']; ?>_field" <?php if($field['options']['wrap_width']){ ?> style="width: <?php echo $field['options']['wrap_width']; ?>;"<?php } ?>>
                        <?php if ($field['options']['label_in_item'] != 'none') { ?>
                            <div class="title_<?php echo $field['options']['label_in_item']; ?>"><?php html($field['title']); ?>: </div>
                        <?php } ?>
                        <div class="value"><?php echo $field['html']; ?></div>
                    </div>

                <?php } ?>
            <?php } ?>

            <?php if ($is_fields_group) { ?></div><?php } ?>

        <?php } ?>
		</div>
    <?php } ?>
	<?php if ($tabs_list){ ?>
		<div class="widget_tabbed">
			<div class="tabs">
				<ul class="tab_count_<?php echo count($tabs_list); ?>">
					<?php foreach ($tabs_list as $tlID => $tlTab){ ?>
						<li class="tab">
							<a <?php if ($tabs_active == $tlID){ ?>class="active"<?php } ?> data-id="<?php html($tlID); ?>">
								<?php if (!empty($tlTab['icon'])){ ?>
									<i class="fa <?php html($tlTab['icon']); ?>"></i> 
									<span><?php echo $tlTab['title']; ?></span>
								<?php } else { ?>
									<?php echo $tlTab['title']; ?>
								<?php } ?>
							</a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="widgets">
				<?php foreach ($tabs_list as $tlID => $tlTab){ ?>
					<div id="widget-<?php html($tlID); ?>" class="body" style="display: <?php if ($tabs_active == $tlID){ ?>block<?php } else { ?>none<?php } ?>;">
						<?php if ($tlTab['type'] == 'fields' && !empty($tlTab['fields'])){ ?>
							<?php foreach ($tlTab['fields'] as $tlTab_name => $tlTab_field){ ?>
								<?php if ($tlTab_name == 'sc_tag_list' || $tlTab_name == 'sc_prop_list'){ continue; } ?>
								<?php if ((empty($item[$tlTab_name]) || empty($tlTab_field['html'])) && $item[$tlTab_name] !== '0' || !$tlTab_field['is_in_item']){ continue; } ?>
								<?php if ($tlTab_field['groups_read'] && !$user->isInGroups($tlTab_field['groups_read'])) { continue; } ?>
								<div class="field ft_<?php echo $tlTab_field['type']; ?> f_<?php echo $tlTab_name; ?> <?php echo $tlTab_field['options']['wrap_type']; ?>_field" <?php if($tlTab_field['options']['wrap_width']){ ?> style="width: <?php echo $tlTab_field['options']['wrap_width']; ?>;"<?php } ?>>
									<?php if ($tlTab_field['options']['label_in_item'] != 'none') { ?>
										<div class="title_<?php echo $tlTab_field['options']['label_in_item']; ?>"><?php html($tlTab_field['title']); ?>: </div>
									<?php } ?>
									<div class="value"><?php echo $tlTab_field['html']; ?></div>
								</div>
							<?php } ?>
							<?php if (!empty($tlTab['fields']['sc_tag_list']) && $ctype['is_tags'] && !empty($ctype['options']['is_tags_in_item']) &&  $item['tags']){ ?>
								<div class="tags_bar">
									<?php echo html_tags_bar($item['tags'], 'content-'.$ctype['name']); ?>
								</div>
								<a href="javascript:void(0);" class="sc_tag_add" onClick="icms.showcase.addTags(this, <?php html($item['id']); ?>)">+ добавить тег</a>
							<?php } ?>
							<?php if (!empty($tlTab['fields']['sc_prop_list']) && $props && array_filter((array)$props_values)){ ?>
								<div class="content_item_props <?php echo $ctype['name']; ?>_item_props">
									<table>
										<tbody>
										<?php if ($props && array_filter((array)$props_values)) { ?>
											<?php
												$props_fields = $this->controller->getPropsFields($props);
												$props_fieldsets = cmsForm::mapFieldsToFieldsets($props);
											?>
											<?php foreach($props_fieldsets as $fieldset_id => $fieldset){ ?>
												<?php if ($fieldset['title']){ ?>
													<tr class="props_groups props_group_<?php echo $ctype['name']; ?>_<?php echo $fieldset_id ?>">
														<td class="heading" colspan="2"><?php html($fieldset['title']); ?></td>
													</tr>
												<?php } ?>
												<?php if ($fieldset['fields']){ ?>
													<?php foreach($fieldset['fields'] as $prop){ ?>
														<?php if (isset($props_values[$prop['id']])) { ?>
														<?php $prop_field = $props_fields[$prop['id']]; ?>
															<tr class="prop_wrap prop_<?php echo $prop['type']; ?>">
																<td class="title"><?php html($prop['title']); ?></td>
																<td class="value">
																	<?php echo $prop_field->setItem($item)->parse($props_values[$prop['id']]); ?>
																</td>
															</tr>
														<?php } ?>
													<?php } ?>
												<?php } ?>
											<?php } ?>
										<?php } ?>
										</tbody>
									</table>
								</div>
							<?php } ?>
						<?php } else if (!empty($tlTab['text'])){ ?>
							<?php 
							if (mb_stripos($tlTab['text'], '{')) {
								preg_match_all('/{(.*?)}/', $tlTab['text'], $matches);
								$phrase = array();
								$healthy = array();
								if (!empty($matches[1])){
									foreach ($matches[1] as $m_key => $match){
										if (!empty($allFields[$match])){
											$phrase[] = '{' . $match . '}';
											$healthy[] = $allFields[$match]['html'];
										} else if(!empty($item[$match])){
											$phrase[] = '{' . $match . '}';
											$healthy[] = $item[$match];
										}
									}
								}
							?>
								<div class="sc_tab_textBox"><?php echo str_replace($phrase, $healthy, nl2br($tlTab['text'])); ?></div>
							<?php } else { ?>
								<div class="sc_tab_textBox"><?php echo $tlTab['text']; ?></div>
							<?php } ?>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	
	<div class="sc_position_5_fields">
	<?php if ($position_5){ ?>
		<?php foreach ($position_5 as $p5_name => $p5_field){ ?>
			<?php if ((empty($item[$p5_name]) || empty($p5_field['html'])) && $item[$p5_name] !== '0'){ continue; } ?>
			<?php if ($p5_field['groups_read'] && !$user->isInGroups($p5_field['groups_read'])) { continue; } ?>
			<div class="field ft_<?php echo $p5_field['type']; ?> f_<?php echo $p5_name; ?> <?php echo $p5_field['options']['wrap_type']; ?>_field" <?php if($p5_field['options']['wrap_width']){ ?> style="width: <?php echo $p5_field['options']['wrap_width']; ?>;"<?php } ?>>
				<?php if ($p5_field['options']['label_in_item'] != 'none') { ?>
					<div class="title_<?php echo $p5_field['options']['label_in_item']; ?>"><?php html($p5_field['title']); ?>: </div>
				<?php } ?>
				<div class="value"><?php echo $p5_field['html']; ?></div>
			</div>
		<?php } ?>
	<?php } ?>
	</div>

    <?php
        $hooks_html = cmsEventsManager::hookAll("content_{$ctype['name']}_item_html", $item);
        if ($hooks_html) { echo html_each($hooks_html); }
    ?>

    <?php if ($ctype['item_append_html']){ ?>
        <div class="append_html"><?php echo $ctype['item_append_html']; ?></div>
    <?php } ?>

    <?php if (!empty($item['info_bar'])){ ?>
        <div class="info_bar">
            <?php foreach($item['info_bar'] as $bar){ ?>
                <div class="bar_item <?php echo !empty($bar['css']) ? $bar['css'] : ''; ?>" title="<?php html(!empty($bar['title']) ? $bar['title'] : ''); ?>">
                    <?php if (!empty($bar['href'])){ ?>
                        <a href="<?php echo $bar['href']; ?>"><?php echo $bar['html']; ?></a>
                    <?php } else { ?>
                        <?php echo $bar['html']; ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</div>

<?php $this->block('bottom_showcase_item'); ?>