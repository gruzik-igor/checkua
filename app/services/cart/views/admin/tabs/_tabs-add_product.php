<div class="row">
	<input type="hidden" id="userType" value="<?= isset($cartInfo->user_type) ? $cartInfo->user_type : $_SESSION['option']->newUserType ?>">
	<div class="col-md-5" id="newProduct" hidden>
		<table class="table table-striped table-bordered nowrap" width="100%" id="cartAddProduct">
			<tbody>
				<tr>
					<th>Артикул товару</th>
					<td>
						<div class="input-group">
							<input type="text" name="article" id="productArticle" class="form-control" required="">
							<span class="input-group-btn">
	    						<button type="submit" class="btn btn-secondary" onclick="getProduct()">Знайти</button>
	    					</span>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
		<h4 id="invoicesError" style="color:red; text-align:center"></h4>
	</div>
	<div class="col-md-12" id="productInvoices" hidden>
	</div>

</div>

<script>
	document.addEventListener('DOMContentLoaded', function(){
		var navTabs = $('.nav-tabs a');
	    var hash = window.location.hash;
	    if (navTabs && location.hash != '')
	    	$('a[href="'+location.hash+'"]').tab('show');

	    $("#productArticle").keypress(function (e) {
	    	if(e.keyCode == 13){
	    		getProduct();
	    	}
	    })
	});

	function getProduct () {
		var article = $("#productArticle").val(),
			cartId = <?= isset($cartInfo) ? $cartInfo->id : '""'?>,
			userId = $("#userId").val() != undefined ? $("#userId").val() : false ;
			userType = $("#userType").val();

		if(!article) return false;

	    $('#saveing').css("display", "block");
	    $("#invoicesError").text('');

	    $.ajax({
	        url: "<?= SITE_URL."admin/".$_SESSION['alias']->alias?>/getProductByArticle",
	        type: 'POST',
	        data: {
	            product: article,
	            cartId : cartId,
	            userType: userType,
	            userId : userId,
	            json: true
	        },
	        success: function(res) {
	            if(res) {
	            	$("#productInvoices").slideDown('slow').html(res);
	            } else {
	            	$("#productInvoices").html('');
	            	$("#invoicesError").text('Жодного товару не знайдено');
	            }
	            $('#saveing').css("display", "none");
	        },
	        error: function(){
	            alert("Помилка! Спробуйте ще раз!");
	            $('#saveing').css("display", "none");
	        },
	        timeout: function(){
	            alert("Помилка: Вийшов час очікування! Спробуйте ще раз!");
	            $('#saveing').css("display", "none");
	        }
	    });
	}
</script>