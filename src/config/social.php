

<?php

return array(
    //create account https://vk.com/editapp?act=create
    'vk' => array(
        "status"=>true,
        "api_id"=>"5009923",
        "secret_key" => "PdTvngSv7pXK5VHHupZj",
        "redirect_url"=>"",
        "remember"=>false,
        "foto" => true
    ),
    //create account https://developers.facebook.com/quickstarts/?platform=web
    'fb' => array(
        "status"=>true,
        "api_id"=>"885147974889661",
        "secret_key" => "e7a172cdfaede22cf0b26b40816e20c8",
        "redirect_url"=>"",
        "remember"=>false
    ),
    //create account https://console.developers.google.com/project
    'google'  => array(
        "status"=>true,
        "api_id"=>"99063614414-o8jgp2ml9tddr9qllhkf1mc4bla0fjoc.apps.googleusercontent.com",
        "secret_key" => "MhGfFbmjAIUPJGSWioQaps4X",
        "redirect_oauth2callback" => Request::root()."/auth_soc/oauth2callback",
        "redirect_url"=>"",
        "remember"=>false
    ),
);

