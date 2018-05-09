<?php

class wl_comments_model {

	private $allowed_ext = array('png', 'jpg', 'jpeg');
	public $paginator = true;

	public function get($where = array(), $type = 'array')
	{
		if($this->paginator)
		{
			$_SESSION['option']->paginator_total = $this->db->getCount('wl_comments', $where);
			if(empty($_SESSION['option']->paginator_total))
				return false;
		}

		if($this->paginator && isset($_SESSION['option']->paginator_per_page) && $_SESSION['option']->paginator_per_page > 0 && $_SESSION['option']->paginator_total > 1)
		{
			$start = 0;
			if(isset($_GET['per_page']) && is_numeric($_GET['per_page']) && $_GET['per_page'] > 0)
				$_SESSION['option']->paginator_per_page = $_GET['per_page'];
			if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1)
				$start = ($_GET['page'] - 1) * $_SESSION['option']->paginator_per_page;
			$this->db->limit($start, $_SESSION['option']->paginator_per_page);
		}

		$this->db->select('wl_comments as c', '*', $where)
				->join('wl_users', 'name as user_name, email as user_email', '#c.user')
				->join('wl_ntkd', 'name as page_name', array('alias' => '#c.alias', 'content' => '#c.content'))
				->join('wl_sitemap', 'link', array('alias' => '#c.alias', 'content' => '#c.content'))
				->order('date_add DESC');

		return $this->db->get($type);
	}

	public function add($user, &$image_names = false)
	{
		if(empty($_POST['content']) || empty($_POST['alias']))
			return false;

		$_SESSION['notify']->success = 'Thanks for your review. <br>Travelers will be happy to read it!';

		$inputs = array('content', 'alias', 'rating', 'comment');
		$data = $this->data->prepare($inputs);
		$data['parent'] = 0;
		$data['user'] = $user;
		$data['date_add'] = time();

		$data['status'] = 2;
		if(preg_match("~(http|https|ftp|ftps|href)~", $data['comment']))
			$data['status'] = 3;

		if(!empty($_FILES['images']['name'][0]))
		{
			$_SESSION['notify']->success = 'Thanks for your review. <br>We will email you as soon as it is published.';
			$data['status'] = 3;
			if($image_name = $this->data->post('image_name'))
			{
				if(count($_FILES['images']['name']) > 1)
				{
					$image_names = $names = array();
					for ($ii=1; $ii <= count($_FILES['images']['name']); $ii++) { 
						$i = $ii - 1;
						$image_names[$i] = false;
						if($pos = strrpos($_FILES['images']['name'][$i], '.'))
						{
                			$ext = strtolower(substr($_FILES['images']['name'][$i], $pos + 1));
                			if(in_array($ext, $this->allowed_ext))
                			{
	                			$names[$i] = $image_name.'-'.$ii.'.'.$ext;
	                			$image_names[$i] = $image_name.'-'.$ii;
	                		}
						}
					}
					$data['images'] = implode('|||', $names);
				}
				else
				{
					if($pos = strrpos($_FILES['images']['name'][0], '.'))
					{
            			$ext = strtolower(substr($_FILES['images']['name'][0], $pos + 1));
            			if(in_array($ext, $this->allowed_ext))
            			{
                			$data['images'] = $image_name.'.'.$ext;
                			$image_names[] = $image_name;
                		}
					}
				}
			}
			else
			{
				if(count($_FILES['images']['name']) > 1)
				{
					$image_names = array();
					for ($i=0; $i < count($_FILES['images']['name']); $i++) {
						$image_names[] = $this->data->latterUAtoEN($_FILES['images']['name'][$i]);
					}
					$data['images'] = implode(', ', $image_names);
				}
				else
					$data['images'] = $image_names = $this->data->latterUAtoEN($_FILES['images']['name'][0]);
			}
		}

		return $this->db->insertRow('wl_comments', $data);
	}

}

?>
