"use strict";

var Registration =
{
    init: function ()
    {
        Registration.registration();
    },

    registration: function()
    {
        jQuery("[name=registration_form]").validate({
            // Rules for form validation
            rules : {
                name : {
                    required : true
                },
                email : {
                    required : true,
                    email : true
                },
                password : {
                    required : true,
                    minlength : 5
                },
                re_password : {
                    required : true
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
                    minlength : 'Введите больше 5-и символов'
                },
                re_password : {
                    required : 'Введите пароль еще раз'
                }
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element);
            },

            submitHandler: function(form) {
                if ($("[name=registration_form] [name=password]").val() == $("[name=registration_form] [name=re_password]").val()) {
                    $.post("/auth/registration", {filds : $("[name=registration_form]").serialize() },
                        function (data) {
                            if (data.status == "error") {
                                $(".registration_form_error").html(data.errors_messages);
                            } else {
                                $(".registration_form_error").html("<span style='color:green'>" + data.ok_messages + "</span>");
                                setTimeout("location.href = location.href", 3000);
                            }
                        },"json");
                } else {
                    $("[name=registration_form] .registration_form_error").html("Ошибка! Пароли не совпадают");
                }
            }

        });
    }
};

$(window).ready(function(){
    Registration.init();
});
