$(function () {
	$('.mCustomScrollbar').mCustomScrollbar({
	    theme:"minimal",
	    scrollInertia: 300,
	    scrollEasing: "linear"
	});

});

function changeQuantity(el) {
    var $quantity = $("#cartQuantity"),
    	val = isInt($quantity.val()) ? parseInt($quantity.val()) : 1;
    switch(el.id){
        case 'buttonMinus':
            if(val > 1) $quantity.val(val-1);
            break;
        case 'buttonPlus':
            $quantity.val(val+1);
            break;
    }
}


var cart = {
	'add' : function(productId, alias, invoiceId, storageId){
		var quantity = $("#cartQuantity").val() ? $("#cartQuantity").val() : 1,
			size = $("#size>option:selected").text() != '' ? $("#size>option:selected").text() : '';
			invoiceId = invoiceId || 0,
			storageId = storageId || 0;
		$.ajax({
			url: SITE_URL+'cart/productAdd',
			type: 'POST',
			data: {
				'productId' : productId,
				'alias' : alias,
				'invoiceId' : invoiceId,
				'storageId' : storageId,
				'quantity' : quantity,
				'size' : size
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

	'remove' : function (productId, invoiceId, storageId) {
		$.ajax({
			url: SITE_URL+'cart/removeProduct',
			type: 'POST',
			data: {
				'productId' : productId,
				'invoiceId' : invoiceId,
				'storageId' : storageId
			},
			success:function(res){
				if(res){
					$("#productsCount").text(res['productsCount']);
					$(".subtotal-cost, #productsSubTotalPrice").text(res['subTotal']+' грн');
					$("#productsSubTotalPriceUAH").text(res['subTotal'] + ' грн');

					$("#cartProduct-"+productId+'-'+invoiceId+'-'+storageId).remove();
					$("#mainCartProduct-"+productId+'-'+invoiceId+'-'+storageId).remove();

					if(res['productsCount'] == 0){
						$(".mCustomScrollbar #mCSB_1_container").append("<li><span class='text-center cart-empty'>Корзина пуста</span> </li>");
						$("#mainCartEmpty").show();
						$("a[href=#next]").parent().addClass('disabled');
					}

				}
			}
		})
	},

	'update' : function(productId, invoiceId, storageId, e){
		$('.alert').removeClass('in');
		var $button = $(e.target),
			$productQuantity = $("#productQuantity-"+productId+'-'+invoiceId+'-'+storageId),
			$productTotalPrice = $("#productTotalPrice-"+productId+'-'+invoiceId+'-'+storageId),
			$productPrice = $("#productPrice-"+productId+'-'+invoiceId+'-'+storageId),
			$cartProductQuantity = $("#cartProductQuantity-"+productId+'-'+invoiceId+'-'+storageId);
		if(isInt($productQuantity.val())){
			switch($button.val()){
				case '+':
					var quantity = parseInt($button.prev().val())+1;
					break;
				case '-':
					if($productQuantity.val() > 1)
						var quantity = parseInt($productQuantity.val())-1;
					else
						return false;
					break;
				default:
					var quantity = $productQuantity.val();
					break;
			}

			$.ajax({
				url: SITE_URL+'cart/updateProduct',
				type: 'POST',
				data: {
					'productId' : productId,
					'invoiceId' : invoiceId,
					'storageId' : storageId,
					'quantity' : quantity
				},
				success:function(res){
					if(res['result'] == true){
						$productQuantity.val(res['quantity']);
						$productTotalPrice.text(res['productTotalPrice']+ " грн");
						$cartProductQuantity.text(res['quantity'] + ' x ' + $productPrice.text());
						$(".subtotal-cost, #productsSubTotalPrice").text(res['subTotal']+' грн');
						$("#productsSubTotalPriceUAH").text(res['subTotal'] + ' грн');
						$("#productsCount").text(res['productsCount']);
					} else {
						$("#maxQuantity").text(res['maxQuantity'])
						$('.alert').addClass('in');
					}
				}
			})
		}
	}
}

function isInt(n) {
    return +n === parseInt(n) && !(n % 1);
}