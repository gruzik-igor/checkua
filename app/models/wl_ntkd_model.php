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

	public function setContent($content = 0, $autoMain = true){
		if(!is_numeric($content)) $content = 0;
		$language = '';
		if($_SESSION['language']) $language = "AND `language` = '{$_SESSION['language']}'";
		$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$content}' {$language}");
		if($this->db->numRows() > 0){
			$ntkd = $this->db->getRows();

			$_SESSION['alias']->name = $ntkd->name;
			$_SESSION['alias']->title = $ntkd->title;
			$_SESSION['alias']->keywords = $ntkd->keywords;
			$_SESSION['alias']->description = $ntkd->description;
			$_SESSION['alias']->text = $ntkd->text;
			$_SESSION['alias']->list = $ntkd->list;

			return $ntkd;
		} else {
			if($autoMain) {
				$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '0' {$language}");
				if($this->db->numRows() > 0){
					$ntkd = $this->db->getRows();

					$_SESSION['alias']->name = $ntkd->name;
					$_SESSION['alias']->title = $ntkd->title;
					$_SESSION['alias']->keywords = $ntkd->keywords;
					$_SESSION['alias']->description = $ntkd->description;
					$_SESSION['alias']->text = $ntkd->text;
					$_SESSION['alias']->list = $ntkd->list;

					return $ntkd;
				} else {
					if($_SESSION['language']) $language = "AND ntkd.language = '{$_SESSION['language']}'";
					$this->db->executeQuery("SELECT alias.id, ntkd.* FROM wl_aliases as alias LEFT JOIN wl_ntkd as ntkd ON ntkd.alias = alias.id WHERE alias.alias = 'main' {$language}");
					if($this->db->numRows() > 0){
						$ntkd = $this->db->getRows();

						$_SESSION['alias']->name = $ntkd->name;
						$_SESSION['alias']->title = $ntkd->title;
						$_SESSION['alias']->keywords = $ntkd->keywords;
						$_SESSION['alias']->description = $ntkd->description;
						$_SESSION['alias']->text = $ntkd->text;
						$_SESSION['alias']->list = $ntkd->list;

						return $ntkd;
					}
				}
			} else {
				$_SESSION['alias']->name = '';
				$_SESSION['alias']->title = '';
				$_SESSION['alias']->keywords = '';
				$_SESSION['alias']->description = '';
				$_SESSION['alias']->text = '';
				$_SESSION['alias']->list = '';
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