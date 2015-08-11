
<form name="registration_form">
    <div class="registration_form_error"></div>
    <table>
        <tr>
            <td><label>Имя</label></td>
            <td><input type="text" name="name"></td>
        </tr>
        <tr>
            <td><label>Email</label></td>
            <td><input type="text" name="email"></td>
        </tr>
        <tr>
            <td><label>Пароль</label></td>
            <td><input type="password" name="password"></td>
        </tr>
        <tr>
            <td><label>Пароль еще раз</label></td>
            <td><input type="password" name="re_password"></td>
        </tr>
        <tr class="reg_submit">
            <td></td>
            <td>
                <button type="submit" class="btn orange">Зарегистрироваться</button>
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

<script src="/packages/vis/registration/js/registration.js"></script>
