<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Дата/час</th>
            <th>Розділ</th>
            <th>Сторінка</th>
        </tr>
    </thead>
	<tbody>
        <?php if($likes_list) foreach ($likes_list as $like) { ?>
            <tr>
                <td><?=date('d.m.y H:i', $like->date_update)?></td>
                <td><?=$like->alias_name?></td>
                <td>
                    <?php if($page = $this->load->function_in_alias($like->alias, '__get_Search', $like->content))
                            echo('<a href="'.SITE_URL.$page->link.'" target="_blank">'.$like->page_name.'</a>');
                        else
                            echo $like->page_name;
                    ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>
<?php
$this->load->library('paginator');
echo $this->paginator->get();
?>