<?php

Route::group(['middleware' => 'web', 'namespace' => 'App\Http\Controllers'], function () {
    Route::get('/password-reset', 'Auth\ResetPasswordController@showResetForm')
        ->name('cognito.password-reset');
    Route::get('/email/verify', 'Auth\VerificationController@show')
        ->name('cognito.verification-notice');
    Route::post('/email/verify', 'Auth\VerificationController@verify')
        ->name('cognito.verification.verify');
    Route::post('/email/resend', 'Auth\VerificationController@resend')
        ->name('cognito.verification-resend');
});
