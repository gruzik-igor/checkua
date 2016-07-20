<?php

class wl_alias_model
{
	public $service = false;

    function alias($link)
    {
		$link = $this->db->sanitizeString($link);
		$_SESSION['alias'] = new stdClass();
		$_SESSION['option'] = new stdClass();
		$_SESSION['service'] = new stdClass();

		$_SESSION['alias']->alias = $link;
		$_SESSION['alias']->id = 0;
		$_SESSION['alias']->service = false;
		$_SESSION['alias']->image = false;
		$_SESSION['alias']->js_plugins = array();
		$_SESSION['alias']->js_load = array();
		$_SESSION['alias']->js_init = array();

		$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = 0 AND `alias` = 0");
		if($this->db->numRows() > 0){
			$options = $this->db->getRows('array');
			foreach($options as $opt){
				$key = $opt->name;
				$_SESSION['option']->$key = $opt->value;
			}
		}

		$alias = $this->db->getAllDataById('wl_aliases', $link, 'alias');
		if(!empty($alias) && $alias->active == 1)
		{
			unset($_SESSION['alias-cache'][$alias->id]);

			$_SESSION['alias']->id = $alias->id;
			$_SESSION['alias']->table = $alias->table;
			$_SESSION['alias']->options = $alias->options;
			if($alias->service > 0){
				$this->db->executeQuery("SELECT `name`, `table` FROM `wl_services` WHERE `id` = {$alias->service} AND `active` = 1");
				if($this->db->numRows() == 1){
					$service = $this->db->getRows();
					$_SESSION['alias']->service = $service->name;
					$_SESSION['service']->name = $service->name;
					$_SESSION['service']->table = $service->table;
				}
			}
			
			$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = 0 AND `alias` = 0");
			if($this->db->numRows() > 0){
				$options = $this->db->getRows('array');
				foreach($options as $opt){
					$key = $opt->name;
					$_SESSION['option']->$key = $opt->value;
				}
			}
			if($alias->options > 0){
				$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = {$alias->service} AND `alias` = 0");
				if($this->db->numRows() > 0){
					$options = $this->db->getRows('array');
					foreach($options as $opt){
						$key = $opt->name;
						$_SESSION['option']->$key = $opt->value;
					}
				}
				$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = {$alias->service} AND `alias` = {$alias->id}");
				if($this->db->numRows() > 0){
					$options = $this->db->getRows('array');
					foreach($options as $opt){
						$key = $opt->name;
						$_SESSION['option']->$key = $opt->value;
					}
				}
			}

			$where = '';
			if($_SESSION['language']) $where = " AND `language` LIKE '{$_SESSION['language']}'";
			$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = {$alias->id} AND `content` = '0' {$where}");
			if($this->db->numRows() == 1){
				$data = $this->db->getRows();
				$_SESSION['alias']->name = $data->name;
				$_SESSION['alias']->title = $data->title;
				$_SESSION['alias']->description = $data->description;
				$_SESSION['alias']->keywords = $data->keywords;
				$_SESSION['alias']->text = $data->text;
				$_SESSION['alias']->list = $data->list;
			} else {
				$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = 1 AND `content` = '0'");
				if($this->db->numRows() == 1){
					$data = $this->db->getRows();
					$_SESSION['alias']->name = $data->name;
					$_SESSION['alias']->title = $data->title;
					$_SESSION['alias']->description = $data->description;
					$_SESSION['alias']->keywords = $data->keywords;
					$_SESSION['alias']->text = $data->text;
					$_SESSION['alias']->list = $data->list;
				}
			}
		} else {
			$where = '';
			if($_SESSION['language']) $where = " AND `language` LIKE '{$_SESSION['language']}'";
			$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = 1 {$where}");
			if($this->db->numRows() == 1){
				$data = $this->db->getRows();
				$_SESSION['alias']->name = $data->name;
				$_SESSION['alias']->title = $data->title;
				$_SESSION['alias']->description = $data->description;
				$_SESSION['alias']->keywords = $data->keywords;
				$_SESSION['alias']->text = $data->text;
				$_SESSION['alias']->list = $data->list;
			} else {
				$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = 1");
				if($this->db->numRows() == 1){
					$data = $this->db->getRows();
					$_SESSION['alias']->name = $data->name;
					$_SESSION['alias']->title = $data->title;
					$_SESSION['alias']->description = $data->description;
					$_SESSION['alias']->keywords = $data->keywords;
					$_SESSION['alias']->text = $data->text;
					$_SESSION['alias']->list = $data->list;
				}
			}
		}
    }

    public function admin_options()
    {
		$_SESSION['admin_options'] = array();
		$admin_options = $sub_menu = $this->db->getAllDataByFieldInArray('wl_options', array('alias' => -$_SESSION['alias']->id));
		if($admin_options) {
			foreach ($admin_options as $ao) {
				if($ao->name != 'sub-menu'){
					$_SESSION['admin_options'][$ao->name] = $ao->value;
				}
			}
		}
    }

}

?>
