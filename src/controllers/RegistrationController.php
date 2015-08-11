<?php namespace Vis\Registration;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;

use Vis\MailTemplates\MailT;

class RegistrationController extends Controller
{
    //валидация при регистрации
    public  $reg_rules = array(
        'email'     => 'required|email|unique:users',
        'password'  => 'required|min:5'
    );

    //валидация при авторизации
    public  $auth_rules = array(
        'email'     => 'required',
        'password'  => 'required|min:5'
    );

    //валидация при напоминании пароля
    public  $forgot_rules = array(
        'email'     => 'required'
    );

    public $messages = array(
        'first_name.required'  => 'Поле имя должно быть заполнено',
        'password.min'      => 'Поле пароль должно быть минимум 5 знаков',
        'password.required' => 'Поле пароль должно быть заполнено',
        'email.unique'      => 'Пользователь с данным Email уже существует',
        'email.email'       => 'Не правильный формат email',
        'email.required'    => 'Поле email должно быть заполнено'
    );

    /*
     * Authorization
     */
    public function doLogin()
    {
        parse_str(Input::get('filds'), $filds);

        $validator = Validator::make($filds, $this->auth_rules);
        if ($validator->fails()) {

            return Response::json(
                array(
                    'status' => 'error',
                    "errors_messages" => implode("<br>",$validator->messages()->all())
                )
            );
        }

        try {
            $user = Sentry::authenticate(
                array(
                    'email' => $filds['email'],
                    'password' => $filds['password'],
                    'activated' => "1"
                )
            );

            return Response::json(
                array(
                    'status' => 'ok', "ok_messages" => "Вы успешно авторизованы"
                )
            );

        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {

            return Response::json(
                array(
                    'status' => 'error',
                    "errors_messages" => "Пользователь не найден"
                )
            );
        }
    } //end doLogin

    /*
     * Registration on site
     */
    public function doRegistration()
    {
        parse_str(Input::get('filds'), $filds);

        //check password
        if ($filds['password'] != $filds['re_password']) {
            return Response::json(
                array(
                    'status' => 'error',
                    "errors_messages" => "Ошибка. Пароли не совпадают"
                )
            );
        }

        $validator = Validator::make($filds, $this->reg_rules, $this->messages);
        if ($validator->fails()) {
            return Response::json(
                array(
                    'status' => 'error',
                    "errors_messages" => implode("<br>",$validator->messages()->all())
                )
            );
        }
        try {
            $user = Sentry::register(
                array(
                    'email' => $filds['email'],
                    'password' => $filds['password'],
                    'first_name' => $filds['name']
                )
            );

            $mail = new MailT(Config::get('registration::registration.template_mail'), [
                "login" => $filds['email'],
                "password" => $filds['password'],
                "activationcode" =>  $user->getActivationCode()
            ]);
            $mail->to = $filds['email'];
            $mail->send();

            return Response::json(
                array(
                    "status" => "ok",
                    "ok_messages" => "Вы успешно зарегистрированы. На почту выслана ссылка для активации",
                )
            );

        } catch (\Cartalyst\Sentry\Users\UserExistsException $e) {
            return Response::json(
                array(
                    'status' => 'error',
                    "errors_messages" => $this->messages['email.unique'],
                )
            );
        }


    } //end doRegistration

    /*
     * logout
     */
    public function doLogout()
    {
        Sentry::logout();
        return Redirect::back();
    } //end doLogout

    /*
     * forgot pass
     */
    public function doForgotPass()
    {
        parse_str(Input::get('filds'), $filds);

        $validator = Validator::make($filds, $this->forgot_rules, $this->messages);
        if ($validator->fails()) {
            return Response::json(
                array(
                    'status' => 'error',
                    "errors_messages" => implode("<br>", $validator->messages()->all())
                )
            );
        }

        try {
            $user = Sentry::findUserByLogin($filds['email']);

            // $resetCode = $user->getResetPasswordCode();

            $new_pass = str_random(5);
            $user->password = $new_pass;
            $user->save();

            $mail = new MailT("napominanie-parolja",
                [
                    "name_user" => $user->first_name,
                    "pass" => $new_pass
                ]);

            $mail->to = $filds['email'];
            $mail->send();

            return Response::json(
                array(
                    'status' => 'ok',
                    "ok_messages" => "Вам на почту был выслан новый пароль"
                )
            );
        } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
            return Response::json(
                array(
                    'status' => 'error',
                    "errors_messages" => "Пользователь не найден"
                )
            );
        }

    } //end doForgotPass

    /*
     * activation user
     */
    public function doActivatingUser()
    {
        $email = Input::get("email");
        $code = Input::get("code");
        $status = "error";

        if ($email && $code) {

            try {
                $user = Sentry::findUserByLogin($email);

                // Attempt to activate the user
                if ($user->attemptActivation($code)) {
                    $result = "Пользователь активирован";
                    $status = "success";
                    Sentry::login($user, false);
                } else {
                    $result = "Ошибка. Пользователя код активации не подходит";
                }

            } catch (\Cartalyst\Sentry\Users\UserNotFoundException $e) {
                $result = "Пользователь не найден";
            } catch (\Cartalyst\Sentry\Users\UserAlreadyActivatedException $e) {
                $result = "Пользователь уже активирован";
            }

            return View::make('registration::activatingUser', compact("result", "status"));
        } else {
            $result = "Неверные входные данные. Email или код активации неверные ";
            return View::make('registration::activatingUser', compact("result"));
        }
    } //end doActivatingUser
}