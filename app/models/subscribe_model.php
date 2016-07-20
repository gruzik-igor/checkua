<?php

class subscribe_model {

    function add_mail($email, $name = ''){
		$this->db->executeQuery("SELECT * FROM `wl_users` WHERE `email` LIKE '{$email}'");
		if($this->db->numRows() > 0) {
			return false;
		} else {
			$data['email'] = $email;
			$data['name'] = $name;
			$data['type'] = 5;
			$data['status'] = 2;
			$data['active'] = 0;
			$data['registered'] = time();
			if($this->db->insertRow('wl_users', $data)){
				$id = $this->db->getLastInsertedId();
				if($id > 0){
					$register['date'] = $data['registered'];
					$register['do'] = 8;
					$register['user'] = $id;
					if($this->db->insertRow('wl_user_register', $register)) return true;
				}
			}
		}
		return false;
    }
	
}

?>
