<!DOCTYPE html>
<html>
    <head>
        <title>Login - {{ $setting->sitename }}</title>
        <link rel="stylesheet" href="{{ asset('css/login.css') }}">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">    
    </head>

    <body class="login-page login-1">
        <div id="app" class="login-wrapper" style="background: #ffffff; color:#000000 !important">
            <div class="login-box">
                <div class="logo-main">
                    <img src="{{ asset('img/logo/rightchoice-logo.jpg') }}" class="brand-image img-circle">
                    <!--<h4 style="color: #fff">{{ $setting->sitename }}</h1>-->
                </div>
                
                @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-block text-white">
                      <button type="button" class="close" data-dismiss="alert">×</button> 
                      <strong>{{ $message }}</strong>
                    </div>
                @endif

                @if (count($errors) > 0)
                    <div class="alert alert-danger text-white">
                      <strong>Error!</strong>
                      <ul>
                        @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                @endif
                <div  class="border p-2">
                    <form  id="loginForm" method="POST" action="{{ route('login') }}" class="mb-3">
                        @csrf
                        <div class="form-group">
                            <input id="email" type="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" name="email" value="{{ old('email') }}" required autofocus placeholder="Enter Email" required="">

                            @if ($errors->has('email'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('email') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="password" type="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required placeholder="Enter Password" required="">

                            @if ($errors->has('password'))
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span>
                            @endif
                        </div>
                        <button class="btn btn-info btn-full">Login</button>           
                    </form>
                </div>

                <div class="page-copyright"style="color: #000;">
                    <!--<div class="pull-left">
                        <p>Powered by <a href="#" target="_blank">Zallasoft Solutions</a></p>
                    </div>-->
                   
                        <p> Alrights Reserved {{ $setting->sitename }} © {{ date('Y') }}</p>                 
                </div>
            </div>
        </div>
    </body>
</html>
