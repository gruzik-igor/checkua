<?php

/**
* Модель для роботи з мультимовністю
*/
class wl_language_model
{
	
	private $words = array();
	private $words_aliases = array(0);

	public function get($word, $alias = -1)
	{
		if(empty($this->words))
			$this->getWords($alias);
		if($alias >= 0 && !in_array($alias, $this->words_aliases))
			$this->getWords($alias);
		if(array_key_exists($word, $this->words))
		{
			if($this->words[$word] != '')
				return $this->words[$word];
		}
		else
			$this->add($word, $alias);
		return $word;
	}

	public function add($word, $alias = -1)
	{
		$this->words[$word] = $word;
		$data['word'] = $word;
		$data['alias'] = $_SESSION['alias']->id;
		$data['type'] = 1;
		if($alias >= 0)
			$data['alias'] = $alias;
		$data['position'] = $this->db->getCount('wl_language_words', $data['alias'], 'alias') + 1;
		if($this->db->insertRow('wl_language_words', $data))
		{
			$id = $this->db->getLastInsertedId();
			if($_SESSION['language'])
				foreach ($_SESSION['all_languages'] as $language) {
					$this->db->insertRow('wl_language_values', array('word' => $id, 'language' => $language));
				}
			else
				$this->db->insertRow('wl_language_values', array('word' => $id));
		}
		return true;
	}

	public function getAllWords()
	{
		$this->db->select('wl_language_words as w');
		if($_SESSION['language'])
			foreach ($_SESSION['all_languages'] as $language) {
				$this->db->join("wl_language_values as language_{$language}", "value as {$language}", array('language' => $language, 'word' => '#w.id'));
			}
		else
			$this->db->join("wl_language_values", "value", array('word' => '#w.id'));
		$this->db->order('position');
		return $this->db->get('array');
	}

	private function getWords($alias = -1)
	{
		$this->words = array();
		if(!in_array($_SESSION['alias']->id, $this->words_aliases))
			$this->words_aliases[] = $_SESSION['alias']->id;
		if($alias >= 0 && !in_array($alias, $this->words_aliases))
			$this->words_aliases[] = $alias;
		$where['alias'] = $this->words_aliases;
		$this->db->select('wl_language_words as w', 'word', $where);
		if($_SESSION['language'])
			$this->db->join('wl_language_values', 'value', array('language' => $_SESSION['language'], 'word' => '#w.id'));
		else
			$this->db->join('wl_language_values', 'value', array('word' => '#w.id'));
		if($words = $this->db->get('array'))
			foreach ($words as $word) {
				$this->words[$word->word] = $word->value;
			}
		return true;
	}

	public function save($word, $language = false, $value = '', $rewrite = true)
	{
		$where['word'] = $word;
		if($language)
			$where['language'] = $language;
		$translate = $this->db->getAllDataById('wl_language_values', $where);
		if($translate)
		{
			if($rewrite || $translate->value == '')
			{
				$this->db->updateRow('wl_language_values', array('value' => $value), $translate->id);
				return true;
			}
		}
		else
		{
			$where['value'] = $value;
			if($this->db->insertRow('wl_language_values', $where))
				return true;
		}
		return false;
	}

	public function copy($alias, $language = false)
	{
		if($words = $this->db->getAllDataByFieldInArray('wl_language_words', $alias, 'alias'))
			foreach ($words as $word) {
				$this->save($word->id, $language, $word->word, false);
			}
		return true;
	}

}

?>