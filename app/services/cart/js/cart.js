var cart = {
	'add' : function(productKey, quantity, options)
	{
		if(quantity == 0)
			quantity = $("#productQuantity").val();
		$.ajax({
			url: SITE_URL+'cart/addProduct',
			type: 'POST',
			data: {
				'productKey' : productKey,
				'quantity' : quantity,
				'options' : options
			},
			success:function(res){
				if(res){
					$("#productsCount").text(res['productsCount']);
					$(".subtotal-cost").text(res['subTotal']+" грн");

					$(".mCustomScrollbar li:has(span.cart-empty)").remove();
					var image = res['m_photo'] ? "<img class='img-responsive' src="+res['m_photo']+">" : '';
					if($("#cartProduct-"+productId+'-'+invoiceId+'-'+storageId).length > 0){
						$("#cartProduct-"+productId+'-'+invoiceId+'-'+storageId).remove();
					}
					$(".mCustomScrollbar #mCSB_1_container").append("<li id='cartProduct-"+productId+'-'+invoiceId+'-'+storageId+"'>"+image+"<button type='button' class='close' onclick='cart.remove("+res['productId']+','+invoiceId+','+storageId+")'>×</button><div class='overflow-h'><span>"+res['name']+"</span><small>"+res['quantity']+' x '+res['price']+" грн</small></div></li>");

					$(".g-popup-wrapper .g-image").empty().append(image);
					$(".g-popup-wrapper .g-name").empty().append(res['name']+'<br>'+res['price']+' грн');
					$(".g-popup-wrapper").css('display', 'block');
				}
			}
		})
	},

	'remove' : function (id, e)
	{
		$('#cart_notify').removeClass('in');
		$.ajax({
			url: SITE_URL+'cart/removeProduct',
			type: 'POST',
			data: {
				'id' : id
			},
			success:function(res){
				if(res['result'] == true)
				{
					e.closest('.product').remove();
					$('#subTotal').text(res['subTotal']);
				}
				else
				{
					$("#cart_notify p").text(res['error'])
					$('#cart_notify').addClass('in');
				}
			}
		})
	},

	'update' : function(id, e)
	{
		$('#cart_notify').removeClass('in');
		var $button = $(e.target),
			$productQuantity = $("#productQuantity-"+id).val();
		if(isInt($productQuantity))
		{
			switch($button.val())
			{
				case '+':
					var quantity = parseInt($productQuantity) + 1;
					break;
				case '-':
					if($productQuantity > 1)
						var quantity = parseInt($productQuantity) - 1;
					else
						return false;
					break;
				default:
					var quantity = $productQuantity;
					break;
			}

			$.ajax({
				url: SITE_URL+'cart/updateProduct',
				type: 'POST',
				data: {
					'id' : id,
					'quantity' : quantity
				},
				success:function(res)
				{
					$("#productQuantity-"+id).val(res['quantity']);
					if(res['result'] == true)
						$('#subTotal').text(res['subTotal']);
					else
					{
						if(res.max)
							$("#productQuantity-"+id).attr('max', res['max']);
						$("#cart_notify p").text(res['error'])
						$('#cart_notify').addClass('in');
					}
				}
			})
		}
	}
}

function isInt(n) {
    return +n === parseInt(n) && !(n % 1);
}

function changeQuantity(el) {
    var $quantity = $("#productQuantity"),
    	val = isInt($quantity.val()) ? parseInt($quantity.val()) : 1;
    switch(el.innerText){
        case '-':
            if(val > 1) $quantity.val(val-1);
            break;
        case '+':
            $quantity.val(val+1);
            break;
    }
}