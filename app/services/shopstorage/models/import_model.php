<?php 

class import_model
{
	private $manufacturer = false;
	private $markUp = false;
	public $inserted = 0;
	public $insertedStorage = 0;
	public $updated = 0;
	public $deleted = 0;
	public $errors = array();

	public function show($spreadsheet)
	{
		echo('<meta charset="utf-8"><pre>');
		if(!empty($spreadsheet)) foreach ($spreadsheet as $Key => $Row)
		{
			echo $Key.': ';
			if ($Row)
			{
				print_r($Row);
			}
			else
			{
				var_dump($Row);
			}
		}
		exit;
	}

	public function shopstorage($spreadsheet)
	{
		// $this->show($spreadsheet);
		$cols = new stdClass();
		$cols->start = -1; // номер з якого починають товари
		$cols->in_id = 0; // інвентаризаційний номер поставщика
		$cols->in_key = 6; // номер властивості поставщика 0 - false
		$cols->article = 1; // артикул
		$cols->analogs = 7; // аналоги (менше нуля ігноряться)
		$cols->analogs_delimiter = ','; // аналоги (менше нуля ігноряться)
		$cols->manufacturer = 4; // виробник
		$cols->name = 2;
		$cols->count = 6;
		$cols->price = 5;
		$cols->group = 3; // 0 -auto, else #group
		foreach ($spreadsheet as $key => $row)
		{
			if(isset($row[0]) && $row[0] == 'ID' &&
				isset($row[1]) && $row[1] == 'Артикул' && 
				isset($row[2]) && $row[2] == 'Наименование' && 
				isset($row[3]) && $row[3] == 'Группа' && 
				isset($row[4]) && $row[4] == 'Производитель' && 
				isset($row[5]) && $row[5] == 'Цена' && 
				isset($row[6]) && $row[6] == 'Остаток' && 
				isset($row[7]) && $row[7] == 'Аналоги')
			{
				$cols->start = $key + 1;
				break;
			}
		}
		if($cols->start >= 0) return $this->import($spreadsheet, $cols);
		return false;
	}

	public function omega($spreadsheet)
	{
		// $this->show($spreadsheet);
		$cols = new stdClass();
		$cols->start = -1; // номер з якого починають товари
		$cols->in_id = 1; // інвентаризаційний номер поставщика
		$cols->in_key = 173; // номер властивості поставщика 0 - false
		$cols->article = 2; // артикул
		$cols->analogs = -1; // аналоги (менше нуля ігноряться)
		$cols->analogs_delimiter = ','; // аналоги (менше нуля ігноряться)
		$cols->manufacturer = 3; // виробник
		$cols->name = 4;
		$cols->count = -1;
		$cols->setCount = 10;
		$cols->price = 6;
		$cols->group = 100; // 0 -auto, else #group
		foreach ($spreadsheet as $key => $row)
		{
			if(isset($row[0]) && $row[0] == '№ п/п' &&
				isset($row[1]) && $row[1] == 'Карточка' && 
				isset($row[2]) && $row[2] == 'Номер по каталогу' && 
				isset($row[3]) && $row[3] == 'Производитель' && 
				isset($row[4]) && $row[4] == 'Наименование товара' && 
				isset($row[6]) && $row[6] == 'Ваша цена')
			{
				$cols->start = $key + 1;
			}
			if(isset($row[1]) && is_float($row[1])) return false;
		}
		if($cols->start >= 0) return $this->import($spreadsheet, $cols);
		return false;
	}

	public function asiaparts($spreadsheet)
	{
		// $this->show($spreadsheet);
		$cols = new stdClass();
		$cols->start = -1; // номер з якого починають товари
		$cols->in_id = 1; // інвентаризаційний номер поставщика
		$cols->in_key = 3; // номер властивості поставщика 0 - false
		$cols->article = 1; // артикул
		$cols->analogs = -1; // аналоги (менше нуля ігноряться)
		$cols->analogs_delimiter = ' '; // аналоги (менше нуля ігноряться)
		$cols->manufacturer = 0; // виробник
		$cols->name = 2;
		$cols->count = 3;
		$cols->price = 4;
		$cols->group = 100; // 0 -auto, else #group
		foreach ($spreadsheet as $key => $row)
		{
			if(isset($row[0]) && $row[0] == 'Производитель' &&
				isset($row[1]) && $row[1] == 'Артикул' && 
				isset($row[2]) && $row[2] == 'Описание' && 
				isset($row[3]) && $row[3] == 'Остаток, шт.' && 
				isset($row[4]) && $row[4] == 'Цена: опт 3, грн.')
			{
				$cols->start = $key + 1;
				break;
			}
		}
		if($cols->start >= 0) return $this->import($spreadsheet, $cols);
		return false;
	}

	public function autodom($spreadsheet)
	{
		// $this->show($spreadsheet);
		$cols = new stdClass();
		$cols->start = -1; // номер з якого починають товари
		$cols->in_id = 2; // інвентаризаційний номер поставщика
		$cols->in_key = 4; // номер властивості поставщика 0 - false
		$cols->article = 1; // артикул
		$cols->analogs = 6; // аналоги (менше нуля ігноряться)
		$cols->analogs_delimiter = ','; // аналоги (менше нуля ігноряться)
		$cols->manufacturer = 0; // виробник
		$cols->name = 3;
		$cols->count = 4;
		$cols->price = 5;
		$cols->group = 100; // 0 -auto, else #group
		foreach ($spreadsheet as $key => $row)
		{
			if(isset($row[0]) && $row[0] == 'Брэнд' &&
				isset($row[1]) && $row[1] == 'Каталожный номер' && 
				isset($row[2]) && $row[2] == 'Код товара' && 
				isset($row[3]) && $row[3] == 'Наименование' && 
				isset($row[4]) && $row[4] == 'Наличие' && 
				isset($row[5]) && $row[5] == 'Опт2' && 
				isset($row[6]) && $row[6] == 'Замена №1')
			{
				$cols->start = $key + 1;
			}
			if(isset($row[1]) && is_float($row[1])) return false;
		}
		if($cols->start >= 0) return $this->import($spreadsheet, $cols);
		return false;
	}
	
	public function engler($spreadsheet)
	{
		// $this->show($spreadsheet);
		$cols = new stdClass();
		$cols->start = -1; // номер з якого починають товари
		$cols->in_id = 1; // інвентаризаційний номер поставщика
		$cols->in_key = 5; // номер властивості поставщика 0 - false
		$cols->article = 1; // артикул
		$cols->analogs = -1; // аналоги (менше нуля ігноряться)
		$cols->analogs_delimiter = ' '; // аналоги (менше нуля ігноряться)
		$cols->manufacturer = 0; // виробник
		$cols->name = 2;
		$cols->count = 3;
		$cols->price = 4;
		$cols->group = 100; // 0 -auto, else -#group (мінус номер групи), #номер колонки
		foreach ($spreadsheet as $key => $row)
		{
			if(isset($row[0]) && $row[0] == 'Бренд' &&
				isset($row[1]) && $row[1] == 'Кат. №' && 
				isset($row[2]) && $row[2] == 'Найменування' && 
				isset($row[3]) && $row[3] == 'Кіл-ть' && 
				isset($row[4]) && $row[4] == 'Ціна')
			{
				$cols->start = $key + 1;
				break;
			}
		}
		if($cols->start >= 0) return $this->import($spreadsheet, $cols);
		return false;
	}

	private function import($spreadsheet, $cols)
	{
		$inStorage = array();
		$invoiceIs = array();
		$products_ids = null;
		$products_articles = null;
		$products_keys = null;

		if($this->data->post('delete') == 1)
		{
			$this->db->select('s_shopstorage_products as s', 'id as invoice, product as id, price_in, price_out, amount', $_SESSION['alias']->id, 'storage');
			$this->db->join('s_shopshowcase_products', 'wl_alias, article', '#s.product');
			$this->db->join('s_shopshowcase_product_options', 'value as manufacturer', array('product' => '#s.product', 'option' => 1));
			$products = $this->db->get('array');
			if($products) 
				foreach ($products as $product) if($product->wl_alias == $this->data->post('shop')) {
					$inStorage[$product->id] = clone $product;
					$product->article = (string) $product->article;
					$products_articles[$product->article] = $product->id;
					$invoiceIs[] = $product->id;
				}
			unset($products);
		}

		if(empty($inStorage))
		{
			$this->db->select('s_shopshowcase_products as p', 'id, article', $this->data->post('shop'), 'wl_alias');
			$this->db->join('s_shopshowcase_product_options', 'value as manufacturer', array('product' => '#p.id', 'option' => 1));
			$products = $this->db->get();
			if($products) foreach ($products as $product) {
				$products_ids[$product->id] = clone $product;
				$product->article = (string) $product->article;
				$products_articles[$product->article] = $product->id;
			}
			unset($products);
		}

		$this->db->select('s_shopshowcase_options as o', 'id', -1, 'group');
		$this->db->join('s_shopshowcase_options_name', 'name', '#o.id', 'option');
		$manufacturers = $this->db->get();
		if($manufacturers)
		{
			$this->manufacturer = array();
			foreach ($manufacturers as $m) {
				$this->manufacturer[$m->name] = $m->id;
			}
		}

		if($cols->in_key > 0)
		{
			$this->db->select('s_shopshowcase_product_options', 'product, value', $cols->in_key, 'option');
			$products = $this->db->get('array');
			if($products) foreach ($products as $product) {
				$products_keys[$product->product] = $product->value;
			}
			unset($products);
		}

		foreach ($spreadsheet as $key => $row)
		{
			if($key >= $cols->start && $row[$cols->article] != '' && $row[$cols->manufacturer] != '')
			{
				$product = false;
				$id = 0;
				$article = $this->makeArticle($row[$cols->article]);
				$price_in = $this->getPriceIn($row[$cols->price]);
				$amount = 0;
				if($cols->count >= 0)
					$amount = $row[$cols->count];
				elseif(isset($cols->setCount) && $cols->setCount > 0)
					$amount = $cols->setCount;

				if(isset($products_articles[$article]) && $article != '')
				{
					$id = $products_articles[$article];
					$manufacturer = $this->getManufacturer($row[$cols->manufacturer]);
					
					if(isset($products_ids[$id]))
					{
						$product = $products_ids[$id];
							
						if($product->manufacturer != $manufacturer && $article != '')
						{
							$article .= '_'.str_replace(' ', '_', strtoupper($row[$cols->manufacturer]));
							$product = false;
							if(isset($products_articles[$article]))
							{
								$id = $products_articles[$article];
				
								if(isset($products_ids[$id]))
								{
									$product = $products_ids[$id];
								}
							}
							else $product = false;
						}
					}
					elseif(isset($inStorage[$id]))
					{
						$product = $inStorage[$id];
						if($product->manufacturer != $manufacturer)
						{
							$article .= '_'.str_replace(' ', '_', strtoupper($row[$cols->manufacturer]));
							$product = false;
							if(isset($products_articles[$article]))
							{
								$id = $products_articles[$article];
				
								if(isset($inStorage[$id]))
								{
									$product = $inStorage[$id];
								}
							}
							else
							{
								$where['wl_alias'] = $this->data->post('shop');
								$where['article'] = $article;
								$this->db->select('s_shopshowcase_products as p', 'id, article', $where);
								$this->db->join('s_shopshowcase_product_options', 'value as manufacturer', array('product' => '#p.id', 'option' => 1));
								$product = $this->db->get();
							}
						}
					}
				}
				else
				{
					$where = array();
					$where['wl_alias'] = $this->data->post('shop');
					$where['article'] = $article;
					$this->db->select('s_shopshowcase_products as p', 'id, article', $where);
					$this->db->join('s_shopshowcase_product_options', 'value as manufacturer', array('product' => '#p.id', 'option' => 1));
					$product = $this->db->get();

					if($product)
					{
						if($product->manufacturer != $manufacturer)
						{
							$article .= '_'.str_replace(' ', '_', strtoupper($import->Vendor));
							$product = false;
							if(isset($products_articles[$article]))
							{
								$id = $products_articles[$article];
				
								if(isset($inStorage[$id]))
								{
									$product = $inStorage[$id];
								}
							}
							else
							{
								$where['wl_alias'] = $this->data->post('shop');
								$where['article'] = $article;
								$this->db->select('s_shopshowcase_products as p', 'id, article', $where);
								$this->db->join('s_shopshowcase_product_options', 'value as manufacturer', array('product' => '#p.id', 'option' => 1));
								$product = $this->db->get();
							}
						}
					}
				}

				if($product && $row[$cols->price] != '' && $price_in > 0)
				{
					if(isset($inStorage[$product->id]))
					{
						if($this->data->post('checkPrice') == -1)
						{
							if($price_in != $inStorage[$product->id]->price_in || $amount != $inStorage[$product->id]->amount)
							{
								$price = array();
								$price['price_in'] = $price_in;
								$price['price_out'] = $this->getPriceOut($price_in);
								$price['amount'] = $amount;
								$price['manager_edit'] = $_SESSION['user']->id;
								$price['date_edit'] = time();
								$this->db->updateRow('s_shopstorage_products', $price, $inStorage[$product->id]->invoice);
								$this->updated++;
								$invoiceIs[] = $product->id;
							}
						}
						else
						{
							$price_out = unserialize($inStorage[$product->id]->price_out);
							$price_out = $price_out[$this->data->post('checkPrice')];
							if($price_in != $price_out || $amount != $inStorage[$product->id]->amount)
							{
								$price = array();
								$price['price_in'] = $price_out;
								$price['price_out'] = $this->getPriceOut($price_in, $this->data->post('checkPrice'));
								$price['amount'] = $amount;
								$price['manager_edit'] = $_SESSION['user']->id;
								$price['date_edit'] = time();
								$this->db->updateRow('s_shopstorage_products', $price, $inStorage[$product->id]->invoice);
								$this->updated++;
								$invoiceIs[] = $product->id;
							}
						}

						unset($inStorage[$product->id]);
					}
					elseif(!in_array($product->id, $invoiceIs))
					{
						$price = array();
						$price['storage'] = $_SESSION['alias']->id;
						$price['product'] = $product->id;
						$price['price_in'] = $price_in;
						$price['price_out'] = $this->getPriceOut($price_in, $this->data->post('checkPrice'));
						$price['amount'] = $amount;
						$price['manager_add'] = $price['manager_edit'] = $_SESSION['user']->id;
						$price['date_add'] = $price['date_edit'] = time();
						$this->db->insertRow('s_shopstorage_products', $price);
						$this->insertedStorage++;
						$invoiceIs[] = $product->id;
					}

					if($products_keys && !isset($products_keys[$product->id]) && $cols->in_key > 0 && isset($row[$cols->in_id]) && $row[$cols->in_id] != '')
					{
						$data = array();
						$data['product'] = $product->id;
						$data['option'] = $cols->in_key;
						$data['value'] = $row[$cols->in_id];
						$this->db->insertRow('s_shopshowcase_product_options', $data);
					}
				}
				elseif($this->data->post('insert') == 1 && $article != '' && !in_array($id, $invoiceIs) && $price_in > 0)
				{
					if($cols->group == 100)
					{
						$manufacturer_group_1 = array('KIA', 'HYUNDAI', 'Kia/Hyundai', 'Hyundai Kia', 'MOBIS', 'Mobis');
						$where['group'] = (in_array($row[$cols->manufacturer], $manufacturer_group_1)) ? 1 : 2;
					}
					elseif($cols->group >= 0)
					{
						$where['group'] = $row[$cols->group];
					}
					elseif($cols->group < 0)
					{
						$cols->group *= -1;
						$where['group'] = $cols->group;
					}
					
					$where['wl_alias'] = $this->data->post('shop');
					$where['article'] = $article;
					$where['alias'] = $this->data->latterUAtoEN($article) .'-'.$this->data->latterUAtoEN(trim($row[$cols->name]));
					$where['price'] = $price_in;
					$where['availability'] = $where['active'] = 1;
					$where['author_add'] = $where['author_edit'] = $_SESSION['user']->id;
					$where['date_add'] = $where['date_edit'] = time();
					if($this->db->insertRow('s_shopshowcase_products', $where))
					{
						$id = $this->db->getLastInsertedId();

						$ntkd = array();
						$ntkd['alias'] = $this->data->post('shop');
						$ntkd['content'] = $id;
						$ntkd['name'] = $row[$cols->name];
						$this->db->insertRow('wl_ntkd', $ntkd);

						$manufacturer = $this->getManufacturer($row[$cols->manufacturer]);
						if($manufacturer)
						{
							$data = array();
							$data['product'] = $id;
							$data['option'] = 1;
							$data['value'] = $manufacturer;
							$this->db->insertRow('s_shopshowcase_product_options', $data);
						}
						if($cols->analogs >= 0 && isset($row[$cols->analogs]) && $row[$cols->analogs] != '')
						{
							$analogs = explode($cols->analogs_delimiter, $row[$cols->analogs]);
							foreach ($analogs as $analog) {
								$analog = $this->makeArticle($analog);
							}
							$data = array();
							$data['product'] = $id;
							$data['option'] = 2;
							$data['value'] = implode(';', $analogs);
							$this->db->insertRow('s_shopshowcase_product_options', $data);
						}
						if($cols->in_key > 0 && isset($row[$cols->in_id]) && $row[$cols->in_id] != '')
						{
							$data = array();
							$data['product'] = $id;
							$data['option'] = $cols->in_key;
							$data['value'] = $row[$cols->in_id];
							$this->db->insertRow('s_shopshowcase_product_options', $data);
						}
						if($row[$cols->price] != '' && $price_in > 0)
						{
							$price = array();
							$price['storage'] = $_SESSION['alias']->id;
							$price['product'] = $id;
							$price['price_in'] = $price_in;
							$price['price_out'] = $this->getPriceOut($price_in);
							$price['amount'] = $amount;
							$price['manager_add'] = $price['manager_edit'] = $_SESSION['user']->id;
							$price['date_add'] = $price['date_edit'] = time();
							$this->db->insertRow('s_shopstorage_products', $price);
							$invoiceIs[] = $id;
						}
						

						$products_articles[$article] = $id;
						$product_new = new stdClass();
						$product_new->id = $id;
						$product_new->manufacturer = $manufacturer;
						$products_ids[$id] = $product_new;

						$this->inserted++;
						$this->insertedStorage++;
					}
				}
			}
		}

		if($this->data->post('delete') == 1 && !empty($inStorage))
		{
			foreach ($inStorage as $row) {
				if($this->db->deleteRow('s_shopstorage_products', $row->invoice)) $this->deleted++;
			}
		}

		$storage = array();
		$storage['storage'] = $_SESSION['alias']->id;
		$storage['file'] = $_FILES['price']['name'];
		$storage['price_for_1'] = $this->data->post('currency_to_1');
		$storage['currency'] = $this->data->post('currency');
		$storage['inserted'] = $this->insertedStorage;
		$storage['updated'] = $this->updated;
		$storage['deleted'] = $this->deleted;
		$storage['manager'] = $_SESSION['user']->id;
		$storage['date'] = time();
		$this->db->insertRow('s_shopstorage_updates', $storage);

		return true;
	}

	private function getManufacturer($manufacturer)
	{
		$kiaHundai = array('KIA', 'HYUNDAI', 'Kia/Hyundai', 'Hyundai Kia', 'MOBIS');
		if(in_array($manufacturer, $kiaHundai)) return 7;
		if(is_array($this->manufacturer))
		{
			$manufacturer = strtoupper($manufacturer);
			if(isset($this->manufacturer[$manufacturer])) return $this->manufacturer[$manufacturer];
			else
			{
				$data = array();
				$data['wl_alias'] = $this->data->post('shop');
				$data['group'] = -1;
				if($this->db->insertRow('s_shopshowcase_options', $data))
				{
					$id = $this->db->getLastInsertedId();
					$data = array();
					$data['option'] = $id;
					$data['name'] = $manufacturer;
					if($this->db->insertRow('s_shopshowcase_options_name', $data)) 
					{
						$this->manufacturer[$manufacturer] = $id;
						return $id;
					}
				}
			}
		}
		return 0;
	}

	public function getPriceIn($price_in)
	{
		if($this->data->post('currency') == 'USD') return $price_in;
		elseif($this->data->post('currency') == 'UA') return round($price_in / $this->data->post('currency_to_1'), 2);
	}

	private function getPriceOut($price_in, $priceTo = -1)
	{
		if(!$this->markUp)
		{
			$markUps = $this->db->getAllDataByFieldInArray('s_shopstorage_markup', $_SESSION['alias']->id, 'storage');
			if($markUps)
			{
				$this->markUp = array();
				foreach ($markUps as $mark) {
					$this->markUp[$mark->user_type] = $mark->markup + 100;
				}
			}
		}
		if($this->markUp)
		{
			if($priceTo >= 0) $price_in = $price_in * 100 / $this->markUp[$priceTo];
			$price_out = array();
			foreach ($this->markUp as $key => $value) {
				$price_out[$key] = round(($price_in * $value / 100), 2);
			}
			return serialize($price_out);
		}
	}

	private function makeArticle($article)
	{
		$article = (string) $article;
		$article = trim($article);
		$article = strtoupper($article);
		$article = str_replace('-', '', $article);
		return str_replace(' ', '', $article);
	}

	public function keyInArray($search, $array)
	{
		foreach ($array as $key => $value) {
			if($search == $key) return true;
		}
		return false;
	}

}
?>