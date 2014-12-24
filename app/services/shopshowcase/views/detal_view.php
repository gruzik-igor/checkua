<script src="<?=SITE_URL?>assets/reveal/jquery.reveal.js"></script>
<link rel="stylesheet" href="<?=SITE_URL?>assets/reveal/reveal.css">

<?php 
	require_once '@languages/lang_'.$_SESSION['language'].'.php';
	$options = array('duration' => '', 'start' => '', 'date' => '', 'price' => '', 'include' => '', 'additional' => '', 'residence' => '', 'food' => '', 'note' => '', '1' => '', '2' => '', '34' => '', '56' => '', '79' => '', '1020' => '', '21' => '');
	$set_price = false;
	if(!empty($product->options)){
		foreach ($product->options as $option) {
			switch ($option->id) {
				case 1:
					$options['duration'] = $option;
					break;
				case 2:
					$options['start'] = $option;
					break;
				case 3:
					$options['1'] = $option;
					$set_price = true;
					break;
				case 4:
					$options['2'] = $option;
					$set_price = true;
					break;
				case 5:
					$options['34'] = $option;
					$set_price = true;
					break;
				case 6:
					$options['56'] = $option;
					$set_price = true;
					break;
				case 7:
					$options['79'] = $option;
					$set_price = true;
					break;
				case 8:
					$options['1020'] = $option;
					$set_price = true;
					break;
				case 9:
					$options['21'] = $option;
					$set_price = true;
					break;
				case 10:
					$options['date'] = $option;
					break;
				case 11:
					$options['include'] = $option;
					break;
				case 12:
					$options['additional'] = $option;
					break;
				case 13:
					$options['residence'] = $option;
					break;
				case 14:
					$options['food'] = $option;
					break;
				case 15:
					$options['note'] = $option;
					break;
			}
			// echo("<p>{$option->name}: {$option->value} {$option->sufix}</p>");
		}
	} 
?>
<div id="liniy"></div>
<div id="wrapper">
	<div id="container">
		<div id="content" role="main">
			<div id="post-1038" class="post-1038 post type-post status-publish format-standard hentry category-novinki">
				<?php if($this->userCan()){ ?>
					<a href="<?=SITE_URL?>admin/<?=$_GET['request']?>" target="_blank" style="float:right; color:#000">Редагувати</a>
				<?php } ?>
				<h1 class="entry-title"><?=$_SESSION['alias']->name?></h1>
				<div id="help_plz">

						<tr>
							<?php if($product->price) { ?>

								<div class="main_option">
									<span title="Для збірних груп">Вартість:</span>
									<span class="value"> <?=$product->price?> грн</span>
								</div>

								<div class="line"></div>

							<?php } if($options['duration'] != '') { ?>

								<div class="main_option">
									<?=$options['duration']->name?>:
									<span class="value"><?=$options['duration']->value?></span>
								</div>

								<div class="line"></div>

							<?php } if($options['start'] != '') { ?>

								<div class="main_option">
									<?=$options['start']->name?>:
									<span class="value"><?=$options['start']->value?></span>
								</div>

								<div class="line"></div>

							<?php } ?>

							<!-- <td>
								<div id="clova1">День проведення:</div>
								<p id="clova" style="font-size: 25px;margin-top: -3px;">щодня,крім понеділка</p>
							</td> -->

				</div>

				<?php
					if($photos){
						require_once '_photos_view.php';
					}
				?>

				<div class="entry-content">
	                <br>
		            <div style="width:360px;float:left; ">

						<?php
							$this->load->model('schedule_model');
							$schedule = $this->schedule_model->getSchedule($product->id);
							if($schedule){
								require_once '@languages/days_'.$_SESSION['language'].'.php';
						?>

		            	<table  class="table1" width="100%" cellspacing="0">
		                    <thead>
							    <td valign="bottom" class="title_bg">
							      <p id="clova" style="font-family: 'Viva'; font-size:16px; margin-left:18px;"><b>Найближчі дати</b></p>
							    </td>
							</thead>
	                        <table width="360px" style="text-align:center;background:#FFFBED;padding:10px 0px 10px;" cellspacing="0">
						        <?php foreach ($schedule as $s) {
						        	echo("<tr><td>".date('d.m.Y', $s->date)."</td><td>".$days[date('w', $s->date)]."</td></tr>");
						        } ?>         
							</table>
						</table>
						<?php } ?>
						<table  class="table1" width="100%" cellspacing="0">
		                    <thead>
							    <td valign="bottom" class="title_bg">
							      <p id="clova" style="font-family: 'Viva'; font-size:16px; margin-left:18px;"><b>Маршрут</b></p>
							    </td>
							</thead>
	                        <table width="360px" style="text-align:center;background:#FFFBED;padding:10px 0px 10px;" cellspacing="0">
						        <tr><td style="text-align:left;padding-left:10px;"><ol>
								 	<li>Галерея "Равлик";</li>
							     	<li>Підземелля церкви Преображення Господнього;</li>
							     	<li>Підземеллями Аптеки-музею;</li>
							     	<li>Екскурсія підземеллями Домініканського собору,збір біля автобуса,виїзд до Львівської пивоварні.</li>
							     	<li>Екскурсія на Львівській пивоварні;</li>
							     	<li>Завершення екскурсії у пивних погребах "Хмільного дому Роберта Домса". </li>
			    				</ol></td></tr>          
							</table>
						</table>
						<?php if($set_price){ ?>
			                <table class="table1" width="100%" cellspacing="0">
			                    <thead>
								    <td valign="bottom" class="title_bg">
								      <p id="clova" style="font-family: 'Viva'; font-size:16px; margin-left:18px;"><b>Індивідуальна вартість</b></p>
								    </td>
								</thead>
		                        <table width="360px" style="text-align:center; ;background:#FFFBED;" cellspacing="0">
		                        	<?php $index = array('1', '2', '34', '56', '79', '1020', '21');
		                        	foreach ($index as $i) {
		                        		if($options[$i] != '' && $options[$i]->value > 0){ ?>
		                        			<tr><td style="border-right: 2px solid #f1a655;"><?=$options[$i]->name?></td><td><?=$options[$i]->value?> <?=$options[$i]->sufix?></td> </tr>
		                        		<?php }
		                        	} ?>       
								</table>
							</table>
						<?php }
						$index = array('include', 'additional', 'residence', 'food');
                        	foreach ($index as $i) {
                        		if($options[$i] != ''){ ?>
	                        		<table class="table1" width="100%" cellspacing="0">
					                    <thead>
										    <td valign="bottom" class="title_bg">
										      <p id="clova" style="font-family: 'Viva'; font-size:16px; margin-left:18px;"><b><?=$options[$i]->name?>:</b></p>
										    </td>
										</thead>
				                        <table width="360px" style="text-align:center;background:#FFFBED;padding:10px 0px 10px;" cellspacing="0">
									        <tr ><td style="text-align:left;padding-left:10px;text-indent:10px;"><?=nl2br($options[$i]->value)?></td></tr>         
										</table>
									</table>
                        		<?php }
                        	} ?> 
				    </div>

					<div style="width:460px;float:right;" >
						<table width="100%" style="background: #fff;" >
							<tbody>
								<tr>
				    				<td align="right" class="btn_order">
				    					<br>
					  					<a style="font-size: 14px; line-height: 21px; margin: 12px;" href="#" data-reveal-id="callback">
					     					<span style="color: #333333; font-family: Georgia, 'Times New Roman', 'Bitstream Charter', Times, serif;">
					          					<img alt="" src="<?=SITE_URL?>style/images/oformyny.png" />
					     					</span>
					   					</a>&nbsp;
				    				</td>
								</tr>
								<tr>
									<td style="background: #fff; padding: 12px 22px 0px;" valign="top">

										<?php if($options['note'] != '') { ?>
															
										<div style="border: solid 1px #f1a95a;background-color:#FFFBED; width:410px;"  cellspacing="0" >
						    				<br>
								    		<h4 style="font-family: 'Viva'; color: #ac3c24; padding: 0px 20px;"><b><?=$options['note']->name?></b></h4>
								    		<div style="padding: 0 20px 0px; "><?=nl2br($options['note']->value)?></div>
								    		<br>
										</div>
										<br>

										<?php } ?>

										<?php echo html_entity_decode($_SESSION['alias']->text, ENT_QUOTES, 'utf-8') ?>
		                            </td>
		                        </tr>
		                        <tr>
				    				<td align="right" class="btn_order">
					  					<a style="font-size: 14px; line-height: 21px; margin: 12px;" href="" data-reveal-id="callback">
					     					<span style="color: #333333; font-family: Georgia, 'Times New Roman', 'Bitstream Charter', Times, serif;">
					          					<img alt="" src="<?=SITE_URL?>style/images/oformyny.png" />
					     					</span>
					   					</a>&nbsp;
				    				</td>
								</tr>
		                    </tbody>    
		                </table>

						<div class="clear:both"></div>
					</div>
				</div><!-- .entry-content -->

				<div id="hypercomments_widget"></div>
				<script type="text/javascript">
				_hcwp = window._hcwp || [];
				_hcwp.push({widget:"Stream", widget_id: 24208});
				(function() {
				if("HC_LOAD_INIT" in window)return;
				HC_LOAD_INIT = true;
				var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage || "en").substr(0, 2).toLowerCase();
				var hcc = document.createElement("script"); hcc.type = "text/javascript"; hcc.async = true;
				hcc.src = ("https:" == document.location.protocol ? "https" : "http")+"://w.hypercomments.com/widget/hc/24208/"+lang+"/widget.js";
				var s = document.getElementsByTagName("script")[0];
				s.parentNode.insertBefore(hcc, s.nextSibling);
				})();
				</script>
				<a href="http://hypercomments.com" class="hc-link" title="comments widget">comments powered by HyperComments</a>

			</div><!-- #post-## -->
			
			</div><!-- #content -->
		</div><!-- #container -->
	     <br><br>
	</div>

<div id="callback" class="reveal-modal">
	<h1>Оформити заявку</h1>
	<form action="<?=SITE_URL?>booking" method="post">
		<input type="hidden" name="excursion" value="<?=$product->id?>">
		<input type="hidden" name="excursion_name" value="<?=$product->name?>">
		<ul class="callbackForm">
			<li>
				<div class="callback-block">
					<span>Ваше имя</span>
					<input type="text" name="name" placeholder="Ваше имя" required>
				</div>
				<div class="callback-block">
					<span>Ваш email</span>
					<input type="email" name="email" placeholder="Ваш email" required>
				</div>
				<div style="clear:both"></div>
			</li>

			<li>
				<div class="callback-block">
					<span>Ваш телефон</span>
					<input type="text" name="phone" placeholder="Ваш телефон" required>
				</div>
				<div class="callback-block">
					<span>Кількість людей</span>
					<select name="people" required>
						<?php for($i = 1; $i < 11; $i++){
							echo("<option value='{$i}'>{$i}</option>");
						} ?>
					</select>
				</div>
				<div style="clear:both"></div>
			</li>

			<li>
				<span>Бажана дата</span>

				<?php if($schedule){ ?>
					<select name="date_schedule">
						<?php foreach ($schedule as $s) {
							echo("<option value='{$s->date}'>".date('d.m.Y', $s->date)." (".$days[date('w', $s->date)].")</option>");
						} ?>
					</select>
				<?php //} else { ?>
					<input id="calendar" type="text" name="date_own" value="<?=date('d.m.Y')?>">
				<?php } ?>
			</li>

			<li>
				<span>Коментарий</span>				
				<textarea name="comment" maxlength="250" placeholder="Комментарий"></textarea>
			</li>

			<li>
				<button type="submit" class="orderBtn">Отправить</button>
			</li>
		</ul>
	</form>
	<a class="close-reveal-modal">&#215;</a>
</div>

<script type='text/javascript' src='<?=SITE_URL?>assets/wonderplugincarouselskins.js'></script>
<script type='text/javascript' src='<?=SITE_URL?>assets/wonderplugincarousel.js'></script>



<!-- <script src="<?=SITE_URL?>assets/ui/jquery.ui.core.js"></script>
<script src="<?=SITE_URL?>assets/ui/jquery.ui.datepicker.js"></script> -->
<script src="<?=SITE_URL?>assets/ui/jquery.ui.datepicker-uk.js"></script>


<script>
  $( "#calendar" ).datepicker({ dateFormat: "dd.mm.yy" });
</script>
<style>
	.callback-block {
		float:left; 
		width: 250px;
	}
	.callback-block span {
		width: 80px;
	}
</style>

<?php include "app/views/@commons/_left_column.php";?>

