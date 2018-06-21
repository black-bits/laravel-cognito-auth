<?php

Route::get('/password-reset', 'Auth\ResetPasswordController@showResetForm')
    ->name('cognito.password-reset');