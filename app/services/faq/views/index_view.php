<!--=== Content Part ===-->
<div class="container content">		
	<div class="row">     
        <div class="col-md-9">
        <h1 class="list-title gray"><strong><?=$_SESSION['alias']->name?></strong></h1>
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
    </div><!--/row-->
</div><!--/container-->