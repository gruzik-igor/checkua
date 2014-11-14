<?php

class wl_alias_model {
	
	public $service = false;

    function alias($link){
		$link = $this->db->sanitizeString($link);
		@$_SESSION['alias']->alias = $link;
		$_SESSION['alias']->id = 0;
		$_SESSION['alias']->service = false;
		$_SESSION['alias']->header = '';
		$_SESSION['alias']->footer = '';
		$_SESSION['alias']->columnAB = true;
		$_SESSION['alias']->lcolumn = '';
		// $_SESSION['alias']->lcolumn = '@commons/list';
		$_SESSION['alias']->rcolumn = '';

		$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = 0 AND `alias` = 0");
		if($this->db->numRows() > 0){
			$options = $this->db->getRows('array');
			foreach($options as $opt){
				$key = $opt->name;
				@$_SESSION['option']->$key = $opt->value;
			}
		}

		$alias = $this->db->getAllDataById('wl_aliases', $link, 'alias');
		if(!empty($alias) && $alias->active == 1){
			$_SESSION['alias']->id = $alias->id;
			$_SESSION['alias']->table = $alias->table;
			$_SESSION['alias']->options = $alias->options;
			if($alias->service > 0){
				$this->db->executeQuery("SELECT `name`, `table` FROM `wl_services` WHERE `id` = {$alias->service} AND `active` = 1");
				if($this->db->numRows() == 1){
					$service = $this->db->getRows();
					$_SESSION['alias']->service = $service->name;
					@$_SESSION['service']->name = $service->name;
					$_SESSION['service']->table = $service->table;
				}
			}
			
			if($alias->options > 0){
				$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = {$alias->service} AND `alias` = 0");
				if($this->db->numRows() > 0){
					$options = $this->db->getRows('array');
					foreach($options as $opt){
						$key = $opt->name;
						@$_SESSION['option']->$key = $opt->value;
					}
				}
				$this->db->executeQuery("SELECT `name`, `value` FROM `wl_options` WHERE `service` = {$alias->service} AND `alias` = {$alias->id}");
				if($this->db->numRows() > 0){
					$options = $this->db->getRows('array');
					foreach($options as $opt){
						$key = $opt->name;
						@$_SESSION['option']->$key = $opt->value;
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
			} else {
				$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = 1 AND `content` = '0'");
				if($this->db->numRows() == 1){
					$data = $this->db->getRows();
					$_SESSION['alias']->name = $data->name;
					$_SESSION['alias']->title = $data->title;
					$_SESSION['alias']->description = $data->description;
					$_SESSION['alias']->keywords = $data->keywords;
					$_SESSION['alias']->text = $data->text;
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
			} else {
				$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = 1");
				if($this->db->numRows() == 1){
					$data = $this->db->getRows();
					$_SESSION['alias']->name = $data->name;
					$_SESSION['alias']->title = $data->title;
					$_SESSION['alias']->description = $data->description;
					$_SESSION['alias']->keywords = $data->keywords;
					$_SESSION['alias']->text = $data->text;
				}
			}
		}
    }

}

?>
