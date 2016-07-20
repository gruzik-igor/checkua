<?php 
if(isset($list)){
	
	$url = $this->data->url();
	$url = implode('/', $url);

	foreach ($list as $el) { 
			$options = array('duration' => '', 'start' => '');
			if(!empty($el->options)){
				foreach ($el->options as $option) {
					switch ($option->id) {
						case 1:
							$options['duration'] = $option;
							break;
						case 2:
							$options['start'] = $option;
							break;
					}
				}
			}
		?>		
        <div id="post-961" class="post type-post status-publish format-standard hentry category-poxodi-karpatami">
			<h2 class="entry-title"><a href="<?=SITE_URL.$url.'/'.$el->link?>" title="<?=$el->name?>" rel="bookmark"><?=$el->name?></a></h2>
			<table style="background:#fff; " width="100%">
				<tr>
					<?php if($el->photo != ''){ ?>
						<td valign="top" style="padding:10px;width:360px">
							<div class="featured-image-blog">
								<img src="<?=IMG_PATH.$_SESSION['option']->folder?>/<?=($type == 'groups') ? 'groups/s_'.$el->photo.'.jpg' : $el->id.'/m_'.$el->photo?>" class="attachment-post-thumbnail colorbox-961 " alt="<?=$el->name?>" />	
							</div>			
						</td>
					<?php } ?>
					<td valign="top" style="padding:10px;padding-left: 0px;width:350px">
						<div class="entry-summary" >
							<?php echo mb_substr( strip_tags( html_entity_decode($el->text, ENT_QUOTES, 'utf-8') ), 0, 300, 'utf-8') ?>
							<a href="<?=SITE_URL.$url.'/'.$el->link?>">Читати повністю <span class="meta-nav">→</span></a>		
						</div>
					</td>
				</tr>
			</table>
			<div id="help_plz">
				<table width="100%">
					<tr>
						<td>
							<?php $i = 0; if($el->price) { $i++; ?>

								<div class="main_option">
									<span title="Для збірних груп">Вартість:</span>
									<span class="value"> <?=$el->price?> грн</span>
								</div>

								<div class="line"></div>

							<?php } if($options['duration'] != '') { $i++; ?>

								<div class="main_option">
									<?=$options['duration']->name?>:
									<span class="value"><?=$options['duration']->value?></span>
								</div>

								<div class="line"></div>

							<?php } if($options['start'] != '' && $i < 2) { ?>

								<div class="main_option">
									<?=$options['start']->name?>:
									<span class="value"><?=$options['start']->value?></span>
								</div>

								<div class="line"></div>

							<?php } ?>
						</td>
						<td width="250" align="right">
							<a href="<?=SITE_URL.$url.'/'.$el->link?>"><img style="margin-top: -2px;" src="<?=SITE_URL?>style/images/oformyny.png"></a>
						</td>
					</tr>
				</table>
			</div>
	    </div>
	    		<?php	}
			} ?>

	    <?php /* if(isset($group)){ ?>
	<a href="<?=SITE_URL.$_SESSION['alias']->alias?>"><?=$group->alias_name?></a> -> 
	<?php if(!empty($group->parents)){
		$link = SITE_URL.$_SESSION['alias']->alias;
		foreach ($group->parents as $parent) { $link .= '/'.$parent->link; ?>
			<a href="<?=$link?>"><?=$parent->name?></a> -> 
<?php } } } 
			    */ ?>