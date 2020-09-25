<?php 
class modelShowcase extends cmsModel{

	public function getData($table, $id = false, $one = false, $coll = false, $key = 'id'){
        if ($id){
			$this->useCache("{$this->name}.{$table}.{$id}");
		} else {
			$this->useCache("{$this->name}.{$table}");
		}
        return $id ? $this->getItemById($table, $id) : ($one ? $this->getItem($table) : $this->get($table, $coll, $key));
    }
	
	public function getDataCount($table, $reset = true){
		$this->useCache("{$this->name}.{$table}");
		$count = $this->getCount($table);
		if ($reset){ $this->resetFilters(); }
		return $count;
	}
	
	public function saveData($table, $data){
        cmsCache::getInstance()->clean("{$this->name}.{$table}");
        return $this->insert($table, $data);
    }
	
	public function updData($table, $id, $data){
		cmsCache::getInstance()->clean("{$this->name}.{$table}");
		cmsCache::getInstance()->clean("{$this->name}.{$table}.{$id}");
        return $this->update($table, $id, $data);
    }
	
	public function deleteData($table, $id, $by_field = 'id'){
		cmsCache::getInstance()->clean("{$this->name}.{$table}");
        cmsCache::getInstance()->clean("{$this->name}.{$table}.{$id}");
        return $this->delete($table, $id, $by_field);
    }

	public function reorderData($table, $items){
		cmsCache::getInstance()->clean("{$this->name}.{$table}");
        return $this->reorderByList($table, $items);
    }
	
	public function getGoods($table, $id = false, $one = false, $coll = false, $key = 'id'){
        if ($id){
			$this->useCache("content.item.{$table}");
		} else {
			$this->useCache("content.list.{$table}");
		}
		$table = 'con_' . $table;
        return $id ? $this->getItemById($table, $id) : ($one ? $this->getItem($table) : $this->get($table, $coll, $key));
    }
	
	public function editGoods($table, $id, $data){
        cmsCache::getInstance()->clean('content.list.' . $table);
        cmsCache::getInstance()->clean('content.item.' . $table);
		$table = 'con_' . $table;
        return $this->update($table, $id, $data);
    }

	public function getCashPaySystem(){
        return array(
			'id' => 0,
			'gateway_name' => 'cash',
			'title' => 'Наличные',
			'hint' => '',
			'icon' => '/templates/default/controllers/showcase/img/cash.png',
			'wallet_id' => '',
			'currency' => '',
			'secret_key' => '',
			'pay_type' => '',
			'redirect_success' => '',
			'redirect_fail' => '',
			'is_test' => 0,
			'is_pub' => 1,
			'ordering' => 0
		);
    }
	
	public function getCheckPaySystem(){
        return array(
			'id' => 999,
			'gateway_name' => 'check',
			'title' => 'По реквизитам',
			'hint' => '',
			'icon' => '/templates/default/controllers/showcase/img/check.png',
			'wallet_id' => '',
			'currency' => '',
			'secret_key' => '',
			'pay_type' => '',
			'redirect_success' => '',
			'redirect_fail' => '',
			'is_test' => 0,
			'is_pub' => 1,
			'ordering' => 0
		);
    }
	
	public function curl_get_contents($url, $post_data = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_REFERER, "http://google.com");
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; ru; rv:1.9.0.1) Gecko/2008070208');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		if($post_data){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	public function deleteController($id){
		
		$widget = $this->getItemByField('widgets', 'controller', 'showcase');
		if($widget){
			$this->delete('widgets_bind', $widget['id'], 'widget_id');
			$this->delete('widgets', $widget['id']);
		}

		$this->db->dropTable('sc_aggregators');
		$this->db->dropTable('sc_cart_delivery');
		$this->db->dropTable('sc_cart_fields');
		$this->db->dropTable('sc_checkouts');
		$this->db->dropTable('sc_colors');
		$this->db->dropTable('sc_logs');
		$this->db->dropTable('sc_pay_gateways');
		$this->db->dropTable('sc_pay_systems');
		$this->db->dropTable('sc_sales');
		$this->db->dropTable('sc_steps');
		$this->db->dropTable('sc_tabs');
		$this->db->dropTable('sc_todo');
		$this->db->dropTable('sc_todo_items');
		$this->db->dropTable('sc_transactions');
		$this->db->dropTable('sc_variations');

		$this->filterEqual('controller', 'showcase')->deleteFiltered('users_tabs');
		$this->filterEqual('controller', 'showcase')->deleteFiltered('scheduler_tasks');
		
		return parent::deleteController($id);
	 
	}

}