<?php

class wl_ntkd_model {

	public function get($request = 'main', $content = 0, $autoMain = true){
		if(!is_numeric($content)) $content = 0;
		$language = '';
		if($_SESSION['language']) $language = "AND ntkd.language = '{$_SESSION['language']}'";
		if(is_numeric($request)){
			$this->db->executeQuery("SELECT ntkd.* FROM wl_ntkd as ntkd WHERE ntkd.alias = '{$request}' AND ntkd.content = '{$content}' {$language}");
		} else {
			$this->db->executeQuery("SELECT alias.id, ntkd.* FROM wl_aliases as alias LEFT JOIN wl_ntkd as ntkd ON ntkd.alias = alias.id WHERE alias.alias = '{$request}' AND ntkd.content = '{$content}' {$language}");
		}
		if($this->db->numRows() > 0){
			return $this->db->getRows();
		} else {
			if($autoMain) {
				$this->db->executeQuery("SELECT alias.id, ntkd.* FROM wl_aliases as alias LEFT JOIN wl_ntkd as ntkd ON ntkd.alias = alias.id WHERE alias.alias = 'main' AND ntkd.content = '0' {$language}");
				if($this->db->numRows() > 0){
					return $this->db->getRows();
				}
			} else {
				@$ntkd->name = '';
				$ntkd->title = '';
				$ntkd->keywords = '';
				$ntkd->description = '';
				$ntkd->text = '';
				return $ntkd;
			}
		}
		return null;
	}
	
	public function all(){
		$this->db->executeQuery("SELECT alias.id, ntkd.* FROM wl_aliases as alias LEFT JOIN wl_ntkd as ntkd ON ntkd.alias = alias.id WHERE 1");
		if($this->db->numRows() > 0){
			return $this->db->getRows('array');
		}
		return null;
	}

}

?>