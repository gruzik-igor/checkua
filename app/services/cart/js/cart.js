var cart = {
	'add' : function(productKey, quantity, options_id)
	{
		if(quantity == 0)
			quantity = $("#productQuantity").val();
		if(options_id != '')
		{
			var options = [];
			for (var i = 0; i < options_id.length; i++) {
				var id = options_id[i].toString();
				var value = id + ':' + $('#product-option-' + id).val();
				options.push(value);
			}
		}
		else
			options = '';
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
					alert('Product add to cart');
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