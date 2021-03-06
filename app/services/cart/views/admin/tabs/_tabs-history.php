<div class="clearfix">
    <h4 class="left">Поточний статус: <?= $cart->status_name?></h4>
    <h4 class="right">Загальна сума: <?= $cart->total?> грн</h4>
</div>
<div class="table-responsive">
    <table class="table table-striped table-bordered nowrap" width="100%">
        <thead>
        	<tr>
        		<th>Дата</th>
    	    	<th>Статус</th>
    	    	<th>Коментар</th>
    	    	<th>Користувач</th>
        	</tr>	
        </thead>
        <tbody>
            <tr>
                <td><?= date('d.m.Y H:i',$cart->date_add)?></td>
                <td>Заявка</td>
                <td><?= $cart->comment?> </td>
                <td><?= $cart->user_name?></td>
            </tr>
        	<?php if($cart->history) foreach($cart->history as $history) {?>
        	<tr>
                <td><?= date('d.m.Y H:i', $history->date)?></td>
                <td><?= $history->status_name?></td>
                <td>
                    <span id="comment-<?= $history->id?>">
                        <?= $history->comment?> 
                    </span>
                    <span>
                        <?= $history->user > 0 ? "<button data-toggle='modal' data-target='#commentModal' data-comment='{$history->comment}' data-id='{$history->id}' class='right'><i class='fa fa-pencil-square-o'></i></button>" : '' ?>
                    </span>
                </td>
        		<td><?= $history->user_name?></td>
        	</tr>
        	<?php } ?> 
        </tbody>
    </table>
    
    <?php if($cart->shipping_id) {
        echo "<legend>Доставка</legend>";
        if(!empty($cart->shipping->name))
            echo "<p>Служба доставки: <b>{$cart->shipping->name}</b> </p>";
        if(!empty($cart->shipping->text))
            echo "<p>{$cart->shipping->text}</p>";
        else
        {
            if(!empty($cart->shipping_info['city']))
                echo "<p>Місто: <b>{$cart->shipping_info['city']}</b> </p>";
            if(!empty($cart->shipping_info['department']))
                echo "<p>Відділення: <b>{$cart->shipping_info['department']}</b> </p>";
            if(!empty($cart->shipping_info['address']))
                echo "<p>Адреса: <b>{$cart->shipping_info['address']}</b> </p>";
        }
        if(!empty($cart->shipping_info['recipient']))
            echo "<p>Отримувач: <b>{$cart->shipping_info['recipient']}</b> </p>";
        if(!empty($cart->shipping_info['phone']))
            echo "<p>Контактний телефон: <b>{$cart->shipping_info['phone']}</b> </p>";
    }

    if(!empty($cart->payment->name))
    {
        echo "<legend>Оплата</legend>";
        echo '<p>Платіжний механізм: <b>'.$cart->payment->name.'</b></p>';
        echo "<p>{$cart->payment->info}</p>";
        if(!empty($cart->payment->admin_link))
            echo "<a href='{$cart->payment->admin_link}' class='btn btn-info btn-xs'>Повна інформація по оплаті</a>";
    }

    if(!empty($cart->comment))
        echo "<div class='alert alert-warning'><h4>Побажання до замовлення</h4>{$cart->comment}</div>";

    if($cartStatuses) { ?>
    <legend>Керування замовленням</legend>
    <table class="table table-striped table-bordered nowrap" width="100%">
        <form action="<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/saveToHistory'?>" onsubmit="return saveToHistory()" method="POST" class="form-horizontal" >
            <input type="hidden" name="cart" value="<?= $cart->id?>">
            <tbody>
                <tr>
                    <th>Статус</th>
                    <td>
                        <select name="status" class="form-control" required>
                            <?php foreach($cartStatuses as $status) {?>
                            <option value="<?= $status->id?>"><?= $status->name?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th>Коментар</th>
                    <td><textarea name="comment" class="form-control" rows="5"></textarea></td>
                </tr>
                <tr>
                        <th></th>
                        <td>
                            <input type="submit" class="btn btn-md btn-success" value="Зберегти">
                            <?php if($cart->status > 1) { ?>
                                <button type="button" class="btn btn-md btn-warning pull-right" data-toggle='modal' data-target='#reNew'>Перевести до статусу "Нове замовлення"</button>
                            <?php } ?>
                        </td>
                    </tr>
            </tbody>
        </form>
    </table>
    <?php } else if($cart->status > 1) { ?>
        <button type="button" class="btn btn-md btn-warning pull-right" data-toggle='modal' data-target='#reNew'>Перевести до статусу "Нове замовлення"</button>
    <?php } ?>
</div>

<div class="modal fade" id="reNew" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="margin: 15% auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Перевести до статусу "Нове замовлення"</h4>
            </div>
            <form action="<?=SITE_URL?>admin/cart/reNew" method="post">
                <div class="modal-body">
                    <p>Увага! Ви підтверджуєте переведення замовлення до статусу "<strong>Нова, Не опрацьовано, не оплачено</strong>"?</p>
                    <p>Буде зроблено запис до історії замовлення, розблокується можливість редагувати вміст замовлення.</p>
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Пароль підтвердження переведення до нового замовлення</strong>
                        </div>
                        <div class="col-md-9">
                            <input type="password" name="password" required class="form-control" placeholder="Пароль підтвердження переведення до нового замовлення">
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <div class="pull-left">
                        <input type="hidden" name="cart" value="<?=$cart->id?>">
                        <button type="submit" class="btn btn-warning">Зберегти</button>
                    </div>
                    <button type="button" class="btn btn-success" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span> Скасувати</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="commentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" style="margin: 15% auto;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Редагувати коментар</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <label class=" col-md-3">Коментар:</label>
                        <div class="col-md-12">
                            <input type="hidden" id="historyId">
                            <textarea class="form-control" id="modalComment"  rows="5"></textarea>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" onclick="editComment()">Зберегти</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        $('#commentModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget),
                comment = button.data('comment'),
                id = button.data('id');

            var modal = $(this);
            modal.find('#modalComment').text(comment);
            modal.find('#historyId').val(id);
        });
    });

    function editComment() {
        var comment = $("#modalComment").val(),
            id = $("#historyId").val();
        $.ajax({
            url: "<?= SITE_URL.'admin/'. $_SESSION['alias']->alias.'/editComment'?>",
            type: 'POST',
            data: {
                'comment' : comment,
                'id' : id
            },
            success:function(res){
                if(res['result'] == true){
                    $("#comment-"+id).text(comment);
                    $('#commentModal').modal('hide');
                }
            }
        })
    }

    function saveToHistory() {
        if(confirm('Ви впевнені, що хочете оновити статус?')){
            return true;
        }
        return false;
    }
</script>