<link href="<?=SITE_URL?>style/style.min.css" rel="stylesheet" />

<div id="page-title" class="page-title has-bg">
    <div class="bg-cover"><img src="<?=SITE_URL?>assets/img/cover.jpg" alt="" /></div>
</div>
<div class="container">
    <div class="row">
        <div style="padding-top: 30px">
        <?php if($articles) {
            foreach ($articles as $article){?>
         <?php if($groups) {
         foreach ($groups as $group) {
                    if ($article->group_name == $group->name){?>
            <div class="panel panel-forum">
                <div class="panel-heading">
                <h4 class="panel-title"><a href="<?=SITE_URL.'forum/'.$group->alias?>">Категорія: <?=html_entity_decode($article->group_name)?></a></h4>
                </div>
                <ul class="forum-list">

                    <li>
                        <div class="media">
                            <img src="<?=IMG_PATH?>Fake.png" alt="">
                        </div>
                        <div class="info-container">
                            <div class="info">
                                <h4 class="title"><a href="<?=SITE_URL.'forum/'.$article->alias?>"><?=$article->name?></a></h4>
                                <p class="desc">
                                    <?=$article->list?>                            
                                </p>
                            </div>
                            <div>
                                <p>Тему додано <?=date("d.m.Y H:i", $article->date_add )?></p>
                                <p>Востаннє редаговано <?=date("d.m.Y H:i", $article->date_edit )?></p>
                                <p>Автор: <?=$article->user_name?></p>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>
            <?php } } } ?>
            <?php } } ?>

        </div>
    </div>
</div>