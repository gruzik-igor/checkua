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
                <td>Не опрацьовано, не оплачено</td>
                <td></td>
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
    
    <?php if($cart->shipping_id > 0) {?>
        <legend>Доставка</legend>
        <b>Служба доставки:</b> <?= $cart->shipping->method_name ?> <br>
        <?php if($cart->shipping->method_site != '') { ?>
            <b>Сайт:</b> <?= $cart->shipping->method_site ?> <br>
        <?php } if($cart->shipping->address != '') { ?>
            <b>Адреса:</b> <?= $cart->shipping->address ?><br>
        <?php } if($cart->shipping->receiver != '') { ?>
            <b>Отримувач:</b> <?= $cart->shipping->receiver ?><br>
        <?php } if($cart->shipping->phone != '') { ?>
            <b>Контактний телефон:</b> <?= $cart->shipping->phone ?><br><br>
    <?php } }

    if(!empty($cart->payment_name))
        echo '<p>Платіжний механізм: <b>'.$cart->payment_name.'</b></p>';

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
                    <td><input type="submit" class="btn btn-md btn-success" value="Зберегти"></td>
                </tr>
            </tbody>
        </form>
    </table>
    <?php } ?>
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
        if(confirm('Ви впевнені, що хочете змінити статус?')){
            return true;
        }
        return false;
    }
</script>