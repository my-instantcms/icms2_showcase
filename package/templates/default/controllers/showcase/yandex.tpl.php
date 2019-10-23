<?php echo '<?xml version="1.0" encoding="utf-8" ?>'; ?>
<!DOCTYPE yml_catalog SYSTEM "shops.dtd">
<yml_catalog date="<?php echo date("Y-m-d H:i:s"); ?>">
    <shop>
        <name><?php html($list['name']); ?></name>
        <company><?php html($list['company']); ?></company>
        <url><?php html($list['url']); ?></url>
		<platform>InstantCMS</platform>

		<?php if ($list['currencies']){ ?>
			<currencies>
				<?php foreach ($list['currencies'] as $currency){ ?>
					<currency id="<?php html($currency['currency']); ?>" rate="<?php html($currency['rate']); ?>" />
				<?php } ?>
			</currencies>
		<?php } ?>
		
		<?php if ($cats){ ?>
			<categories>
				<?php foreach ($cats as $cat){ ?>
					<category id="<?php html($cat['id']); ?>" <?php if ($cat['ns_level'] > 1){ ?>parentId="<?php html($cat['parent_id']); ?>"<?php } ?>><?php html($cat['title']); ?></category>
				<?php } ?>
			</categories>
		<?php } ?>
		
		<?php if ($list['delivery']){ ?>
			<delivery-options>
				<option cost="<?php html($list['cost']); ?>" days="<?php html($list['days']); ?>" />
			</delivery-options>
		<?php } ?>

		<?php if ($list['pickup']){ ?>
			<pickup-options>
				<option cost="<?php html($list['pickup_cost']); ?>" days="<?php html($list['pickup_days']); ?>" />
			</pickup-options>
		<?php } ?>

		<?php if ($items){ ?>
			<offers>
				<?php foreach ($items as $item){ ?>
					<?php $item['ctype_name'] = $this->controller->ctype_name; ?>
					<?php if (empty($this->options['variants_off']) && !empty($item['variants'])){ ?>
						<?php $variants = is_array($item['variants']) ? $item['variants'] : cmsModel::yamlToArray($item['variants']); ?>
						<?php if ($variants){ ?>
							<?php foreach ($variants as $index => $variant){ ?>
								<?php if (empty($variations[$variant])){ continue; } else { $variant = $variations[$variant]; } ?>
								<offer id="<?php echo $item['id'] . '000000' . $variant['id']; ?>" type="vendor.model">
									<url><?php echo href_to_abs($this->controller->ctype_name, $item['slug'] . '.html?variant=' . $variant['id']); ?></url>
									<?php if (!empty($variant['sale'])){ ?>
										<price><?php echo !empty($variant['sale']) ? $variant['sale'] : 0; ?></price>
										<oldprice><?php echo !empty($variant['price']) ? $variant['price'] : 0; ?></oldprice>
									<?php } else { ?>
										<price><?php echo !empty($variant['price']) ? $variant['price'] : 0; ?></price>
									<?php } ?>
									<currencyId><?php echo !empty($list['currency']) ? $list['currency'] : 'RUR'; ?></currencyId>
									<categoryId type="Own"><?php html($item['category_id']); ?></categoryId>
									<?php if (!empty($variant['photo'])){ ?>
										<picture><?php echo $this->controller->cms_config->host . html_image_src($variant['photo'], 'big', true); ?></picture>
									<?php } ?>
									<delivery><?php echo !empty($list['delivery']) ? 'true' : 'false'; ?></delivery>
									<pickup><?php echo !empty($list['pickup']) ? 'true' : 'false'; ?></pickup> 
									<store><?php echo !empty($list['store']) ? 'true' : 'false'; ?></store>
									<?php if ($list['relateds']){ ?>
										<?php foreach ($list['relateds'] as $key => $val){ ?>
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
									<?php if ($list['fields']){ ?>
										<?php foreach ($list['fields'] as $index => $field_name){ ?>
											<?php if (is_numeric($field_name)){ ?>
												<?php
													if (empty($props[$field_name]) || empty($item['sc_props'][$field_name])){ continue; }
													$values = cmsModel::yamlToArray($props[$field_name]['values']);
													if (empty($values[$item['sc_props'][$field_name]])){ continue; }
												?>
												<param name="<?php echo $props[$field_name]['title']; ?>">
													<?php html(strip_tags($values[$item['sc_props'][$field_name]])); ?>
												</param>
											<?php } else if (!empty($fields[$field_name]) && !empty($item[$field_name])){ ?>
												<param name="<?php echo $fields[$field_name]['title']; ?>">
													<?php html(strip_tags($fields[$field_name]['handler']->setItem($item)->parse($item[$field_name]))); ?>
												</param>
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
								</offer>
							<?php } ?>
						<?php } ?>
					<?php } else { ?>
						<offer id="<?php html($item['id']); ?>" type="vendor.model">
							<url><?php echo href_to_abs($this->controller->ctype_name, $item['slug'] . '.html'); ?></url>
							<?php if (!empty($item['sale'])){ ?>
								<price><?php echo !empty($item['sale']) ? $item['sale'] : 0; ?></price>
								<oldprice><?php echo !empty($item['price']) ? $item['price'] : 0; ?></oldprice>
							<?php } else { ?>
								<price><?php echo !empty($item['price']) ? $item['price'] : 0; ?></price>
							<?php } ?>
							<currencyId><?php echo !empty($list['currency']) ? $list['currency'] : 'RUR'; ?></currencyId>
							<categoryId type="Own"><?php html($item['category_id']); ?></categoryId>
							<?php if (!empty($item['photo'])){ ?>
								<picture><?php echo $this->controller->cms_config->host . html_image_src($item['photo'], 'big', true); ?></picture>
							<?php } ?>
							<delivery><?php echo !empty($list['delivery']) ? 'true' : 'false'; ?></delivery>
							<pickup><?php echo !empty($list['pickup']) ? 'true' : 'false'; ?></pickup> 
							<store><?php echo !empty($list['store']) ? 'true' : 'false'; ?></store>
							<?php if ($list['relateds']){ ?>
								<?php foreach ($list['relateds'] as $key => $val){ ?>
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
							<?php if ($list['fields']){ ?>
								<?php foreach ($list['fields'] as $index => $field_name){ ?>
									<?php if (is_numeric($field_name)){ ?>
										<?php
											if (empty($props[$field_name]) || empty($item['sc_props'][$field_name])){ continue; }
											$values = cmsModel::yamlToArray($props[$field_name]['values']);
											if (empty($values[$item['sc_props'][$field_name]])){ continue; }
										?>
										<param name="<?php echo $props[$field_name]['title']; ?>">
											<?php html(strip_tags($values[$item['sc_props'][$field_name]])); ?>
										</param>
									<?php } else if (!empty($fields[$field_name]) && !empty($item[$field_name])){ ?>
										<param name="<?php echo $fields[$field_name]['title']; ?>">
											<?php html(strip_tags($fields[$field_name]['handler']->setItem($item)->parse($item[$field_name]))); ?>
										</param>
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
						</offer>
					<?php } ?>
				<?php } ?>
			</offers>
		<?php } ?>
    </shop>
</yml_catalog>