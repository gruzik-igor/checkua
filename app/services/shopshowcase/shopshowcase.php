<?php

class shopshowcase extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
        	$request = $_GET['request'];
        	$request = explode('/', $request);
        	if($request[0] == 'admin') $this->admin($method);
            else $this->index();
            // else $this->index($method);
        }
    }

    public function index($admin = false)
    {
    	$this->load->smodel('shop_model');
		
		$url = $this->data->url();
		$id = end($url);
		$id = explode('-', $id);
		$id = $id[0];

		if(is_numeric($id)) {
			if($admin) {
				$this->edit($id);
				exit();
			} else {
				$product = $this->shop_model->getProductById($id);
				if($product && ($product->active == 1 || $this->userCan($_SESSION['alias']->alias))){

					array_shift($url);
					$url = implode('/', $url);
					if($url != $product->link){
						header ('HTTP/1.1 301 Moved Permanently');
						header ('Location: '. SITE_URL. $_SESSION['alias']->alias .'/'. $product->link);
						exit();
					}

					$product->alias_name = $_SESSION['alias']->name;

					$this->load->model('wl_ntkd_model');
					$this->wl_ntkd_model->setContent($product->id);
					
					$this->load->page_view('detal_view', array('product' => $product));
				} else $this->load->page_404();
			}
		} elseif($id != '' && $id != $_SESSION['alias']->alias) {
			if($_SESSION['option']->useGroups){

				$group = false;
				$parent = 0;
				array_shift($url);
				foreach ($url as $uri) {
					$group = $this->shop_model->getGroupByAlias($uri, $parent);
					if($group){
						$parent = $group->id;
					} else $group = false;
				}

				if($group && ($group->active == 1 || $this->userCan($_SESSION['alias']))){
					$group->alias_name = $_SESSION['alias']->name;
					$group->parents = array();
					if($group->parent > 0){
						$list = array();
			            $groups = $this->db->getAllData($this->shop_model->table('_groups'));
			            foreach ($groups as $Group) {
			            	$list[$Group->id] = clone $Group;
			            }
						$group->parents = $this->shop_model->makeParents($list, $group->parent, $group->parents);
					}
					$this->load->model('wl_ntkd_model');
					$this->wl_ntkd_model->setContent(($group->id * -1));

					$type = 'groups';
					if($admin) {
						$list = $this->shop_model->getGroups($group->id, false);
						if (empty($list)) {
							$type = 'products';
							$list = $this->shop_model->getProducts($group->id, false);
						}
						if($type == 'groups') $this->load->admin_view('admin/index_view', array('group' => $group, 'groups' => $list));
						else $this->load->admin_view('admin/list_view', array('group' => $group, 'products' => $list));
					} else {
						$list = $this->shop_model->getGroups($group->id);
						if (empty($list)) {
							$type = 'products';
							$list = $this->shop_model->getProducts($group->id);
						}
						$this->load->page_view('index_view', array('group' => $group, 'list' => $list, 'type' => $type));
					}
				} else $this->load->page_404();
			} else $this->load->page_404();
		} else {
			if($_SESSION['option']->useGroups){
				$type = 'groups';
				if($admin) $list = $this->shop_model->getGroups(0, false);
				else $list = $this->shop_model->getGroups();
				if (empty($list)) {
					$type = 'products';
					$list = $this->shop_model->getProducts();
				}
				if($admin) {
					if($type == 'groups') $this->load->admin_view('admin/index_view', array('groups' => $list));
					else $this->load->admin_view('admin/list_view', array('products' => $list));
				} else {
					$this->load->page_view('index_view', array('list' => $list, 'type' => $type));
				}
			} else {
				if($admin){
					$products = $this->shop_model->getproducts(-1, false);
					$this->load->admin_view('admin/list_view', array('products' => $products));
				} else {
					$products = $this->shop_model->getproducts();
					$this->load->page_view('index_view', array('list' => $products, 'type' => 'products'));
				}
			}
		}
    }
	
	function admin(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->index(true);
			exit();		
		} else $this->load->page_404();
	}

	function all(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->smodel('shop_model');
			$_SESSION['option']->PerPage = 0;
			$products = $this->shop_model->getProducts(-1, false);
			$this->load->admin_view('admin/list_view', array('products' => $products));			
		} else $this->load->page_404();
	}
	
	function add(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->admin_view('admin/add_product_view');
		}
	}
	
	function edit($id = 0){
		if($this->userCan($_SESSION['alias']->alias)){
			if($id == 0) $id = $this->data->uri(3);	
			if(!is_numeric($id)){
				$id = explode('-', $id);
				$id = $id[0];
			}
			if(is_numeric($id)){
				$this->load->smodel('shop_model');
				$product = $this->shop_model->getProductById($id, false);
				if($product){
					$groups = null;
					if($_SESSION['option']->useGroups){
						$groups = $this->shop_model->getGroups();
						if($_SESSION['option']->ProductMultiGroup){
							$activeGroups = $this->db->getAllDataByFieldInArray($this->shop_model->table('_article_group'), $article->id, 'article');
							$product->group = array();
							if($activeGroups){
								foreach ($activeGroups as $ag) {
									$product->group[] = $ag->group;
								}
							}
						}
					} 
					$this->load->admin_view('admin/edit_product_view', array('product' => $product, 'groups' => $groups));
					unset($_SESSION['notify']);
				} else $this->load->page_404();
			} else {
				header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias);
				exit();
			}
		}
	}
	
	function save(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('shop_model');
				if($_POST['id'] == 0){
					$photo = false;
					if(!empty($_FILES['photo']['name'])) $photo = true;
					$id = $this->shop_model->add_product($photo);
					if($id){
						if(!empty($_FILES['photo']['name'])) $this->savephoto('photo', IMG_PATH.$_SESSION['option']->folder.'/', $id);
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/edit/'.$id);
						exit;
					}
				} else {
					$data = array('active' => 1, 'availability' => 1);
					if(isset($_POST['link']) && $_POST['link'] != '') $data['link'] = $_POST['id'] . '-' . trim($this->data->post('link'));
					if(isset($_POST['active']) && $_POST['active'] == 0) $data['active'] = 0;
					if(isset($_POST['availability']) && is_numeric($_POST['availability']) && $_POST['availability'] > 1) $data['availability'] = $_POST['availability'];
					if(isset($_POST['price']) && is_numeric($_POST['price']) && $_POST['price'] >= 0) $data['price'] = $_POST['price'];
					if($_SESSION['option']->useGroups){
						if($_SESSION['option']->ProductMultiGroup){
							$use = array();
							$activegroups = $this->db->getAllDataByFieldInArray($this->shop_model->table('_article_group'), $_POST['id'], 'article');
							if($activegroups) {
								$temp = array();
								foreach ($activegroups as $ac) {
									$temp[] = $ac->group;
								}
								$activegroups = $temp;
								$temp = null;
							}
							if(isset($_POST['group']) && is_array($_POST['group'])){
								foreach ($_POST['group'] as $group) {
									if(!in_array($group, $activegroups)){
										$this->db->insertRow($this->shop_model->table('_article_group'), array('article' => $_POST['id'], 'group' => $group));
									}
									$use[] = $group;
								}
							}
							if($activegroups) {
								foreach ($activegroups as $ac) {
									if(!in_array($ac, $use)){
										$this->db->executeQuery("DELETE FROM {$this->shop_model->table('_article_group')} WHERE `article` = '{$_POST['id']}' AND `group` = '{$ac}'");
									}
								}
							}
						} else {
							if(isset($_POST['group']) && is_numeric($_POST['group'])) $data['group'] = $_POST['group'];
						}
					}
					if(!empty($_FILES['photo']['name'])){
						$data['photo'] = $_POST['id'];

						$path = IMG_PATH.$_SESSION['option']->folder.'/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
						if(!is_dir($path)) mkdir($path, 0777);

						$this->savephoto('photo', $path, $_POST['id']);
					}
					$this->shop_model->saveProductOptios($_POST['id']);
					if($this->db->updateRow($this->shop_model->table('_products'), $data, $_POST['id'])){

						if(isset($_POST['to']) && $_POST['to'] == 'new'){
							header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/add');
						} elseif(isset($_POST['to']) && $_POST['to'] == 'category') {
							$link = SITE_URL.'admin/'.$_SESSION['alias']->alias;
							$product = $this->shop_model->getProductById($_POST['id']);
							$product->link = explode('/', $product->link);
							array_pop ($product->link);
							if(!empty($product->link)){
								$product->link = implode('/', $product->link);
								$link .= '/'.$product->link;
							}
							header("Location: ".$link);
						} else {
							$referer = $_SERVER['HTTP_REFERER'];
							$referer = explode('/', $referer);
							array_pop ($referer);
							if(isset($data['link'])) $referer[] = $data['link'];
							else $referer[] = $_POST['id'];
							$referer = implode('/', $referer);
							@$_SESSION['notify']->success = 'Дані успішно оновлено!';
							header("Location: ".$referer);
						}
						exit;
					}
				}
			}
		} else $this->load->page_404();
	}
	
	function delete(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('shop_model');
				$article = $this->db->getAllDataById($this->shop_model->table('_products'), $_POST['id']);
				if($article){

					$this->db->deleteRow($this->shop_model->table('_products'), $article->id);
					$this->db->executeQuery("UPDATE `{$this->shop_model->table('_products')}` SET `position` = position - 1 WHERE `id` > '{$article->id}'");
					$this->db->executeQuery("DELETE FROM wl_ntkd WHERE alias = '{$_SESSION['alias']->id}' AND content = '{$article->id}'");
					if($article->photo > 0){
						$path = IMG_PATH.$_SESSION['option']->folder.'/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

						$prefix = array('');
						$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
						if($sizes){
							foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
								$prefix[] = $resize->prefix .'_';
							}
						}
						
						foreach ($prefix as $p) {
							$filename = $path.$p.$article->photo.'.jpg';
							@unlink ($filename);
						}
					}

					$link = '';
					if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 0){
						$group = $this->db->getAllDataById($this->shop_model->table('_groups'), $article->group);
						if($group) $link = '/'.$group->link;
					}
					header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.$link);
					exit;

				}
			}
		} else $this->load->page_404();
	}
	
	function changeposition(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position'])){
				$this->load->smodel('shop_model');
				if($this->shop_model->changePosition('_products', $_POST['id'], $_POST['position'])){
					header("Location: ".$_SERVER['HTTP_REFERER']);
					exit;
				} else $this->load->page_404();
			}
		}
	}

	function changeGroup(){
		$res = array('result' => false);
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['group']) && is_numeric($_POST['group']) && isset($_POST['id']) && is_numeric($_POST['id'])){
				if($this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, array('group' => $_POST['group']), $_POST['id'])){
					$res['result'] = true;
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	function changeAvailability(){
		$res = array('result' => false);
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['availability']) && is_numeric($_POST['availability']) && isset($_POST['id']) && is_numeric($_POST['id'])){
				if($this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, array('availability' => $_POST['availability']), $_POST['id'])){
					$res['result'] = true;
				}
			}
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	function groups(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->smodel('shop_model');
			$groups = $this->shop_model->getGroups(-1, false);
			$this->load->admin_view('admin/groups_view', array('groups' => $groups));			
		} else $this->load->page_404();
	}

	function add_group(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->smodel('shop_model');
			$groups = $this->shop_model->getGroups(-1);
			$this->load->admin_view('admin/add_group_view', array('groups' => $groups));
		} else $this->load->page_404();
	}

	function edit_group(){
		if($this->userCan($_SESSION['alias']->alias)){
			$id = $this->data->uri(3);	
			if(is_numeric($id)){
				$this->load->smodel('shop_model');
				$group = $this->shop_model->getgroupById($id, false);
				if($group){
					$groups = $this->shop_model->getGroups(-1);
					$this->load->admin_view('admin/edit_group_view', array('group' => $group, 'groups' => $groups));
				} else $this->load->page_404();
			} else $this->load->page_404();
		}
	}

	function save_group(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('shop_model');
				if($_POST['id'] == 0){
					$photo = false;
					if(!empty($_FILES['photo']['name'])) $photo = true;
					$id = $this->shop_model->add_group($photo);
					if($id){
						if(!empty($_FILES['photo']['name'])) $this->savephoto('photo', IMG_PATH.$_SESSION['option']->folder.'/groups/', $id);
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/edit_group/'.$id);
						exit;
					}
				} else {
					$group = $this->db->getAllDataById($this->shop_model->table('_groups'), $_POST['id']);
					if($group){
						$data = array('active' => 1);
						if(isset($_POST['link']) && $_POST['link'] != '') $data['link'] = $_POST['link'];
						if(isset($_POST['active']) && $_POST['active'] == 0) $data['active'] = 0;
						if(isset($_POST['parent']) && is_numeric($_POST['parent']) && $_POST['parent'] >= 0) $data['parent'] = $_POST['parent'];
						if(!empty($_FILES['photo']['name'])){
							$data['photo'] = $_POST['id'];

							$path = IMG_PATH.$_SESSION['option']->folder.'/groups/';
							if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
							if(!is_dir($path)) mkdir($path, 0777);

							$this->savephoto('photo', $path, $_POST['id']);
						}
						if($group->parent != $data['parent']) $this->shop_model->changeGroupParent($group->id, $group->parent, $data['parent']);
						if($this->db->updateRow($this->shop_model->table('_groups'), $data, $_POST['id'])){
							header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/groups');
							exit;
						}
					} else {
						$this->load->page_404();
					}
				}
			}
		} else $this->load->page_404();
	}

	function delete_group(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('shop_model');
				$group = $this->db->getAllDataById($this->shop_model->table('_groups'), $_POST['id']);
				if($group){

					$content = false;
					if(isset($_POST['content']) && $_POST['content'] == 1) $content = true;
					if($content){

						$list = array();
						$childs1 = array();
						$emptyParentsList = array();
			            $groups = $this->db->getAllData($this->shop_model->table('_groups'));
			            foreach ($groups as $g) {
			            	$list[$g->id] = clone $g;
			            	$list[$g->id]->childs = array();
							if(isset($emptyParentsList[$g->id])){
								foreach ($emptyParentsList[$g->id] as $c) {
									$list[$g->id]->childs[] = $c;
								}
							}
							if($g->parent > 0) {
								if(isset($list[$g->parent]->childs)) $list[$g->parent]->childs[] = $g->id;
								else {
									if(isset($emptyParentsList[$g->parent])) $emptyParentsList[$g->parent][] = $group->id;
									else $emptyParentsList[$g->parent] = array($g->id);
								}
							}
			            	if($g->parent == $group->id) $childs1[] = $g->id;
			            }
						$childs = $this->shop_model->getGroupParents($list, $childs1);

						$this->deleteProductsByGroup($group->id, $this->shop_model->table('_products'));
						if($childs){
							foreach ($childs as $g) {
								$this->deleteProductsByGroup($g, $this->shop_model->table('_products'));
								$this->db->deleteRow($this->shop_model->table('_groups'), $g);
								$this->db->executeQuery("DELETE FROM wl_ntkd WHERE alias = '{$_SESSION['alias']->id}' AND content = '-{$g}'");
								if($list[$g]->photo > 0){
									$path = IMG_PATH.$_SESSION['option']->folder.'/groups/';
									if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

									$prefix = array('');
									$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
									if($sizes){
										foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
											$prefix[] = $resize->prefix .'_';
										}
									}
									
									foreach ($prefix as $p) {
										$filename = $path.$p.$list[$g]->photo.'.jpg';
										@unlink ($filename);
									}
								}
							}
						}
					} else {
						$groups = $this->db->getAllDataByFieldInArray($this->shop_model->table('_groups'), $group->id, 'parent');
						if($groups){
							$count = 1;
							$this->db->executeQuery("SELECT count(*) as count FROM {$this->shop_model->table('_groups')} WHERE `parent` = '{$group->parent}'");
							if($this->db->numRows() == 1){
				                $count = $this->db->getRows()->count;
				            }
				            foreach ($groups as $g) {
				            	$count++;
				            	$this->db->updateRow($this->shop_model->table('_groups'), array('parent' => $group->parent, 'position' => $count), $g->id);
				            }
						}
						$this->db->executeQuery("UPDATE `{$this->shop_model->table('_products')}` SET `group` = '{$group->parent}' WHERE `group` = '{$group->id}'");
					}

					$this->db->deleteRow($this->shop_model->table('_groups'), $group->id);
					$this->db->executeQuery("UPDATE `{$this->shop_model->table('_groups')}` SET `position` = position - 1 WHERE `position` > '{$group->position}'");
					$this->db->executeQuery("DELETE FROM wl_ntkd WHERE alias = '{$_SESSION['alias']->id}' AND content = '-{$group->id}'");
					if($group->photo > 0){
						$path = IMG_PATH.$_SESSION['option']->folder.'/groups/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

						$prefix = array('');
						$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
						if($sizes){
							foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
								$prefix[] = $resize->prefix .'_';
							}
						}
						
						foreach ($prefix as $p) {
							$filename = $path.$p.$group->photo.'.jpg';
							@unlink ($filename);
						}
					}

					header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/groups');
					exit;

				}
			}
		} else $this->load->page_404();
	}

	public function change_group_position(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position'])){
				$this->load->smodel('shop_model');
				if($this->shop_model->changePosition('_groups', $_POST['id'], $_POST['position'])){
					header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/groups');
					exit;
				} else $this->load->page_404();
			}
		}
	}

	private function savephoto($name_field, $path, $name, $array = false, $i = 0){
		if(!empty($_FILES[$name_field]['name'])){
			$this->load->library('image');
			if($array) $this->image->uploadArray($name_field, $i, $path, $name);
			else $this->image->upload($name_field, $path, $name);
			$this->image->save();
			if($this->image->getErrors() == ''){
				if($_SESSION['option']->resize > 0){
					$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$this->image->loadImage($path, $name);
							if($resize->type == 1) $this->image->resize($resize->width, $resize->height, 100);
							if($resize->type == 2) $this->image->preview($resize->width, $resize->height, 100);
							$this->image->save($path, $resize->prefix);
						}
					}
				}
				return true;
			}
		}
		return false;
	}

	private function deleteProductsByGroup($group, $table){
		$products = $this->db->getAllDataByFieldInArray($table, $group, 'group');
		if($products) foreach ($products as $a) {
			$this->db->deleteRow($table, $a->id);
			$this->db->executeQuery("DELETE FROM wl_ntkd WHERE alias = '{$_SESSION['alias']->id}' AND content = '{$a->id}'");
			if($a->photo > 0){
				$path = IMG_PATH.$_SESSION['option']->folder.'/';
				if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));

				$prefix = array('');
				$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
				if($sizes){
					foreach ($sizes as $resize) if($resize->active == 1 && $resize->prefix != ''){
						$prefix[] = $resize->prefix .'_';
					}
				}
				
				foreach ($prefix as $p) {
					$filename = $path.$p.$a->photo.'.jpg';
					@unlink ($filename);
				}
			}
		}
		return true;
	}

	public function options(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->smodel('shop_model');

			$url = $this->data->url();
			$id = end($url);
			$id = explode('-', $id);
			$id = $id[0];

			if(is_numeric($id)){
				$option = $this->db->getAllDataById($this->shop_model->table('_group_options'), $id);
				if($option)	$this->load->admin_view('admin/edit_option_view', array('option' => $option));
				else $this->load->page404();
			} elseif($id != '' && $id != $_SESSION['alias']->alias) {
				if($_SESSION['option']->useGroups){

					$group = false;
					$parent = 0;
					array_shift($url);
					array_shift($url);
					array_shift($url);
					if($url) foreach ($url as $uri) {
						$group = $this->shop_model->getGroupByAlias($uri, $parent);
						if($group){
							$parent = $group->id;
						} else $group = false;
					}

					if($group){
						$group->alias_name = $_SESSION['alias']->name;
						$group->parents = array();
						if($group->parent > 0){
							$list = array();
				            $groups = $this->db->getAllData($this->shop_model->table('_groups'));
				            foreach ($groups as $Group) {
				            	$list[$Group->id] = clone $Group;
				            }
							$group->parents = $this->shop_model->makeParents($list, $group->parent, $group->parents);
						}
						$this->load->model('wl_ntkd_model');
						$this->wl_ntkd_model->setContent(($group->id * -1));

						$groups = $this->shop_model->getGroups($group->id, false);
						$options = $this->shop_model->getOptions($group->id, false);
						$this->load->admin_view('admin/options_view', array('group' => $group, 'groups' => $groups, 'options' => $options));

					} else {
						$groups = $this->shop_model->getGroups(0, false);
						$options = $this->shop_model->getOptions(0, false);
						$this->load->admin_view('admin/options_view', array('options' => $options, 'groups' => $groups));
					}
				} else {
					$options = $this->shop_model->getOptions(0, false);
					$this->load->admin_view('admin/options_view', array('options' => $options));	
				}
			} else $this->load->page_404();
		} else $this->load->page_404();
	}

	public function add_option(){
		if($this->userCan($_SESSION['alias']->alias)){
			$this->load->admin_view('admin/add_option_view');
		}
	}

	public function save_option()
	{
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('shop_model');
				if($_POST['id'] == 0){
					$id = $this->shop_model->add_option();
					if($id){
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/options/'.$id);
						exit;
					}
				} else {
					if($this->shop_model->saveOption($_POST['id'])){
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/options');
						exit;
					}
				}
			}
		} else $this->load->page_404();
	}

	public function delete_option()
	{
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0){
				$this->load->smodel('shop_model');
				if($this->shop_model->deleteOption($_POST['id'])){
					header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/options');
					exit;
				}
			}
		} else $this->load->page_404();
	}

	public function change_option_position(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id']) && is_numeric($_POST['position'])){
				$this->load->smodel('shop_model');
				if($this->shop_model->changePosition('_group_options', $_POST['id'], $_POST['position'])){
					header("Location: ".$_SERVER['HTTP_REFERER']);
					exit;
				} else $this->load->page_404();
			}
		}
	}

	public function deleteOptionProperty()
	{
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('shop_model');
				if($this->db->deleteRow($this->shop_model->table('_group_options'), $_POST['id']) && $this->db->deleteRow($this->shop_model->table('_options_name'), $_POST['id'], 'option')){
					if(isset($_POST['json']) && $_POST['json']){
						$res['result'] = true;
						header('Content-type: application/json');
						echo json_encode($res);
						exit();
					} else {
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/options');
						exit();
					}
				}
			}
		}
	}
	
}

?>