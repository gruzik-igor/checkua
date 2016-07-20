<?php

class wl_Video extends Controller {
				
    function _remap($method)
    {
        if (method_exists($this, $method)) {
            $this->$method();
        } else {
            $this->index($method);
        }
    }

    function index(){
    	header("Location: ".SITE_URL);
    	exit();
	}
	
	function save(){
		if(isset($_POST['alias']) && is_numeric($_POST['alias']) && isset($_POST['content']) && is_numeric($_POST['content']) && $_POST['video'] != ''){
			$videolink = $this->data->post('video', true); 
			$controler_video=parse_url($videolink);
			$site = '';
			if(!empty($controler_video['host'])){			
				if (($controler_video['host']=="youtu.be") || ($controler_video['host']=="www.youtube.com") || ($controler_video['host']=="youtube.com")) {
				$site="youtube";
					if ($controler_video['host']=="youtu.be"){
						$site_link=substr($controler_video['path'],1);
					} else{
						$first_marker = strpos( $controler_video['query'], '=')+1;
						$second_marker=strpos( $controler_video['query'], '&');
						if($second_marker != '') {$second_marker -=2;
							$site_link=substr($controler_video['query'],$first_marker,$second_marker);
						} else $site_link=substr($controler_video['query'],$first_marker);
					}
				}
				elseif (($controler_video['host']=="vk.com") || ($controler_video['host']=="vkontakte.ru")){
					//$site="vkontakte";
				}
				elseif ($controler_video['host']=="vimeo.com"){
					$site="vimeo";
					$site_link=substr($controler_video['path'],1);
				}
			}
			if($site != ''){
				$data['author'] = $_SESSION['user']->id;
				$data['date_add'] = time();
				$data['alias'] = $_POST['alias'];
				$data['content'] = $_POST['content'];
				$data['site'] = $site;
				$data['link'] = $site_link;
				$data['active'] = 1;

				if($this->db->insertRow('wl_video', $data)){
					header('Location: '.$_SERVER['HTTP_REFERER'].'#tab-video');
					exit;
				}
			} else {
				$_SESSION['notify'] = new stdClass();
				$_SESSION['notify']->errors = 'Невірна адреса відео. Підтримуються сервіси youtu.be, youtube.com, vimeo.com!';
				header('Location: '.SITE_URL.$_SERVER['HTTP_REFERER']);
				exit;
			}
		} else $this->load->page_404();
	}

	public function delete()
	{
		if($this->userCan() && isset($_GET['id']) && is_numeric($_GET['id'])){
			$this->db->deleteRow('wl_video', $_GET['id']);
			header('Location: '.$_SERVER['HTTP_REFERER'].'#tab-video');
			exit;
		} else $this->load->page_404();
	}
}
?>