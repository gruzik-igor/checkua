<?php
	if(isset($products)){
		$url = SITE_URL.'tours/';

		foreach ($products as $product) if($product->photo != '') {
			if($uri != '') $url = SITE_URL.'tours/'.$uri.'/';
			else {
				$group = $this->db->getAllDataById('s_shopshowcase_groups_4', $product->group);
				if($group) $url = SITE_URL.'tours/'.$group->link.'/';
			}
		 ?>
			<div style="width:100%;height:50%;">
				<div class="featured-image-blog">
					<a href="<?=$url.$product->link?>">
						<img width="250" height="180" src="<?=IMG_PATH?>tours/<?=$product->id.'/b_'.$product->photo?>" class="attachment-post-thumbnail colorbox-1004 " alt="b_<?=$product->photo?>" title="<?=$product->name?>" />	
					</a>
					<p style="text-align: center;"><strong><a href="<?=$url.$product->link?>"><?=$product->name?></a></strong></p>
				</div>
			</div>
		<?php }
	}
?>