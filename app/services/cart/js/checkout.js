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
}

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