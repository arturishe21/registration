<?php

/* social  */
Route::get('auth_soc/vk', array(
        'as' => 'auth_vk',
        'uses' => 'Vis\Registration\VKController@doLogin')
);

Route::get('auth_soc/vk_res', array(
        'as' => 'auth_vk_res',
        'uses' => 'Vis\Registration\VKController@index')
);

Route::get('auth_soc/fb', array(
        'as' => 'auth_fb',
        'uses' => 'Vis\Registration\FBController@doLogin')
);

Route::get('auth_soc/face_res', array(
        'as' => 'auth_fb_res',
        'uses' => 'Vis\Registration\FBController@index')
);

Route::get('auth_soc/google', array(
        'as' => 'auth_google',
        'uses' => 'Vis\Registration\GoogleController@google')
);
Route::get('auth_soc/oauth2callback', array(
        'as' => 'auth_google_oauth2callback',
        'uses' => 'Vis\Registration\GoogleController@oauth2callback')
);

Route::get('activating_user', array(
        'as' => 'activating_user',
        'uses' => 'Vis\Registration\RegistrationController@doActivatingUser')
);

//logout
Route::get('logout', array(
        'as' => 'logout',
        'uses' => 'Vis\Registration\RegistrationController@doLogout')
);

if (Request::ajax()) {

    Route::post('auth/login', array(
            'uses' => 'Vis\Registration\RegistrationController@doLogin')
    );

    Route::post('auth/registration', array(
            'uses' => 'Vis\Registration\RegistrationController@doRegistration')
    );

    Route::post('auth/forgot_pass', array(
            'uses' => 'Vis\Registration\RegistrationController@doForgotPass')
    );

}
