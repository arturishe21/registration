<?php

namespace Vis\Registration;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use Cartalyst\Sentry\Facades\Laravel\Sentry;


class FBController extends Controller
{
    public function __construct()
    {
        Session::put('url_previous', URL::previous());
    }

    public function doLogin()
    {
        $link = route('auth_fb_res');
        header("Location: $link");
    }

    //auth FB
    public function index()
    {
        $app_id = Config::get('registration::social.fb.api_id');;
        $app_secret = Config::get('registration::social.fb.secret_key');
        $my_url = "http://" . $_SERVER['HTTP_HOST'] . "/auth_soc/face_res";
        $code = Input::get("code");
        $state = Input::get("state");

        if (empty($code)) {

            Session::put('state', md5(uniqid(rand(), TRUE)));
            $dialog_url = "http://www.facebook.com/dialog/oauth?client_id="
                . $app_id . "&redirect_uri=" . urlencode($my_url) . "&scope=public_profile,publish_actions,email&state="
                .  Session::get('state')."&fields=email,first_name,last_name,id,gender";
            header("Location: $dialog_url");
        }

        if ($state == Session::get('state')) {
            $token_url = "https://graph.facebook.com/oauth/access_token?"
                . "client_id=" . $app_id . "&redirect_uri=" . urlencode($my_url)
                . "&client_secret=" . $app_secret . "&code=" . $code."&fields=email,first_name,last_name,id,gender";

            $response = file_get_contents($token_url);
            $params = null;
            parse_str($response, $params);
            $graph_url = "https://graph.facebook.com/me?access_token=". $params['access_token']."&fields=email,first_name,last_name,id,gender";
            $user = json_decode(file_get_contents($graph_url));
            
            $first_name = $user->first_name;
            $last_name = $user->last_name;

            $fb_id = $user->id;

            if (isset($user->email)) {
                $user_email = $user->email;
            } else {
                $user_email = $fb_id;
            }

            //проверка юзера
            if ($user_email && $fb_id) {

                $user = DB::table("users")->where("id_fb", $fb_id)->first();
                if (!$user['id']){
                    $user = DB::table("users")->where("email","like" , $user_email)->first();
                }

                if (!$user['id']) {

                    $new_pass = str_random(6);

                    $user =  Sentry::register(array(
                        'email'    => $user_email,
                        'password' => $new_pass,
                        'id_fb' =>$fb_id,
                        'activated'=>"1",
                        'first_name'=>$first_name,
                        'last_name'=>$last_name
                    ));

                    $user_auth = Sentry::findUserById($user->id);
                    Sentry::login($user_auth, Config::get('registration::social.fb.remember'));

                } else {
                    $user_auth = Sentry::findUserById($user['id']);
                    Sentry::login($user_auth, Config::get('registration::social.fb.remember'));
                }
                $redirect = Session::get('url_previous', "/");
                Session::forget('url_previous');

                //if not empty redirect_url
                if (Config::get('registration::social.fb.redirect_url')) {
                    $redirect = Config::get('registration::social.fb.redirect_url');
                    Session::flash('id_user', $user_auth->id);
                }else {
                    $redirect = Session::get('url_previous', "/");
                    Session::forget('url_previous');
                }

                return Redirect::to($redirect);
            }
        }
    }
}

