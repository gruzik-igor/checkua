<div id="liniy"></div>

<div id="wrapper">
    <div id="container">
    	<div style="width:840px; background-color: rgb(254, 243, 200);">

          	<div id="img_container" style="padding:20px; width:800px;">
	            <div class="visible_txt">
		            <ul> 
                        <li><p id="p_text" ><?=$_SESSION['alias']->name?></p>
                            
                        </li>
           
					</ul>		
				</div>

             	   
 				<div class="foto">
 				    <img style="width:800px; height:350px;" src="<?=IMG_PATH?>tours/<?=(isset($group))?$group->link:'tours'?>.jpg">
				</div>

               	<div class="entry-content" style="padding:0px 20px ;">
					<?=html_entity_decode($_SESSION['alias']->text)?>
				</div><!-- .entry-content -->

				<div id="content" role="main">	
					<?php 
						if($type == 'groups') require_once '_groups_view.php';
						else require_once '_list_view.php';
					?>
		        </div><!-- #content###main## --> 
		         <!-- <h3 style="font-family:'viva_viva';margin-left:350px;padding-bottom: 25px;"><a  href="<?=SITE_URL?>" style="color: #f1a655;">Повний список<span class="meta-nav">→</span></a></h3>    -->
            </div>

		</div>
	</div><!-- #container## -->
</div><!-- #wrapper-## -->

<?php include "app/views/@commons/_left_column.php"; ?>