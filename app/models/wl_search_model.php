<?php 

class wl_search_model {

	public function search($data)
	{
		$data = $this->db->sanitizeString($data);
		$language = '';
		if ($_SESSION['language']  && !isset($_GET['search_checkbox'])) {
			$language = 'AND s.language = "'.$_SESSION['language'].'"';
		}
		$this->db->executeQuery("SELECT s.*, a.alias as link, a.table, a.service FROM wl_ntkd AS s LEFT JOIN wl_aliases AS a ON s.alias = a.id WHERE (s.name LIKE '%{$data}%' OR s.text LIKE '%{$data}%') {$language}");
		if($this->db->numRows() > 0){
			$data = $this->db->getRows('array');
			return $data;
		}
		return null;
	}

}

?>