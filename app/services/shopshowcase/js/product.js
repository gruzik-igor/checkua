function updateProductPrice()
{
	if(productOptionsChangePrice.length > 0)
	{
		var options = [];
		for (var i = 0; i < productOptionsChangePrice.length; i++) {
			var id = productOptionsChangePrice[i];
			var value = $('input[name=product-option-'+id+']:checked').val();
			if(value != '')
				options[id] = value;
		}
		if(options.length > 0)
		{
			$.ajax({
				url: SHOP_URL+'ajaxupdateproductprice',
				type: 'POST',
				data: {
					'product' : productID,
					'options' : options
				},
				success:function(res){
					if(res.price){
						$('#product-price').html(res.price + ' грн');
					}
				}
			})
		}
	}
}