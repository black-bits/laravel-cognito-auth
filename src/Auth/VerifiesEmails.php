<?php

namespace BlackBits\LaravelCognitoAuth\Auth;

use Illuminate\Http\Request;
use BlackBits\LaravelCognitoAuth\CognitoClient;
use Illuminate\Foundation\Auth\VerifiesEmails as BaseVerifiesEmails;

trait VerifiesEmails
{
    use BaseVerifiesEmails;

    /**
     * Show the email verification notice.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show(Request $request)
    {
        return view('black-bits/laravel-cognito-auth::verify');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        $request->validate(['email' => 'required|email', 'confirmation_code' => 'required|numeric']);

        $response = app()->make(CognitoClient::class)->confirmUserSignUp($request->email, $request->confirmation_code);

        if ($response == 'validation.invalid_user') {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans('black-bits/laravel-cognito-auth::validation.invalid_user')]);
        }

        if ($response == 'validation.invalid_token') {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['confirmation_code' => trans('black-bits/laravel-cognito-auth::validation.invalid_token')]);
        }

        if ($response == 'validation.exceeded') {
            return redirect()->back()
                ->withInput($request->only('email'))
                ->withErrors(['confirmation_code' => trans('black-bits/laravel-cognito-auth::validation.exceeded')]);
        }

        if ($response == 'validation.confirmed') {
            return redirect($this->redirectPath())->with('verified', true);
        }

        return redirect($this->redirectPath())->with('verified', true);
    }


    /**
     * Resend the email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $response = app()->make(CognitoClient::class)->resendToken($request->email);

        if ($response == 'validation.invalid_user') {
            return response()->json(['error' => trans('black-bits/laravel-cognito-auth::validation.invalid_user')], 400);
        }

        if ($response == 'validation.exceeded') {
            return response()->json(['error' => trans('black-bits/laravel-cognito-auth::validation.exceeded')], 400);
        }

        if ($response == 'validation.confirmed') {
            return response()->json(['error' => trans('black-bits/laravel-cognito-auth::validation.confirmed')], 400);
        }

        return response()->json(['success' => 'true']);
    }
}
