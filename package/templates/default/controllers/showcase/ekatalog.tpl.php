<?php echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<yml_catalog date="<?php echo date("Y-m-d H:i:s"); ?>" >
	<shop>
		<name><?php html($list['name']); ?></name>
        <url><?php html($list['url']); ?></url>
		<?php if ($list['currencies']){ ?>
			<currencies>
				<?php foreach ($list['currencies'] as $currency){ ?>
					<currency id="<?php html($currency['currency']); ?>" rate="<?php html($currency['rate']); ?>" />
				<?php } ?>
			</currencies>
		<?php } ?>
		<?php if ($cats){ ?>
			<catalog>
				<?php foreach ($cats as $cat){ ?>
					<category id="<?php html($cat['id']); ?>" <?php if ($cat['ns_level'] > 1){ ?>parentId="<?php html($cat['parent_id']); ?>"<?php } ?>><?php html($cat['title']); ?></category>
				<?php } ?>
			</catalog>
		<?php } ?> 
		<?php if ($items){ ?>
			<items> 
				<?php foreach ($items as $item){ ?>
					<?php $item['ctype_name'] = $this->controller->ctype_name; ?>
					<?php if (empty($this->options['variants_off']) && !empty($item['variants'])){ ?>
						<?php $variants = is_array($item['variants']) ? $item['variants'] : cmsModel::yamlToArray($item['variants']); ?>
						<?php if ($variants){ ?>
							<?php foreach ($variants as $index => $variant){ ?>
								<?php if (empty($variations[$variant])){ continue; } else { $variant = $variations[$variant]; } ?>
								<item id="<?php echo $item['id'] . '000000' . $variant['id']; ?>"> 
								<name><?php html($variant['title']); ?></name>
								<url><?php echo href_to_abs($this->controller->ctype_name, $item['slug'] . '.html?variant=' . $variant['id']); ?></url> 
								<?php if (!empty($variant['sale'])){ ?>
									<price><?php echo !empty($variant['sale']) ? $variant['sale'] : 0; ?></price>
								<?php } else { ?>
									<price><?php echo !empty($variant['price']) ? $variant['price'] : 0; ?></price>
								<?php } ?>
								<categoryId><?php html($item['category_id']); ?></categoryId>
								<?php if ($list['relateds']){ ?>
									<?php foreach ($list['relateds'] as $key => $val){ ?>
										<?php if ($key != 'vendor'){ continue; } ?>
										<?php if ($val){ ?>
											<?php if (is_numeric($val)){ ?>
												<?php
													if (empty($props[$val]) || empty($item['sc_props'][$val])){ continue; }
													$values = cmsModel::yamlToArray($props[$val]['values']);
													if (empty($values[$item['sc_props'][$val]])){ continue; }
												?>
												<<?php html($key); ?>><?php html(strip_tags($values[$item['sc_props'][$val]])); ?></<?php html($key); ?>>
											<?php } else if (!empty($fields[$val]) && !empty($item[$val])){ ?>
												<<?php html($key); ?>><?php html(strip_tags($fields[$val]['handler']->setItem($item)->parse($item[$val]))); ?></<?php html($key); ?>>
											<?php } else if($val == 'cat_name'){ ?>
												<typePrefix><?php echo $cats[$item['category_id']]['title']; ?></typePrefix>
											<?php } ?>
										<?php } ?>
									<?php } ?>
								<?php } ?>
								<?php if (!empty($variant['photo'])){ ?>
									<image><?php echo $this->controller->cms_config->host . html_image_src($variant['photo'], 'big', true); ?></image>
								<?php } ?> 
								<?php if (!empty($item['content'])){ ?>
									<description>
										<![CDATA[
											<?php echo str_ireplace(array('<', '>', '&', '\'', '"'), array('&lt;', '&gt;', '&amp;', '&apos;', '&quot;'), strip_tags(str_replace(array('&mdash;', '&nbsp;'), array('-', ' '), $item['content']))); ?>
										]]>
									</description>
								<?php } ?>
								</item> 
							<?php } ?>
						<?php } ?>
					<?php } else { ?>
						<item id="<?php html($item['id']); ?>"> 
							<name><?php html($item['title']); ?></name> 
							<url><?php echo href_to_abs($this->controller->ctype_name, $item['slug'] . '.html'); ?></url>
							<?php if (!empty($item['sale'])){ ?>
								<price><?php echo !empty($item['sale']) ? $item['sale'] : 0; ?></price>
							<?php } else { ?>
								<price><?php echo !empty($item['price']) ? $item['price'] : 0; ?></price>
							<?php } ?>
							<categoryId><?php html($item['category_id']); ?></categoryId>
							<?php if (!empty($item['photo'])){ ?>
								<image><?php echo $this->controller->cms_config->host . html_image_src($item['photo'], 'big', true); ?></image>
							<?php } ?>
							<?php if ($list['relateds']){ ?>
								<?php foreach ($list['relateds'] as $key => $val){ ?>
									<?php if ($key != 'vendor'){ continue; } ?>
									<?php if ($val){ ?>
										<?php if (is_numeric($val)){ ?>
											<?php
												if (empty($props[$val]) || empty($item['sc_props'][$val])){ continue; }
												$values = cmsModel::yamlToArray($props[$val]['values']);
												if (empty($values[$item['sc_props'][$val]])){ continue; }
											?>
											<<?php html($key); ?>><?php html(strip_tags($values[$item['sc_props'][$val]])); ?></<?php html($key); ?>>
										<?php } else if (!empty($fields[$val]) && !empty($item[$val])){ ?>
											<<?php html($key); ?>><?php html(strip_tags($fields[$val]['handler']->setItem($item)->parse($item[$val]))); ?></<?php html($key); ?>>
										<?php } else if($val == 'cat_name'){ ?>
											<typePrefix><?php echo $cats[$item['category_id']]['title']; ?></typePrefix>
										<?php } ?>
									<?php } ?>
								<?php } ?>
							<?php } ?>
							<?php if (!empty($item['content'])){ ?>
								<description>
									<![CDATA[
										<?php echo str_ireplace(array('<', '>', '&', '\'', '"'), array('&lt;', '&gt;', '&amp;', '&apos;', '&quot;'), strip_tags(str_replace(array('&mdash;', '&nbsp;'), array('-', ' '), $item['content']))); ?>
									]]>
								</description>
							<?php } ?>
						</item> 
					<?php } ?>
				<?php } ?>
			</items>
		<?php } ?>
	</shop>
</yml_catalog>