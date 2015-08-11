"use strict";

var ForgotPass =
{
    init: function ()
    {
        ForgotPass.forgotPass();
    },

    forgotPass: function()
    {
        jQuery("[name=forgot_pass_form]").validate({
            // Rules for form validation
            rules : {
                email : {
                    required : true,
                    email : true
                }
            },

            // Messages for form validation
            messages : {
                email : {
                    required : 'Введите адрес эл.почты',
                    email : 'Введите валидный адрес эл.почты'
                }
            },

            // Do not change code below
            errorPlacement : function(error, element) {
                error.insertAfter(element);
            },

            submitHandler: function(form) {
                $.post("/auth/forgot_pass", {filds : $("[name=forgot_pass_form]").serialize() },
                    function (data) {
                        if (data.status == "error") {
                            $(".forgot_pass_error").html(data.errors_messages);
                        } else {
                            $(".forgot_pass_error").html("<span style='color:green'>" + data.ok_messages + "</span>");
                            setTimeout("location.href = location.href", 3000);
                        }
                    },"json");
            }

        });
    }
};

$(window).ready(function(){
    ForgotPass.init();
});
