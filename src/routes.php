<?php

Route::group(['middleware' => 'web', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/password-reset', 'Auth\ResetPasswordController@showResetForm')
        ->name('cognito.password-reset');
});
