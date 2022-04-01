<?php

namespace BlackBits\LaravelCognitoAuth\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use BlackBits\LaravelCognitoAuth\CognitoClient;
use Illuminate\Foundation\Auth\ResetsPasswords as BaseResetsPasswords;

trait ResetsPasswords
{
    use BaseResetsPasswords;

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reset(Request $request)
    {
        $this->validate($request, $this->rules(), $this->validationErrorMessages());

        $client = app()->make(CognitoClient::class);

        $user = $client->getUser($request->email);

        if (isset($user['UserStatus'])
            && $user['UserStatus'] == CognitoClient::FORCE_PASSWORD_STATUS) {
            $response = $this->forceNewPassword($request);
        } else {
            $response = $client->resetPassword($request->token, $request->email, $request->password);
        }

        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    /**
     * If a user is being forced to set a new password for the first time follow that flow instead.
     *
     * @param  \Illuminate\Http\Request $request
     * @return string
     */
    private function forceNewPassword($request)
    {
        $client = app()->make(CognitoClient::class);
        $login = $client->authenticate($request->email, $request->token);

        return $client->confirmPassword($request->email, $request->password, $login->get('Session'));
    }

    /**
     * Display the password reset view for the given token.
     *
     * If no token is present, display the link request form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showResetForm(Request $request, $token = null)
    {
        return view('vendor.black-bits.laravel-cognito-auth.reset-password')->with(
            ['email' => $request->email]
        );
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }
}
