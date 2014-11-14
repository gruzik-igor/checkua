<?php

class photo_model {

	public function table($sufix = '')
	{
		return $_SESSION['service']->table.$sufix.$_SESSION['alias']->table;
	}

	public function getAlbums($active = true){
		if($active) $active = 'WHERE `active` = 1'; else $active = '';
		$this->db->executeQuery("SELECT * FROM `{$this->table('_albums')}` {$active} ORDER BY `position` ASC");
        if($this->db->numRows() > 0){
            $data = $this->db->getRows('array');
            $language = '';
			if($_SESSION['language']) $language = "AND `language` = '{$_SESSION['language']}'";
			foreach($data as $album){
				$this->db->executeQuery("SELECT * FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$album->id}' {$language}");
				if($this->db->numRows() > 0){
					$ntkd = $this->db->getRows();

					@$album->name = $ntkd->name;
					@$album->description = $ntkd->description;
					@$album->text = $ntkd->text;
				}
			}
			return $data;
		}
		return null;
	}
	
	public function getAlbumInfoById($id){
		$this->db->executeQuery("SELECT * FROM `{$this->table('_albums')}` WHERE `id` = {$id}");
        if($this->db->numRows() == 1){
            $album = $this->db->getRows();
			return $album;
		}
		return null;
	}
	
	public function getAlbumPhotos($id){
		$this->db->executeQuery("SELECT p.*, u.name as user_name FROM {$this->table()} as p LEFT JOIN wl_users as u ON p.user = u.id WHERE p.album = {$id}");
        if($this->db->numRows() > 0){
            $data = $this->db->getRows('array');
			return $data;
		}
		return null;
	}

	public function changePosition($id, $new_pos){
		$this->db->executeQuery("SELECT id, position as pos FROM `{$this->table('_albums')}` WHERE 1 ORDER BY `position` ASC ");
		 if($this->db->numRows() > 0){
            $articles = $this->db->getRows();
			$old_pos = 0;
			foreach($articles as $a) if($a->id == $id) { $old_pos = $a->pos; break; }
			if($new_pos < $old_pos)	foreach($articles as $a){
				if($a->pos >= $new_pos){
					if($a->pos != $old_pos && $a->pos < $old_pos){
						$pos = $a->pos + 1;
						$this->db->executeQuery("UPDATE `{$this->table('_albums')}` SET `position` = '{$pos}' WHERE `id` = {$a->id}");
												echo $a->id.'b'.$pos;
					}
					if($a->pos == $old_pos){ 
						$this->db->executeQuery("UPDATE `{$this->table('_albums')}` SET `position` = '{$new_pos}' WHERE `id` = {$a->id}");
												echo 'a';
						return true;
					}
				}
			}
			if($new_pos > $old_pos)	foreach($articles as $a){
				if($a->pos <= $new_pos){
					if($a->pos != $old_pos && $a->pos > $old_pos){
						$pos = $a->pos - 1;
						$this->db->executeQuery("UPDATE `{$this->table('_albums')}` SET `position` = '{$pos}' WHERE `id` = {$a->id}");
					}
					if($a->pos == $old_pos){ 
						$this->db->executeQuery("UPDATE `{$this->table('_albums')}` SET `position` = '{$new_pos}' WHERE `id` = {$a->id}");
					}
				} else return true;
			}
		}
		return true;
	}
	
}

?>