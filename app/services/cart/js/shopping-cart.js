$(function () {
	stepsWizard = $(".shopping-cart").steps({
        headerTag: ".header-tags",
        bodyTag: "section",
        transitionEffect: "fade",
        // forceMoveForward: true,
        labels: cartLabels,

        onInit:function (event, currentIndex) {

        	if(currentIndex == 0 && $("#mainCartEmpty").is(":visible"))
        		$("a[href=#next]").parent().addClass('disabled');
        },

        onStepChanging:function(event, currentIndex, newIndex) {
        	$('#loading').css("display", "block");
            var move = true;
            if((currentIndex == 0 && $("#mainCartEmpty").is(":visible")) || (currentIndex == 1 && newIndex > currentIndex && $("#checkUser").length > 0))
                move = false;

            if(currentIndex > newIndex && currentIndex == $(this).data('state').stepCount-1){
                move = false;
            }

            if($(this).find('.body.current').has("#shipping").length > 0 && currentIndex < newIndex || currentIndex +1 < newIndex && !$("#mainCartEmpty").is(":visible")){
                move = false;
                var shippingMethod = $("#shipping-method").val(),
                    shippingAddress = $("#shipping-cities").val() + ($("#shipping-department").hasClass('hidden') ? ' : ' + $("#shipping-department-other").val() : ' : ' + $("#shipping-department").val()),
                    shippingReceiver = $("#shipping-receiver").val(),
                    shippingPhone = $("#shipping-phone").val(),
                    shippingDefault = $("#shipping-default").val();

                if(shippingMethod && shippingAddress.length > 10 && ($("#shipping-department-other").val() || $("#shipping-department").val()) && shippingReceiver && shippingPhone ){
                    $.ajax({
                        url: SITE_URL+'cart/saveShipping',
                        type: 'POST',
                        async:false,
                        data: {
                            shippingMethod:shippingMethod,
                            shippingAddress:shippingAddress,
                            shippingReceiver:shippingReceiver,
                            shippingPhone:shippingPhone,
                            shippingDefault:shippingDefault
                        },
                        success:function(res){
                            if(res['result'] == true){
                                $(".subtotal-cost, #productsSubTotalPrice").text(res['subTotal']+" грн");

                                move = true;
                            } else {
                                alert('Помилка');
                            }
                        }
                    })
                } else {
                    $("#deliveryError").show('slow');
                    setTimeout(function () {$("#deliveryError").hide()}, 3000);
                    move = false;
                }
            }

            if($(this).find('.body.current').has("#invoice").length > 0 && currentIndex < newIndex){
                move = false;
                $.ajax({
                    url: SITE_URL+'cart/addInvoice',
                    type: 'POST',
                    async:false,
                    success:function(res){
                        if(res['result'] == true){
                            move = true;
                        } else {
                            alert('Помилка');
                        }
                    }
                })
            }

            $('#loading').css("display", "none");

            return move;
        },

        onStepChanged: function (event, current, next) {
            if (current > 0) {
                $('.actions > ul > li:first-child').attr('style', '');
            } else {
                $('.actions > ul > li:first-child').attr('style', 'display:none');
            }

            if(current == 1 && $("#checkUser").length > 0)
                $("a[href=#next]").parent().addClass('disabled');

            if(current == 2 && $('#deleteUser').length > 0){
                $(this).steps('remove', 1);
                if($(window).width() > 991)
                    $(".wizard > .steps > ul > li").attr('style', 'width: 24% !important');
            }

            $('#loading').css("display", "none");
	    },

        onFinished:function (event, currentIndex) {
            if($(this).find('.body.current').has("#invoice").length > 0){
                $.ajax({
                    url: SITE_URL+'cart/addInvoice',
                    type: 'POST',
                    success:function(res){
                        if(res['result'] == true){
                            $(".steps.clearfix, .actions.clearfix, .mCustomScrollbar #mCSB_1_container >li").remove();
                            $(".mCustomScrollbar #mCSB_1_container").append("<li><span class='text-center cart-empty'>Корзина пуста</span></li>");
                            $("#productsCount").text('0');
                            $(".subtotal-cost").text('0 грн.');
                            $(".content.clearfix").html(res['message']).addClass('cartFinish');
                        } else {
                            alert('Помилка');
                        }
                    }
                })
            }

        }

    });

    $("#clientEntry").on('click', function () {
        var emailorPhone = $('#clientEmailorPhone').val(),
            password = $("#clientPassword input[name=password]").val();

        if(emailorPhone && password){
            $.ajax({
                url: SITE_URL+'cart/clientAuthentication',
                type: 'POST',
                data: {
                    'email' : emailorPhone,
                    'password' : password
                },
                success:function(res){
                   if(res['result'] == true){
                        $("#checkUser").attr('id', 'deleteUser');
                        stepsWizard.steps("next");
                        $("#clientError").text('');
                   } else $("#clientError").text('Не правильний e-mail/телефон або пароль');
                }
            })
        } else $("#clientError").text('Введіть email або телефон');
    });

    $("#newClientRegistration").on('click',function () {
        var name = $('#newClientName').val(),
            email = $('#newClientEmail').val(),
            password = $("input[name=passwordOption]:checked").val();

        if($.trim(name) == ''){
            $("#newClientError").text("Введіть ім'я");
            return false;
        }

        switch(password){
            case '1':
                password = $("#myPasswordValue").val();
                if(password.length < 4){
                    $("#newClientError").text('Пароль повинен містити не меньше 5 символів');
                    setTimeout(function(){$("#newClientError").text('')}, 3000)
                    return false;
                }
                break;
            case '2':
                password = '2';
                break;
        }

        if(email && password){
             $.ajax({
                url: SITE_URL+'cart/clientSignUp',
                type: 'POST',
                data: {
                    'name' : name,
                    'email' : email,
                    'password' : password
                },
                success:function(res){
                    if(res['result'] == true){
                        $("#checkUser").attr('id', 'deleteUser');
                        stepsWizard.steps("next");
                        $("#newClientError").text('');
                    } else $("#newClientError").text(res['message']);
                }
            })
        } else $("#newClientError").text('Введіть email та пароль');
        setTimeout(function(){$("#newClientError").text('')}, 3000);
        return false;
    })

});

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
                                $("#checkUser").attr('id', 'deleteUser');
                                stepsWizard.steps("next");
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
}


function showPassword(action) {
    action == 'show' ? $("#password").show() : $("#password").hide();
}

