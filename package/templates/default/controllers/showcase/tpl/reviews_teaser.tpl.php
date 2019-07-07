<?php
	$tpl->addCSS($tpl->getTplFilePath('controllers/recommends/css/rateit.css', false));
	$tpl->addJs($tpl->getTplFilePath('controllers/recommends/js/jquery.rateit.min.js', false));
	$count = 0;
	$total = 0;
	if ($recommends){
		$recoms = is_array($recommends) ? $recommends : cmsModel::yamlToArray($recommends);
		$total = $total ? $total : (!empty($recoms['count']) ? $recoms['count'] : 0);
		$summ = !empty($recoms['summ']) ? $recoms['summ'] : 0;
		$summ_1 = !empty($recoms['summ_1']) ? $recoms['summ_1'] : 0;
		$summ_2 = !empty($recoms['summ_2']) ? $recoms['summ_2'] : 0;
		$summ_3 = !empty($recoms['summ_3']) ? $recoms['summ_3'] : 0;
		$summ_4 = !empty($recoms['summ_4']) ? $recoms['summ_4'] : 0;
		$summ_5 = !empty($recoms['summ_5']) ? $recoms['summ_5'] : 0;
		$summ_all = $summ_1 + $summ_2 + $summ_3 + $summ_4 + $summ_5;
		$count = $summ ? number_format($summ / $summ_all, 1) : 0;
	}
?>
<div class="miw_reviews dsct_top_left" data-tc-tip="<?php echo html_spellcount($total, LANG_RECOMMENDS_SPELL); ?>">
	<div class="rateit miw_reviews_total_rate" data-rateit-value="<?php html($count); ?>" data-rateit-ispreset="true" data-rateit-readonly="true" style="vertical-align:sub"></div> 
</div>