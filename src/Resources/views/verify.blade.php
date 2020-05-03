@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif

                        @error('alreadyConfirmed')
                            <div class="alert alert-danger text-center" role="alert">
                                <strong>{{ $message }}</strong>
                            </div>
                        @enderror

                        <div class="text-center alert alert-warning" id="globalAlert">
                            {{ __('Before proceeding, please check your email for a verification code.') }}
                        </div>
                        <br/>
                        <form method="POST" action="{{ route('cognito.verification.verify') }}" id="verifyForm">
                            @csrf

                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                                           value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="confirmation_code" class="col-md-4 col-form-label text-md-right">{{ __('Verification Code') }}</label>

                                <div class="col-md-6">
                                    <input id="confirmation_code" type="number" class="form-control @error('confirmation_code') is-invalid @enderror"
                                           name="confirmation_code" value="{{ old('confirmation_code') }}" required autocomplete="confirmation_code">

                                    @error('confirmation_code')
                                    <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Confirm') }}
                                </button>
                            </div>
                        </form>
                        <br/>
                        <br/>
                        <div class="text-center">
                            {{ __('If you did not receive the email') }},
                            <form class="d-inline" method="POST" action="{{ route('cognito.verification-resend') }}" onsubmit="event.preventDefault(); resendEmail();">
                                <button type="submit" class="btn btn-link p-0 m-0 align-baseline" id="resendButton">{{ __('click here to request another') }}</button>.
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function resendEmail() {
                var resendButton = document.getElementById('resendButton');
                resendButton.disabled = true;

                var form = document.getElementById("verifyForm");
                Array.from(form.elements).forEach(formElement => formElement.disabled = true);

                var globalAlert = document.getElementById('globalAlert');
                var message = globalAlert.innerHTML;
                globalAlert.innerHTML = "{{ trans('black-bits/laravel-cognito-auth::validation.resending') }}";

                var xhr = new XMLHttpRequest();
                var csrf = document.getElementsByName("_token")[0].value;
                var email = document.getElementById('email').value;
                var params = 'email=' + email;

                xhr.open('POST', "{{ route('cognito.verification-resend') }}");
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);

                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // document.body.innerHTML = xhr.responseText;
                        Array.from(form.elements).forEach(formElement => formElement.disabled = false);
                        globalAlert.innerHTML = "{{ trans('black-bits/laravel-cognito-auth::validation.resend') }}";
                        resendButton.disabled = false;
                    }
                    else if (xhr.status !== 200) {
                        if(xhr.status === 400) {
                            var prased = JSON.parse(xhr.responseText)
                            alert(prased.error)
                        }
                        else if(xhr.status === 422) {
                            alert("{{ trans('black-bits/laravel-cognito-auth::validation.required') }}")
                        } else {
                            alert('Request failed.  Returned status of ' + xhr.status);
                        }

                        Array.from(form.elements).forEach(formElement => formElement.disabled = false);
                        resendButton.disabled = false;
                        globalAlert.innerHTML = message;
                    }
                };

                xhr.send(params);
            }
        </script>
    </div>
@endsection
