<!-- begin row -->
<div class="row">
    <!-- begin col-12 -->
    <div class="col-md-12">
        <div class="result-container">
            <div class="input-group m-b-20">
                <input type="text" class="form-control input-white" placeholder="Ключове слово пошуку..." value="<?=$this->data->get('by')?>" />
                <div class="input-group-btn">
                    <button type="button" class="btn btn-inverse"><i class="fa fa-search"></i> Пошук</button>
                    <button type="button" class="btn btn-inverse dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-cog"></i>
                    </button>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="javascript:;" title="Скасувати фільтр">Пошук всюди</a></li>
                        <?php if($_SESSION['language']) { ?>
                            <li class="divider"></li>
                            <li><a href="javascript:;" title="Включення результату видачі на всіх мовах">Пошук у всіх мовах</a></li>
                            <li><a href="javascript:;">Пошук тільки <strong>UA</strong></a></li>
                            <li><a href="javascript:;">Пошук тільки <strong>RU</strong></a></li>
                            
                        <?php }
                        if($wl_aliases) { ?>
                            <li class="divider"></li>
                            <li><a href="javascript:;" title="Скасувати фільтр">Пошук у всіх розділах</a></li>
                            <?php
                            foreach ($wl_aliases as $wl_alias)
                                if($this->userCan($wl_alias->alias)) {
                            ?>
                                <li><a href="javascript:;">Пошук тільки <strong><?=$wl_alias->name?></strong></a></li>
                            <?php }
                        } ?>
                    </ul>
                </div>
            </div>
            <div class="dropdown pull-left">
                <a href="javascript:;" class="btn btn-white btn-white-without-border dropdown-toggle" data-toggle="dropdown">
                    Сортувати за <span class="caret m-l-5"></span>
                </a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="javascript:;">Авто</a></li>
                    <li class="divider"></li>
                    <li><a href="javascript:;">Датою останнього редагування <i class="fa fa-level-down"></i></a></li>
                    <li><a href="javascript:;">Датою останнього редагування <i class="fa fa-level-up"></i></a></li>
                    <li class="divider"></li>
                    <li><a href="javascript:;">Назві аА..яЯ <i class="fa fa-level-down"></i></a></li>
                    <li><a href="javascript:;">Назві яЯ..аА <i class="fa fa-level-up"></i></a></li>
                    <li class="divider"></li>
                    <li><a href="javascript:;">Перегляди від найбільших <i class="fa fa-level-down"></i></a></li>
                    <li><a href="javascript:;">Перегляди від найменших <i class="fa fa-level-up"></i></a></li>
                </ul>
            </div>
            <ul class="pagination pagination-without-border pull-right m-t-0">
                <li class="disabled"><a href="javascript:;">«</a></li>
                <li class="active"><a href="javascript:;">1</a></li>
                <li><a href="javascript:;">2</a></li>
                <li><a href="javascript:;">3</a></li>
                <li><a href="javascript:;">4</a></li>
                <li><a href="javascript:;">5</a></li>
                <li><a href="javascript:;">6</a></li>
                <li><a href="javascript:;">7</a></li>
                <li><a href="javascript:;">»</a></li>
            </ul>
            <ul class="result-list">
                <li>
                    <div class="result-image">
                        <a href="javascript:;"><img src="assets/img/gallery/gallery-1.jpg" alt="" /></a>
                    </div>
                    <div class="result-info">
                        <h4 class="title"><a href="javascript:;">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</a></h4>
                        <p class="location">United State, BY 10089</p>
                        <p class="desc">
                            Nunc et ornare ligula. Aenean commodo lectus turpis, eu laoreet risus lobortis quis. Suspendisse vehicula mollis magna vel aliquet. Donec ac tempor neque, convallis euismod mauris. Integer dictum dictum ipsum quis viverra.
                        </p>
                        <div class="btn-row">
                            <a href="javascript:;" data-toggle="tooltip" data-container="body" data-title="Analytics"><i class="fa fa-fw fa-bar-chart-o"></i></a>
                            <a href="javascript:;" data-toggle="tooltip" data-container="body" data-title="Tasks"><i class="fa fa-fw fa-tasks"></i></a>
                            <a href="javascript:;" data-toggle="tooltip" data-container="body" data-title="Configuration"><i class="fa fa-fw fa-cog"></i></a>
                            <a href="javascript:;" data-toggle="tooltip" data-container="body" data-title="Performance"><i class="fa fa-fw fa-tachometer"></i></a>
                            <a href="javascript:;" data-toggle="tooltip" data-container="body" data-title="Users"><i class="fa fa-fw fa-user"></i></a>
                        </div>
                    </div>
                    <div class="result-price">
                        $92,101 <small>PER MONTH</small>
                        <a href="javascript:;" class="btn btn-inverse btn-block">View Details</a>
                    </div>
                </li>
            </ul>
            <div class="clearfix">
                <ul class="pagination pagination-without-border pull-right">
                    <li class="disabled"><a href="javascript:;">«</a></li>
                    <li class="active"><a href="javascript:;">1</a></li>
                    <li><a href="javascript:;">2</a></li>
                    <li><a href="javascript:;">3</a></li>
                    <li><a href="javascript:;">4</a></li>
                    <li><a href="javascript:;">5</a></li>
                    <li><a href="javascript:;">6</a></li>
                    <li><a href="javascript:;">7</a></li>
                    <li><a href="javascript:;">»</a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- end col-12 -->
</div>
<!-- end row -->