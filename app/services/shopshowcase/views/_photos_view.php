<div class="post_gallery" style="width: 830px; height: 200px;">
			      
    <div class="wonderplugincarousel" id="wonderplugincarousel-29" 
         data-carouselid="29"
         data-width="240" 
         data-height="180" 
         data-skin="classic" 
         data-autoplay="true" 
         data-random="false" 
         data-circular="true" 
         data-responsive="true" 
         data-visibleitems="3" 
         data-arrowstyle="always" 
         data-arrowimage="arrows-32-32-2.png" 
         data-arrowwidth="32" 
         data-arrowheight="32" 
         data-navstyle="bullets" 
         data-navimage="bullet-16-16-0.png" 
         data-navwidth="16" 
         data-navheight="16" 
         data-navspacing="8" 
         data-jsfolder="<?=SITE_URL?>assets/wonderplugin-carousel/engine/" 
 
         style="display:none;position:relative;margin:0 auto;width:100%;max-width:720px;">
        <div class="amazingcarousel-list-container" style="overflow:hidden;">
            <ul class="amazingcarousel-list">
            	<?php foreach ($photos as $photo) { ?>
	                <li class="amazingcarousel-item">
	                    <div class="amazingcarousel-item-container">
	                        <div class="amazingcarousel-image">
	                            <a href="<?=IMG_PATH.$_SESSION['option']->folder.'/'.$product->id.'/'.$photo->name?>" title="<?=$photo->title?>" class="wondercarousellightbox" data-group="wondercarousellightbox-29">
	                            	<img src="<?=IMG_PATH.$_SESSION['option']->folder.'/'.$product->id.'/s_'.$photo->name?>" alt="" data-description="" />
	                            </a>
	                        </div>
	                        <div class="amazingcarousel-title"></div>
	                    </div>
	                </li>
				<?php } ?>
            </ul>
        </div>
        <div class="amazingcarousel-prev"></div>
        <div class="amazingcarousel-next"></div> 
    </div>

</div>

<style type="text/css">

	@import url(http://fonts.googleapis.com/css?family=Open+Sans);

	#wonderplugincarousel-29 .amazingcarousel-image {	
		position: relative;
		padding: 4px;
	}

	#wonderplugincarousel-29 .amazingcarousel-image img {
		display: block;
		width: 100%;
		max-width: 100%;
		border: 0;
		margin: 0;
		padding: 0;
		-moz-border-radius: 0px;
		-webkit-border-radius: 0px;
		border-radius: 0px;
		-moz-box-shadow:  0 1px 4px rgba(0, 0, 0, 0.2);
		-webkit-box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
		box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
	}

	#wonderplugincarousel-29 .amazingcarousel-title {
		position:relative;
		font:14px "Open Sans", sans-serif;
		color:#333333;
		margin:6px;
		text-align:center;
		text-shadow:0px 1px 1px #fff;
	}

	#wonderplugincarousel-29 .amazingcarousel-list-container { 
		padding: 16px 0;
	}

	#wonderplugincarousel-29 .amazingcarousel-item-container {
		text-align: center;
		padding: 4px;
		background-color: #fff;
		border: 1px solid #ddd;
		-moz-box-shadow: 0px 0px 5px 1px rgba(96, 96, 96, 0.1);
		-webkit-box-shadow: 0px 0px 5px 1px rgba(96, 96, 96, 0.1);
		box-shadow: 0px 0px 5px 1px rgba(96, 96, 96, 0.1);
	}

	#wonderplugincarousel-29 .amazingcarousel-prev {
	 background-position:-30px 0px !important;
		left: 0%;
		top: 50%;
		margin-left: -48px;
		margin-top: -16px;
	}

	#wonderplugincarousel-29 .amazingcarousel-next {
	 background-position:0px 0px !important; 
		right: 0%;
		top: 50%;
		margin-right: -48px;
		margin-top: -16px;
	}

	#wonderplugincarousel-29 .amazingcarousel-nav {
		position: absolute;
		width: 100%;
		top: 100%;
	}

	#wonderplugincarousel-29 .amazingcarousel-bullet-wrapper {
		margin: 4px auto;
	}

	@media (max-width: 600px) {
		#wonderplugincarousel-29 .amazingcarousel-nav {
			display: none;
		}
	}

</style>