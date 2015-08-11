<?php namespace Vis\Registration;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;


class VKController extends Controller
{
    public function __construct()
    {
        Session::put('url_previous', URL::previous());
    }

    public function doLogin()
    {
        $destination = "http://api.vk.com/oauth/authorize?client_id=".Config::get('registration::social.vk.api_id')."&scope=friends,photos,offline&display=popup&redirect_uri=".route('auth_vk_res');
        header("Location: $destination");
    }

    //auth vk
    public function index()
    {
        if (Input::get("code")) {

            $api_id = Config::get('registration::social.vk.api_id');
            $secret_key = Config::get('registration::social.vk.secret_key');

            $params = array(
                'client_id' => $api_id,
                'client_secret' => $secret_key,
                'code' => Input::get("code"),
                'redirect_uri' => "http://" . $_SERVER['HTTP_HOST'] . "/auth_soc/vk_res"
            );

            $url = 'https://oauth.vk.com/access_token' . '?' . urldecode(http_build_query($params));
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($result, true);

            if (isset($data['access_token'])) {
                $str = "https://api.vkontakte.ru/method/getProfiles?uid=".$data['user_id']."&fields=photo_big&access_token=" . $data['access_token'];
                $resp2 = file_get_contents($str);
                $el = json_decode($resp2, true);

                $first_name = $el['response'][0]['first_name'];
                $last_name = $el['response'][0]['last_name'];

                $id_user = $el['response'][0]['uid'];
                $user = DB::table("users")->where("id_vk", $id_user)->first();


                if (!isset($user['id'])) {

                    $new_pass = str_random(6);

                    $user =  Sentry::register(array(
                        'email'    => $id_user,
                        'password' => $new_pass,
                        'id_vk' =>$id_user,
                        'activated'=>"1",
                        'first_name'=>$first_name,
                        'last_name'=>$last_name
                    ));

                    //качаем аватарку юзера
                    if ($el['response'][0]['photo_big'] && Config::get('registration::social.vk.foto')) {
                        $id_one = substr($user->id, 0, 1);
                        $destinationPath = "/storage/users/$id_one/$user->id/";

                        $path_server = public_path().$destinationPath;
                        File::makeDirectory($path_server, $mode = 0777, true, true);

                        $foto_resource = file_get_contents($el['response'][0]['photo_big']);
                        $foto_user = time() . basename($el['response'][0]['photo_big']);
                        $f = fopen($_SERVER['DOCUMENT_ROOT'] . $destinationPath . $foto_user, 'w');
                        fwrite($f, $foto_resource);
                        fclose($f);
                        $user->photo = $destinationPath.$foto_user;
                        $user->save();
                    }

                    $user_auth = Sentry::findUserById($user->id);
                    Sentry::login($user_auth, Config::get('registration::social.vk.remember'));

                } else {
                    $user_auth = Sentry::findUserById($user['id']);
                    Sentry::login($user_auth, Config::get('registration::social.vk.remember'));
                }

                //if not empty redirect_url
                if (Config::get('registration::social.vk.redirect_url')) {
                    $redirect = Config::get('registration::social.vk.redirect_url');
                    Session::flash('id_user', $user_auth->id);
                } else {
                    $redirect = Session::get('url_previous', "/");
                    Session::forget('url_previous');
                }

                return Redirect::to($redirect);
            }
        }
    }
}