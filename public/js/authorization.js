"use strict";

var Authorization =
{
    init: function ()
    {
        Authorization.login();
    },

    //enter site
    login: function()
    {
        jQuery("[name=authorization_form]").validate({
            // Rules for form validation
            rules : {
                email : {
                    required : true,
                    email : true
                },
                password : {
                    required : true,
                    minlength : 5,
                    maxlength : 20
                }
            },

            // Messages for form validation
            messages : {
                email : {
                    required : 'Введите адрес эл.почты',
                    email : 'Введите валидный адрес эл.почты'
                },
                password : {
                    required : 'Введите пароль',
                    minlength : 'Введите больше 5-и символов',
                    maxlength : 'Введите меньше 20-и символов'
                }
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element);
            },

            submitHandler: function(form) {
                $.post("/auth/login", {filds : $("[name=authorization_form]").serialize() },
                    function (data) {
                        if (data.status == "error") {
                            $(".login_form_error").html(data.errors_messages);
                        } else {
                            $(".login_form_error").html("<span style='color:green'>" + data.ok_messages + "</span>");
                            setTimeout("location.href = location.href", 3000);
                        }
                    },"json");
            }

        });
    }
};

$(window).ready(function(){
    Authorization.init();
});
