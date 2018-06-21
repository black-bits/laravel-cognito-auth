# Laravel Package to easily manage authentication with AWS Cognito

[![Latest Version on Packagist](https://img.shields.io/packagist/v/black-bits/laravel-cognito-auth.svg?style=flat-square)](https://packagist.org/packages/black-bits/laravel-firewall)
[![Total Downloads](https://img.shields.io/packagist/dt/black-bits/laravel-cognito-auth.svg?style=flat-square)](https://packagist.org/packages/black-bits/laravel-firewall)

This package provides a simple way to use AWS Cognito authentication in Laravel. 
The idea of this package is based on the origin package from Pod-Point which you can find here: https://github.com/Pod-Point/laravel-cognito-auth.
We decided to use it as a basis for our own package as we wanted to customize it in some ways to fit our needs. 

Currently we have the following features implemented in our package:

- Registration and Confirmation E-Mail
- Login
- Remember Me Cookie
- Single Sign On
- Forgot Password

## Installation

You can install the package via composer.

```bash
composer require black-bits/laravel-cognito-auth
```

Next you can publish the config and the view.

```bash
php artisan vendor:publish --provider="BlackBits\LaravelCognitoAuth\CognitoAuthServiceProvider"
```

Last but not least you want to change the auth driver. 
To do so got to your `config\auth.php` file and change it to look the following:

```
'guards' => [
    'web' => [
        'driver' => 'cognito', // This line is important 
        'provider' => 'standard',
    ],
    'api' => [
        'driver' => 'token',
        'provider' => 'standard',
    ],
],
```



## Usage

Add the following fields to your `.env` file:

```
AWS_KEY
AWS_SECRET
AWS_REGION
AWS_COGNITO_CLIENT_ID
AWS_COGNITO_CLIENT_SECRET
AWS_COGNITO_USER_POOL_ID
AWS_COGNITO_SSO
```

Add BlackBits\LaravelCognitoAuth\CognitoAuthServiceProvider to your `config\app.php` if you are using Laravel version < 5.5.

Go to your amazon management console into your cognito area and create a new user pool. 

Generate an App Client. You can name it whatever you want. This will give you the App client id and the App client secret
you need for your `.env` file. 

```
IMPORTANT: Don't forget to activate the checkbox to Enable sign-in API for server-based Authentication. 
The Auth Flow is called: ADMIN_NO_SRP_AUTH
```

Our package is providing you 4 traits you can just add to your Auth Controllers to get our package running.

- BlackBits\LaravelCognitoAuth\Auth\AuthenticatesUsers
- BlackBits\LaravelCognitoAuth\Auth\RegistersUsers
- BlackBits\LaravelCognitoAuth\Auth\ResetsPasswords
- BlackBits\LaravelCognitoAuth\Auth\SendsPasswordResetEmails


In the simplest way you just go through your Auth Controllers and change namespaces from the traits which are currently implemented from laravel.

During the publishing process of our package you created a view which you will find under `Resources/views/vendor/black-bits/laravel-cognito-auth`. 

You can change structure to fit your needs. Please be aware of the @extend statement in the blade file to fit into your project structure. 
At the current state you need to have those 4 form fields defined in here. Those are `token`, `email`, `password`, `password_confirmation`. 

## Single Sign On

With our package and AWS Cognito we provide you a simple way to use Single Sign On's. To enable it just go to the `cognito.php`
file in the config directory and set sso to true. But how does it work? 

When you have SSO enabled in your config and a user tries to login into your application we will check if he exists 
in your Cognito pool. If the user exists he will be created automatically in your database and is logged in simultaneously.

Thats what we need the fields `sso_user_model` and `sso_user_fields` for. In `sso_user_model` you define the class of your user model.
In most cases this will simply be App\User. 

With `sso_user_fields` you can define the fields which should be stored in Cognito. Put attention here. If you define a field 
which you do not send with the Register Request this will throw you an InvalidUserFieldException and you won't be able to register. 

So now you have registered your user with its attributes in the cognito pool and your local database and you want to attach a second 
app which should use the same pool. Well, thats actually pretty easy. You set up your project like you are used to and install our 
laravel-cognito-auth package. On both sites set sso to true. Also be sure you entered exactly the same pool id. 
Now when a user is registered in your other app but not in your second and wants to login he gets created. And thats all you need to do. 


```
   IMPORTANT: if your user table has a password field you are not going to need this anymore. 
   What you want to do is set this field to be nullable so that users can be created without passwords. 
   Passwords are stored in Cognito now. 
   
   Also any additional registration data you have, for example firstname, lastname needs to be added in 
   config\congito.php sso_user_fields config to be pushed to Cognito. Otherwise they are only stored locally 
   and are not available if you want to use Single Sign On's. 
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

### Security

If you discover any security related issues, please email [hello@blackbits.io](mailto:hello@blackbits.io) instead of using the issue tracker.

## Credits

- [Oliver Heck](https://github.com/oheck)
- [Andreas Przywara](https://github.com/aprzywara)
- [Adrian Raeuchle](https://github.com/araeuchle)
- [All Contributors](../../contributors)

## Support us

Black Bits, Inc. is a web and consulting agency specialized in Laravel and AWS based in Grants Pass, Oregon. You'll find an overview of what we do [on our website](https://blackbits.io).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.