var cart = {
	'add' : function(productKey, quantity, options_id)
	{
		var alertOptions = [];
		if(quantity == 0)
			quantity = $("#productQuantity").val();
		if(options_id != '')
		{
			var options = [];
			for (var i = 0; i < options_id.length; i++) {
				var id = options_id[i].toString();
				var elem = $('[name=product-option-' + id + ']:checked');
				var value = elem.val();
				if(!value)
				{
					var name = $('#product-option-name-' + id).text();
					alertOptions.push(name);
				}
				else
				{
					if(elem.data('id'))
						id = elem.data('id');
					value = id + ':' + value;
					options.push(value);
				}
			}
		}
		else
			options = '';
		if(alertOptions.length)
		{
			var text = 'Властивості товару';
			for (var i = 0; i < alertOptions.length; i++) {
				text += ' ' + alertOptions[i];
			}
			text += ' не вказані';
			alert (text);
		}
		else

		$.ajax({
			url: SITE_URL+'cart/addProduct',
			type: 'POST',
			data: {
				'productKey' : productKey,
				'quantity' : quantity,
				'options' : options
			},
			success:function(res){
				if(res.result)
				{
					var product_exist = document.getElementById('product-'+res.product.key);
					if(product_exist)
					{
						$('#product-'+res.product.key + ' span.amount').text(res.product.priceFormat+' x '+res.product.quantity);
					}
					else
					{
						var li = document.createElement('li');
						li.id = 'product-'+res.product.key;

						var a = document.createElement('a');
						a.href = SITE_URL + res.product.link;

						var a_img = a.cloneNode(true);
						var img = document.createElement('img');
						img.src = SITE_URL + 'images/' + res.product.admin_photo;
						img.className = 'img-responsive product-img';
						a_img.appendChild(img);
						li.appendChild(a_img);

						var div = document.createElement('div');
						div.className = 'product-details';

						var p_title = document.createElement('p');
						p_title.className = 'product-title clearfix';
						a.innerText = res.product.name;
						p_title.appendChild(a);
						div.appendChild(p_title);

						var p_price = document.createElement('p');
						p_price.className = 'product-price clearfix';
						p_price.innerHTML = '<span class="amount">'+res.product.priceFormat+' x '+res.product.quantity+'</span>';
						div.appendChild(p_price);

						li.appendChild(div);

						var minicart_in = document.getElementById('slimScrollDiv');
						minicart_in.insertBefore(li, minicart_in.firstChild);
					}

					var minicart = document.getElementById('shopping-cart-in-menu');
					minicart.className = 'shopping-cart open';
					$('.cart-empty').remove();

					// document.getElementById('subTotal').innerText = res.subTotal;
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
					$('.subTotal').text(res['subTotal']);
					if($('.row.product').length == 0)
						$('.subTotal.price + input').attr('disabled', true);
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
					{
						$('.subTotal').text(res['subTotal']);
						$('#product-'+id + ' span.amount').text(res.priceFormat+' x '+quantity);
					}
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