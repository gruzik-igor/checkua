$("#shipping-cities input").autocomplete({ source: cities });

function changeShipping(el) {

    active_shipping_method = $(el).val();

    if(shippingsInformation[active_shipping_method] != '')
    {
        $("#shipping-info").text(shippingsInformation[active_shipping_method]);
        $("#shipping-info").slideDown();
    }
    else
        $("#shipping-info").slideUp();

    $('#Shipping_to_cart').html('');
    $("#shipping-cities, #shipping-departments, #shipping-address").addClass('hidden');
    $("#shipping-cities input, #shipping-departments input, #shipping-address textarea, #Shipping_to_cart input, #Shipping_to_cart textarea").attr('required', '');

    shippingType = shippingsTypes[active_shipping_method];
    if(shippingType == '0')
    {
        $("#divLoading").addClass('show');
        $.ajax({
            url: SITE_URL + 'cart/get_Shipping_to_cart',
            type: 'POST',
            data: {
                shipping: active_shipping_method,
                ajax: true
            },
            complete: function() {
                $("div#divLoading").removeClass('show');
            },
            success: function(html) {
                $('#Shipping_to_cart').html(html);
            }
        })
    }
    else if(shippingType == '1')
    {
        $("#shipping-cities, #shipping-address").removeClass('hidden');
        $("#shipping-cities input, #shipping-address textarea").attr('required', 'required');
    }
    else if(shippingType == '2')
    {
        $("#shipping-cities, #shipping-departments").removeClass('hidden');
        $("#shipping-cities input, #shipping-departments input").attr('required', 'required');
    }
}

$("form.checkout-form input[name=recipient]").on("change", function() {
    if($(this).val() == 'other')
    {
        $("#recipientOtherName").attr('required', 'required');
        $("#recipientOtherName").attr('disabled', false);
    }
    else
    {
        $("#recipientOtherName").attr('required', false);
        $("#recipientOtherName").attr('disabled', 'disabled');
    }
});

$("form.checkout-form input[type=email]").on("change", function() {
    $("#divLoading").addClass('show');
    $.ajax({
        url: SITE_URL + 'cart/checkEmail',
        type: 'POST',
        data: {
            email: $(this).val(),
            ajax: true
        },
        complete: function() {
            $("div#divLoading").removeClass('show');
        },
        success: function(res) {
            if (res.result == true)
            {
                $('.checkout-login-form').slideDown();
                $('form.login-form input[name=email]').val(res.email);
                $('form.login-form .message').html(res.message);
                $('form.login-form #password').focus();
            }
            else
            {
                $('.checkout-login-form').slideUp();
                $('#recipientOtherName').val($('#loginName').val());
            }
        }
    })
});

$("[data-slide-toggle]").on("click", function(a) {
    a.preventDefault(), $target = $($(this).data("slide-toggle")), parent = $(this).attr("data-parent"),
        parent && $(this).parents(parent).find("[data-slide-toggle]").each(function(a, b) { $($(b).data("slide-toggle")).slideUp() }), $target.slideToggle()
});
$(".checkbox[data-slide-toggle], .radio[data-slide-toggle]").on("click", function(a) { query = $(this).hasClass("checkbox") ? "checkbox" : "radio", $input = $(this).parent().find('>input[type="' + query + '"]'), "radio" == query && $('[name="' + $input.attr("name") + '"]').attr("checked", !1).prop("checked", !1), $input.attr("checked") ? $input.attr("checked", !1).prop("checked", !1) : $input.attr("checked", !0).prop("checked", !0) });

function facebookSignUp() {
    FB.login(function(response) {
        if (response.authResponse) {
            $("#divLoading").addClass('show');
            var accessToken = response.authResponse.accessToken;
            FB.api('/me?fields=email', function(response) {
                if (response.email && accessToken) {
                    $('#authAlert').addClass('collapse');
                    $.ajax({
                        url: SITE_URL + 'signup/facebook',
                        type: 'POST',
                        data: {
                            accessToken: accessToken,
                            ajax: true
                        },
                        complete: function() {
                            $("div#divLoading").removeClass('show');
                        },
                        success: function(res) {
                            if (res['result'] == true) {
                                location.reload();
                            } else {
                                $('#authAlert').removeClass('collapse');
                                $("#authAlertText").text(res['message']);
                            }
                        }
                    })
                } else {
                    $("div#divLoading").removeClass('show');
                    $("#clientError").text('Для авторизації потрібен e-mail');
                    setTimeout(function(){$("#clientError").text('')}, 5000);
                    FB.api("/me/permissions", "DELETE");
                }
            });
        } else {
            $("div#divLoading").removeClass('show');
        }

    }, { scope: 'email' });
    return false;
}