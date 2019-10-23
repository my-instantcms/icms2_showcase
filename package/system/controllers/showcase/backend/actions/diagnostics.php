<?php

class actionShowcaseDiagnostics extends cmsAction {

    public function run(){

		$paid = $this->model->getItemByField('sc_cart_fields', 'name', 'paid');

		$price_field = $this->model->db->query("SHOW COLUMNS FROM `{#}sc_variations` LIKE 'price'");
		$price_float = false;
		while($data = $this->model->db->fetchAssoc($price_field)){
			if ($data['Field'] != 'price'){ continue; }
            $price_float = ($data['Type'] == 'float') ? true : false;
        }
		
		$v_ordering = $this->model->db->isFieldExists('sc_variations', 'ordering');
		$v_sale = $this->model->db->isFieldExists('sc_variations', 'sale');
		$v_seo_keys = $this->model->db->isFieldExists('sc_variations', 'seo_keys');
		$v_seo_desc = $this->model->db->isFieldExists('sc_variations', 'seo_desc');
		$v_seo_title = $this->model->db->isFieldExists('sc_variations', 'seo_title');

		$receipt = $this->model->db->isFieldExists('sc_pay_systems', 'receipt');
		$tax = $this->model->db->isFieldExists('sc_pay_systems', 'tax');
		$nds = $this->model->db->isFieldExists('sc_pay_systems', 'nds');
		
		$sc_tabs = $this->model->db->query("SELECT id FROM {#}sc_tabs", false, true) ? 1 : 0;

		$sc_aggregators = $this->model->db->query("SELECT id FROM {#}sc_aggregators", false, true) ? 1 : 0;
		$yml = $this->model->getItemByField('scheduler_tasks', 'hook', 'yml');
		
		return $this->cms_template->render('backend/diagnostics', array(
            'paid' => $paid,
            'price_float' => $price_float,
            'v_ordering' => $v_ordering,
            'v_sale' => $v_sale,
            'v_seo_keys' => $v_seo_keys,
            'v_seo_desc' => $v_seo_desc,
            'v_seo_title' => $v_seo_title,
            'receipt' => $receipt,
            'tax' => $tax,
            'nds' => $nds,
            'sc_tabs' => $sc_tabs,
            'sc_aggregators' => $sc_aggregators,
            'yml' => $yml,
        ));
		
    }
	
}