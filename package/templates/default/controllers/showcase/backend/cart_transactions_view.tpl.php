<?php
	
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/bootstrap.min.css', false), false);
	$this->addCSS($this->getTplFilePath('controllers/showcase/backend/css/reset.css', false), false);
	$this->addBreadcrumb('Корзина', $this->href_to('cart'));
	$this->addBreadcrumb('Транзакции', $this->href_to('cart_transactions'));
	$this->addBreadcrumb('Транзакция №' . $transaction['id']);
	$this->setPageTitle('Транзакция №' . $transaction['id']);

?>
<div class="management">
	<?php echo $this->controller->renderHtmlSidebar('cart'); ?>
	<div class="page-content">

		<table class="sc_transaction_view">
			<tbody>
				<?php foreach ($transaction as $name => $val){ ?>
					<?php if (!isset($val) || $name == 'system_id'){ continue; } ?>
					<?php if ($name == 'history'){ ?>
						<tr class="sc_ol_item">
							<td class="sc_tli_title"><?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?></td>
							<td class="sc_tli_val">
								<ul>
									<?php 
										$history = cmsModel::yamlToArray($val);
										foreach ($history as $key => $v){
											echo '<li>' . $v . '</li>';
										}
									?>
								</ul>
							</td>
						</tr>
					<?php } else if ($name == 'errors'){ ?>
						<tr class="sc_ol_item">
							<td class="sc_tli_title"><?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?></td>
							<td class="sc_tli_val">
								<ul>
									<?php 
										$errors = cmsModel::yamlToArray($val);
										foreach ($errors as $key => $v){
											echo '<li>' . $v . '</li>';
										}
									?>
								</ul>
							</td>
						</tr>
					<?php } else if ($name == 'order_id'){ ?>
						<tr class="sc_ol_item">
							<td class="sc_tli_title"><?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?></td>
							<td class="sc_tli_val"><a href="<?php echo href_to('showcase', 'orders', $val) ?>" target="_blank"><?php html($val); ?></a></td>
						</tr>
					<?php } else if ($name == 'price'){ ?>
						<tr class="sc_ol_item">
							<td class="sc_tli_title"><?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?></td>
							<td class="sc_tli_val"><strong style="color:red"><?php echo cmsCore::getController('showcase')->getPriceFormat($val); ?></strong></td>
						</tr>
					<?php } else { ?>
						<tr class="sc_ol_item">
							<td class="sc_tli_title"><?php echo !empty($titles[$name]) ? $titles[$name] : $name; ?></td>
							<td class="sc_tli_val"><?php echo $val; ?></td>
						</tr>
					<?php } ?>
				<?php } ?>
			</tbody>
		</table>

	</div>
</div>
<style>
	.sc_transaction_view{
		width: 100%;
		border-collapse: separate;
		border-spacing: 2px;
		border-color: grey;
	}
	.sc_transaction_view tr td.sc_tli_title{
		background: #eee;
		border: 1px solid #ddd;
		padding: 5px 10px;
		width: 20%;
	}
	.sc_transaction_view tr td.sc_tli_val{
		border: 1px solid #eee;
		padding: 5px 10px;
	}
	.sc_transaction_view table{
		width: 100%;
		border-collapse: separate;
		border-spacing: 2px;
		border-color: grey;
	}
	.sc_transaction_view table td{
		border: 1px solid #eee;
		width: 50%;
		padding: 5px 10px;
	}

</style>
