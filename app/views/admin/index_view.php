<div class="row">
    <?php if($views) {
        echo '<div class="col-md-8 ui-sortable">';
    	require_once '@commons/_wl_statistic.php';
    	echo "</div>";
    } ?>
    <div class="col-md-4 ui-sortable">
        <?php require_once '@commons/_wl_users.php'; ?>
    </div>
</div>