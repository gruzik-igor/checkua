<?php

class subscribe_model {

	private $table = 'clients'; // mails

    function add_mail($email, $name = '', $tel = ''){
		$email = $this->db->sanitizeString($email);
		$name = $this->db->sanitizeString($name);
		$tel = $this->db->sanitizeString($tel);
		$this->db->executeQuery("SELECT `id` FROM `{$this->table}` WHERE `email` LIKE '{$email}'");
		if($this->db->numRows() > 0) return false; else {
			$time = time();
			$this->db->executeQuery("INSERT INTO `{$this->table}` (`email`, `name`, `tel`, `active`, `add_date`, `add_from`) VALUES ('{$email}', '{$name}', '{$tel}', '1', '{$time}', '1')");
			if($this->db->affectedRows() > 0) return true; else return false;
		}
    }
	
	function getAll($only_active = false){
		$where = '1';
		if($only_active) $where = ' `active` = 1';
		$this->db->executeQuery("SELECT * FROM `{$this->table}` WHERE {$where} ORDER BY add_date DESC");
		if($this->db->numRows() > 0){
			return $this->db->getRows('array');
		} else return null;
	}
	
	function getListActiveMail(){
		$this->db->executeQuery("SELECT `email` FROM `{$this->table}` WHERE `active` = 1");
		if($this->db->numRows() > 0){
			$mails = $this->db->getRows('array');
			return $mails;
		}
		return false;
	}
	
	function setActiveR($id, $active){
		if($active == 1) $active = 0; else $active = 1;
		$this->db->executeQuery("UPDATE `{$this->table}` SET  `active` =  '{$active}' WHERE `id` ={$id}");
		if($this->db->affectedRows() > 0) return true; else return false;
	}
	
}

?>
