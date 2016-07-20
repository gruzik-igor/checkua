<!--=== Breadcrumbs ===-->
<div class="breadcrumbs">
    <div class="container">
        <h1 class="pull-left"><?=$_SESSION['alias']->name?></h1>
        <ul class="pull-right breadcrumb">
            <li><a href="<?=SITE_URL?>"><?=$this->text('Головна сторінка')?></a></li>
            <li class="active"><?=$_SESSION['alias']->name?></li>
        </ul>
    </div>
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->

<!--=== Content Part ===-->
<div class="container content">		
	<div class="row">            
        <div class="col-md-9">
            <?php 
            if($groups) 
            {
                foreach($groups as $group){ ?>
                    <div class="headline"><h2><?=$group->name?></h2></div>
                    <div class="panel-group acc-v1 margin-bottom-40" id="<?=$group->alias?>">
                        <?php if($faqs) { 
                            foreach($faqs as $faq) { 
                                if($faq->group == $group->id){ ?>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title">
                                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#<?=$group->alias?>" href="#<?=$faq->id.'-'.$faq->alias?>">
                                                    <?=$faq->question?>
                                                </a>
                                            </h4>
                                        </div>
                                        <?php
                                        $active = false;
                                        if($this->data->uri(1) == $group->alias && $this->data->uri(2) == $faq->alias) $active = true;
                                        ?>
                                        <div id="<?=$faq->id.'-'.$faq->alias?>" class="panel-collapse collapse <?=($active) ? 'in' : ''?>">
                                            <div class="panel-body">
                                                <?=html_entity_decode($faq->answer)?>
                                            </div>
                                        </div>
                                    </div>
                        <?php   }
                            }
                        } ?>
                    </div>
            <?php
                }
            } else {
                if($faqs) { 
                    foreach($faqs as $faq) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title">
                                    <a class="accordion-toggle" data-toggle="collapse" href="#<?=$faq->id.'-'.$faq->alias?>">
                                        <?=$faq->question?>
                                    </a>
                                </h4>
                            </div>
                            <?php
                            $active = false;
                            if($this->data->uri(1) == $faq->alias) $active = true;
                            ?>
                            <div id="<?=$faq->id.'-'.$faq->alias?>" class="panel-collapse collapse <?=($active) ? 'in' : ''?>">
                                <div class="panel-body">
                                    <?=html_entity_decode($faq->answer)?>
                                </div>
                            </div>
                        </div>
        <?php       }
                }
            }
        ?>
		</div><!--/col-md-9-->
        
            <div class="col-md-3">
                <!-- Contacts -->
                <div class="headline"><h2><?=$this->text('Контакти')?></h2></div>
                <ul class="list-unstyled who margin-bottom-30">
                    <li><i class="fa fa-home"></i><?=$this->text('вул. Любінська 92, Будинок меблів,')?><br><?=$this->text('м. Львів, Україна')?></li>
                    <li><i class="fa fa-envelope"></i><?=$this->text('Email: info@webspirit.com.ua')?></li>
                    <li><i class="fa fa-phone"></i><?=$this->text('тел: +38 067 3141471')?> </li>
                    <li><i class="fa fa-phone"></i><?=$this->text('тел: +38 093 1289017')?> </li>
                </ul>

                <!-- Business Hours -->
                <div class="headline"><h2><?=$this->text('Ми працюємо')?></h2></div>
                <ul class="list-unstyled margin-bottom-30">
                    <li><strong><?=$this->text('Понеділок-П\'ятниця:')?></strong><?=$this->text('з 10:00 до 19:00')?> </li>
                    <li><strong><?=$this->text('Субота: ')?></strong><?=$this->text(' з 11:00 до 16:00')?></li>
                    <li><strong><?=$this->text('Неділя: ')?></strong><?=$this->text(' Ми відпочиваємо :)')?></li>
                </ul>

                <!-- Why we are? -->
                <div class="headline"><h2><?=$this->text('Чому - ми?')?></h2></div>
                <p><?=$this->text('Задоволений замовник - це безцінно, отже ми працюємо на совість. Зрештою, нам просто подобається працювати якісно, швидко, відповідально і водночас творчо над кожним проектом.')?></p> 
            </div><!--/col-md-3-->            		
    </div><!--/row-->
</div><!--/container-->