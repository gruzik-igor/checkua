<?php

/*

 	Service "Shop Showcase 2.0"
	for WhiteLion 1.0

*/

class shopshowcase extends Controller {
				
    function _remap($method, $data = array())
    {
        if (method_exists($this, $method)) {
            $this->$method($data);
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
					$photos = $this->shop_model->getProductPhotos($product->id);
					
					$this->load->page_view('detal_view', array('product' => $product, 'photos' => $photos));
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
					$photos = $this->shop_model->getProductPhotos($product->id);

					$this->load->admin_view('admin/edit_product_view', array('product' => $product, 'groups' => $groups, 'photos' => $photos));
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
					$link = '';
					$id = $this->shop_model->add_product($link);
					if($id){
						$path = IMG_PATH.$_SESSION['alias']->alias.'/'.$id;
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
						if(!is_dir($path)){
							mkdir($path, 0777);
						}
						if(!empty($_FILES['photo']['name'])) {
							$data['product'] = $id;
							$data['user'] = $_SESSION['user']->id;
							$data['date'] = time();
							$data['main'] = time();
							$this->db->insertRow($this->shop_model->table('_product_photos'), $data);
							$photo_id = $this->db->getLastInsertedId();
							$photo = $link . '-' . $photo_id;
							$extension = $this->savephoto('photo', IMG_PATH.$_SESSION['option']->folder.'/'.$id.'/', $photo);
							if($extension){
								$photo .= '.'.$extension;
								$this->db->updateRow($this->shop_model->table('_products'), array('photo' => $photo), $id);
								$this->db->updateRow($this->shop_model->table('_product_photos'), array('name' => $photo), $photo_id);
							}
						}
						header("Location: ".SITE_URL.'admin/'.$_SESSION['alias']->alias.'/edit/'.$link);
						exit;
					}
				} else {
					$data = array('active' => 1, 'availability' => 1, 'date_edit' => time());
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

	public function save_product_ntkd()
    {
    	if (isset($_POST['id']) && $_POST['id'] > 0) {
    		$this->load->smodel('shop_model');
    		$product = $this->shop_model->getProductById($_POST['id']);
    		if($product){
    			$wl_ntkd_where['alias'] = $_SESSION['alias']->id;
    			$wl_ntkd_where['content'] = $product->id;
    			if(isset($_POST['language']) && in_array($_POST['language'], $_SESSION['all_languages'])){
	    			$wl_ntkd_where['language'] = $_POST['language'];
	    			$tab = $_POST['language'];

	    			$ntkdt['name'] = $this->data->post('name-'.$_POST['language']);
	    			$ntkdt['title'] = $this->data->post('title-'.$_POST['language']);
	    			$ntkdt['keywords'] = $this->data->post('keywords-'.$_POST['language']);
	    			$ntkdt['description'] = $this->data->post('description-'.$_POST['language']);
	    			$ntkdt['text'] = htmlentities($_POST['text-'.$_POST['language']], ENT_QUOTES, 'utf-8');
	    		} else {
	    			$tab = 'ntkd';

	    			$ntkdt['name'] = $this->data->post('name');
	    			$ntkdt['title'] = $this->data->post('title');
	    			$ntkdt['keywords'] = $this->data->post('keywords');
	    			$ntkdt['description'] = $this->data->post('description');
	    			$ntkdt['text'] = htmlentities($_POST['text'], ENT_QUOTES, 'utf-8');
		    	}

    			$wl_ntkd = $this->db->getAllDataById('wl_ntkd', $wl_ntkd_where);

    			if($wl_ntkd){
    				$this->db->updateRow('wl_ntkd', $ntkdt, $wl_ntkd->id);
    			} else {
    				$ntkdt['alias'] = $_SESSION['alias']->id;
	    			$ntkdt['content'] = $product->id;
	    			$ntkdt['language'] = $_POST['language'];
    				$this->db->insertRow('wl_ntkd', $ntkdt);
    			}

    			$this->shop_model->saveProductOptios($product->id);
    			$this->db->updateRow($this->shop_model->table('_products'), array('date_edit' => time()), $product->id);

    			@$_SESSION['notify']->success = 'Дані успішно оновлено!';
    			header("Location: ".SITE_URL."admin/{$_SESSION['alias']->alias}/{$product->link}#tab-{$tab}");
		    	exit;
    		}
    	}
    }
	
	function delete(){
		if($this->userCan($_SESSION['alias']->alias)){
			if(isset($_POST['id']) && is_numeric($_POST['id'])){
				$this->load->smodel('shop_model');
				$product = $this->shop_model->getProductById($_POST['id']);
				if($product){

					$this->db->deleteRow($this->shop_model->table('_products'), $product->id);
					$this->db->executeQuery("UPDATE `{$this->shop_model->table('_products')}` SET `position` = position - 1 WHERE `id` > '{$product->id}'");
					$this->db->executeQuery("DELETE FROM `wl_ntkd` WHERE `alias` = '{$_SESSION['alias']->id}' AND `content` = '{$product->id}'");
					$this->db->executeQuery("DELETE FROM `{$this->shop_model->table('_product_photos')}` WHERE `product` = '{$product->id}'");
					
					$path = IMG_PATH.$_SESSION['option']->folder.'/'.$product->id;
					if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
					$this->data->removeDirectory($path);

					$link = '';
					if($_SESSION['option']->useGroups == 1 && $_SESSION['option']->ProductMultiGroup == 0){
						$product->link = explode('/', $product->link);
						array_pop ($product->link);
						$link = '/'.implode('/', $product->link);
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
			$extension = $this->image->getExtension();
			$this->image->save();
			if($this->image->getErrors() == ''){
				if($_SESSION['option']->resize > 0){
					$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
					if($sizes){
						foreach ($sizes as $resize) if($resize->active == 1){
							$this->image->loadImage($path, $name, $extension);
							if($resize->type == 1) $this->image->resize($resize->width, $resize->height, 100);
							if($resize->type == 2) $this->image->preview($resize->width, $resize->height, 100);
							$this->image->save($path, $resize->prefix);
						}
					}
				}
				return $this->image->getExtension();
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

	// --- photo
	public function photo_add(){
		$res = array();

		if (isset($_SESSION['user']->id) && $_SESSION['user']->id > 0) {
			$id = $this->data->uri(3);
			if(is_numeric($id)){
				$path = IMG_PATH.$_SESSION['option']->folder.'/'.$id;
				$name_field = 'photos';
				$error = 0;
				if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
				if(!is_dir($path)){
					if(mkdir($path, 0777) == false){
						$error++;
						$res['error'] = 'Error create dir ' . $path;
					} 
				}
				$path = IMG_PATH.$_SESSION['option']->folder.'/'.$id.'/';

				$this->load->smodel('shop_model');
				$product = $this->db->getAllDataById($this->shop_model->table('_products'), $id);

				if($product && !empty($_FILES[$name_field]['name'][0]) && $error == 0){
					$length = count($_FILES[$name_field]['name']);
					for($i = 0; $i < $length; $i++){
						$data['product'] = $product->id;
						$data['user'] = $_SESSION['user']->id;
						$data['date'] = time();
						$data['main'] = time();
						$this->db->insertRow($this->shop_model->table('_product_photos'), $data);
						$photo_id = $this->db->getLastInsertedId();
						$photo_name = $product->link . '-' . $photo_id;
						
						$extension = $this->savephoto($name_field, $path, $photo_name, true, $i);
						if($extension){
							$photo_name .= '.'.$extension;
							$this->db->updateRow($this->shop_model->table('_products'), array('date_edit' => time(), 'photo' => $photo_name), $id);
							$this->db->updateRow($this->shop_model->table('_product_photos'), array('name' => $photo_name), $photo_id);

							$photo['id'] = $photo_id;
							$photo['name'] = '';
							$photo['date'] = date('d.m.Y H:i');
							$photo['url'] = $path.$photo_name;
							$photo['thumbnailUrl'] = $path.'/s_'.$photo_name;
							$res[] = $photo;
						} else {
							$error++;
						}
					}
				}
				if($error > 0){
					$photo['result'] = false;
					$photo['error'] = "Access Denied!";
					$res[] = $photo;
				}
			}
		}

		if(empty($res)){
			$photo['result'] = false;
			$photo['error'] = "Access Denied!";
			$res[] = $photo;
		}

		header('Content-type: application/json');
		echo json_encode(array('files' => $res));
		exit;
	}

	public function photo_save()
	{
		$res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
		if(isset($_SESSION['user']) && $_SESSION['user']->id > 0){
			if(isset($_POST['photo']) && is_numeric($_POST['photo']) && isset($_POST['name'])){
				$photo = $this->db->getAllDataById($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $_POST['photo']);
				if(!empty($photo)){
					$data = array();
					if($_POST['name'] == 'title') $data['title'] = $this->db->sanitizeString($_POST['title']);
					if($_POST['name'] == 'active'){
						$data['main'] = time();
						$this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, array('date_edit' => time(), 'photo' => $photo->name), $photo->product);
					} 
					if(!empty($data)) if($this->db->updateRow($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $data, $_POST['photo'])){
						$res['result'] = true;
						$res['error'] = '';
					}
				} else $res['error'] = 'Фотографію не знайдено!';
			}
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}
	
	public function photo_delete()
	{
		$res = array('result' => false, 'error' => 'Доступ заборонено! Тільки автор або адміністрація!');
		if(isset($_SESSION['user']) && $_SESSION['user']->id > 0){
			if(isset($_POST['photo']) && is_numeric($_POST['photo'])){
				$photo = $this->db->getAllDataById($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $_POST['photo']);
				if(!empty($photo)){
					if($this->db->deleteRow($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $_POST['photo'])){
						$path = IMG_PATH.$_SESSION['alias']->alias.'/'.$photo->product.'/';
						if(strlen(IMG_PATH) > strlen(SITE_URL)) $path = substr($path, strlen(SITE_URL));
						$prefix = array('');
						$sizes = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_photo_size', $_SESSION['alias']->id, 'alias');
						if($sizes){
							foreach ($sizes as $resize) if($resize->active == 1){
								$prefix[] = $resize->prefix.'_';
							}
						}
						foreach ($prefix as $p) {
							$filename = $path.$p.$_POST['photo'].'.jpg';
							@unlink ($filename);
						}
						$res['result'] = true;
						$res['error'] = '';

						$product = $this->db->getAllDataById($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, $photo->product);
						if($product) {
							$data['date_edit'] = time();
							if($product->photo == $photo->id){
								$data['photo'] = 0;
								$photos = $this->db->getAllDataByFieldInArray($_SESSION['service']->table.'_product_photos'.$_SESSION['alias']->table, $product->id, 'product', 'main DESC');
								if($photos) $data['photo'] = $photos[0]->id;
							}
							$this->db->updateRow($_SESSION['service']->table.'_products'.$_SESSION['alias']->table, $data, $product->id);
						}
					}
				} else $res['error'] = 'Фотографію не знайдено!';
			}
		}
		header('Content-type: application/json');
		echo json_encode($res);
		exit;
	}

	public function __show_mini_list_Products($data = array())
	{
		$group = 0;
		$my_alias = $_SESSION['alias']->id;
		$my_PerPage = $_SESSION['option']->PerPage;
		if(isset($data['group']) && is_numeric($data['group'])) $group = $data['group'];
		if(isset($data['limit']) && is_numeric($data['limit'])) $_SESSION['option']->PerPage = $data['limit'];
		if(isset($data['alias']) && is_numeric($data['alias'])) $_SESSION['alias']->id = $data['alias'];
		if(isset($data['table'])) $_SESSION['alias']->table = $data['table'];

		$this->load->smodel('shop_model');
		$products = $this->shop_model->getProducts($group);

		$this->load->view('_show_mini_list_Products', array('products' => $products, 'uri' => $data['uri']));

		$_SESSION['alias']->id = $my_alias;
		$_SESSION['option']->PerPage = $my_PerPage;
	}
	
}

?>