<form name="authorization_form">
    <div class="login_form_error"></div>
    <table>
        <tr>
            <td><label>Email</label></td>
            <td><input type="text" name="email"></td>
        </tr>
        <tr>
            <td><label>Пароль</label></td>
            <td><input type="password" name="password"></td>
        </tr>
        <tr class="enter_submit">
            <td></td>
            <td>
                <div class="left">
                    <button type="submit" class="btn orange">Войти</button>
                </div>
                <div class="right">
                    <p><a href="javascript:;" onclick=" Popup.hide('#authorization_form'); Popup.show('#forgot_pass_form');">Напоминание пароля</a></p>
                    <p><a href="javascript:;" onclick=" Popup.hide('#authorization_form'); Popup.show('#registration_form'); ">Регистрация</a></p>
                </div>
            </td>
         </tr>
      </table>
      <div class="share soc_link">
          <table>
              <tr>
                  <td>
                      <p class="share_text">Вход через соцсети:</p>
                  </td>
                  <td>
                     <a href="{{route("auth_fb")}}" class="fa fa-facebook "></a>
                  </td>
                  <td>
                     <a href="{{route("auth_vk")}}" class="fa fa-vk"></a>
                  </td>
                  <td>
                     <a href="{{route("auth_google")}}" class="fa fa-google-plus"></a>
                  </td>
              </tr>
          </table>
      </div>
 </form>

 <script src="/packages/vis/registration/js/authorization.js"></script>