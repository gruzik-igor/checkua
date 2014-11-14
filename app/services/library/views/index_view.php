<?php if(empty($_GET['page']) && $this->data->uri(1) == ''){
 $this->load->smodel('articles_model'); ?>

<!-- ========== Info Columns ========== -->
    <div class="info-col">
      <div class="container">
        <div class="row">
          <div class="grid_4">
            <div class="box">
              <div class="maxheight">
                <div class="info-col_block">
                  <h3><a href="<?=$_SESSION['alias']->alias.'/'.$categories[0]->link?>"><?=$categories[0]->name?></a></h3>
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores.</p>
                  <?php $list = $this->articles_model->getArticles($categories[0]->id);
                  if(!empty($list)){ ?>
                  <ul class="list">
                  	<?php $i = 0; foreach ($list as $article) { $i++; ?>
                    	<li><a href="<?=$_SESSION['alias']->alias.'/'.$categories[0]->link.'/'.$article->link?>"><?=$article->name?></a></li>
                    <?php if($i == 4) break; } ?>
                  </ul>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div class="grid_4">
            <div class="box">
              <div class="maxheight">
                <div class="info-col_block">
                  <h3><a href="<?=$_SESSION['alias']->alias.'/'.$categories[1]->link?>"><?=$categories[1]->name?></a></h3>
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores.</p>
                  <?php $list = $this->articles_model->getArticles($categories[1]->id);
                  if(!empty($list)){ ?>
                  <ul class="list">
                  	<?php $i = 0; foreach ($list as $article) { $i++; ?>
                    	<li><a href="<?=$_SESSION['alias']->alias.'/'.$categories[1]->link.'/'.$article->link?>"><?=$article->name?></a></li>
                    <?php if($i == 4) break; } ?>
                  </ul>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
          <div class="grid_4">
            <div class="box">
              <div class="maxheight">
                <div class="info-col_block">
                  <h3><a href="<?=$_SESSION['alias']->alias.'/'.$categories[2]->link?>"><?=$categories[2]->name?></a></h3>
                  <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores.</p>
                  <?php $list = $this->articles_model->getArticles($categories[2]->id);
                  if(!empty($list)){ ?>
                  <ul class="list">
                  	<?php $i = 0; foreach ($list as $article) { $i++; ?>
                    	<li><a href="<?=$_SESSION['alias']->alias.'/'.$categories[2]->link.'/'.$article->link?>"><?=$article->name?></a></li>
                    <?php if($i == 4) break; } ?>
                  </ul>
                  <?php } ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

<?php } require_once('articles_view.php'); ?>