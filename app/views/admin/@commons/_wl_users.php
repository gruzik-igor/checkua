<!-- begin panel -->
<div class="panel panel-inverse" data-sortable-id="index-4">
    <div class="panel-heading">
        <h4 class="panel-title">Новозареєстровані користувачі 
            <span class="pull-right label label-success">Всіх підтверджених користувачів: <?=$this->db->getCount('wl_users', 1, 'status')?></span>
        </h4>
    </div>
    <ul class="registered-users-list clearfix">
        <?php if($users = $this->db->getAllDataByFieldInArray('wl_users', array('status' => '!3'), 'id DESC LIMIT 8'))
            foreach ($users as $user) {
                $link = 'javascript:;';
                if($_SESSION['user']->admin == 1) $link = SITE_URL.'admin/wl_users/'.$user->email;
        ?>
            <li>
                <a href="<?=$link?>"><img src="<?=SERVER_URL?>style/admin/images/user-<?=$user->type?>.jpg" alt="<?=$user->name?>"></a>
                <h4 class="username text-ellipsis">
                    <?=$user->name?>
                    <small><?=date('d.m.Y H:i', $user->registered)?></small>
                </h4>
            </li>
        <?php } ?>
    </ul>
    <?php if($_SESSION['user']->admin == 1) { ?>
        <div class="panel-footer text-center">
            <a href="<?=SITE_URL?>admin/wl_users" class="text-inverse">До всіх користувачів</a>
        </div>
    <?php } ?>
</div>
<!-- end panel -->