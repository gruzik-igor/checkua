<!-- ========== Blog ========== -->
<div class="blog">
  <div class="container">
    <h3 >
    	<?php if(isset($group)){ ?>
    		<a href="<?=SITE_URL.$_SESSION['alias']->alias?>"><?=$group->alias_name?></a> -> 
    		<?php if(!empty($group->parents)){
    			$link = SITE_URL.$_SESSION['alias']->alias;
    			foreach ($group->parents as $parent) { $link .= '/'.$parent->link; ?>
					<a href="<?=$link?>"><?=$parent->name?></a> -> 
    	<?php } } } 
    		$go_own_link = false;
    		if(count($_GET) > 1){
				foreach ($_GET as $key => $value) {
					if($key != 'request' && $key != 'page' && $go_own_link == false){
						$go_own_link = $value;
						break;
					}
				}
			}
			if($go_own_link){
				$url = $this->data->url();
				$url = implode('/', $url);
				$url = SITE_URL .$url;
				echo("<a href='{$url}'>{$_SESSION['alias']->name}</a> -> {$go_own_link}");
			} else echo($_SESSION['alias']->name);
		?>
    </h3>
	<?php 
		echo html_entity_decode($_SESSION['alias']->text);
		if(isset($list) && !empty($list)){
			if($type == 'groups'){
				$url = $_SESSION['alias']->alias;
			} else {
				$url = $this->data->url();
				$url = implode('/', $url);
				if(!empty($list)){
					$options = array();
					foreach ($list as $el) {
						if(!empty($el->options)){
							foreach ($el->options as $option){
								if($option->filter == 1){
									if(isset($options[$option->id])){
										if(!in_array($option->value, $options[$option->id]->values)){
											$options[$option->id]->values[] = $option->value;
										}
									} else {
										@$options[$option->id]->id = $option->id;
										$options[$option->id]->name = $option->name;
										$options[$option->id]->link = $option->link;
										$options[$option->id]->values = array($option->value);
									}
								}
							}
						}
					}
					if(!empty($options)){
						$show_filter_block = false;
						foreach ($options as $option) {
							if(count($option->values) > 1){
								if($show_filter_block == false){
									echo('<div id="filter">');
									$show_filter_block = true;
								}
								echo('<b>'.$option->name.':</b> ');
								foreach ($option->values as $value) { ?>
									<a href="<?=SITE_URL.$url?>?<?=$option->link?>=<?=$value?>" class="filter"><?=$value?></a>
								<?php } 
							}
						}
						if($show_filter_block){
							echo('</div>');
						}
					}
				}
			}
			foreach ($list as $el) { ?>
				<div class="post">
					<?php if($el->photo > 0){ ?>
			      		<a href="<?=SITE_URL.$url.'/'.$el->link?>">
			      			<img src="<?=IMG_PATH.$_SESSION['option']->folder?>/<?=($type == 'groups')?'groups/':''?>s_<?=$el->photo?>.jpg" alt="<?=$el->name?>">
			      		</a>
			      	<?php } ?>
			      	<div class="post_text">
			        	<p class="p__title"><a href="<?=SITE_URL.$url.'/'.$el->link?>"><?=$el->name?></a></p>
				        <?php if($type != 'groups'){ ?>
					        <div class="post_info">
					        	<p><strong>Ціна: <?=$el->price?> грн</strong></p>
								<time pubdate title="Опубліковано"><?=date('d.m.Y H:i', $el->date)?></time>
					        </div>
					        <div class="post_info">
								<?php 
									if(!empty($el->options)){
										foreach ($el->options as $option) {
											echo("<p>{$option->name}: {$option->value} {$option->sufix}</p>");
										}
									} 
								?>
					        </div>
					        Наявність: <span style="color:<?=$el->availability_color?>"><?=$el->availability_name?></span>
				        <?php } else { ?>
				        	<p><?php echo mb_substr( strip_tags( html_entity_decode($el->text, ENT_QUOTES, 'utf-8') ), 0, 300, 'utf-8') ?></p>
				        <?php } ?>
				        <div class="btn_wrapper">
				          	<a href="<?=SITE_URL.$_SESSION['alias']->alias.'/'.$el->link?>" class="btn">
				            	<div class="btn_slide_wrapper">
				              	<div class="btn_main">Детальніше</div>
				              	<div class="btn_slide"><div>Детальніше</div></div>
				            	</div>
				          	</a>
				        </div>
			      	</div>
			      	<div style="clear: both"></div>
			    </div>
		<?php	}
		}
	?>
  	</div>
</div>