<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr"
      data-theme="theme-default"
      data-assets-path="{{ asset('sneat/') }}"
      data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>
    <title>{{ __('menu.auth.register') }} | {{ config('app.name') }}</title>
    <meta name="description" content="Halaman Daftar"/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('sneat/img/favicon/favicon.ico')}}"/>

    <!-- Fonts dan CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="{{asset('sneat/vendor/fonts/boxicons.css')}}"/>
    <link rel="stylesheet" href="{{asset('sneat/vendor/css/core.css')}}"/>
    <link rel="stylesheet" href="{{asset('sneat/vendor/css/theme-default.css')}}"/>
    <link rel="stylesheet" href="{{asset('sneat/css/demo.css')}}"/>
    <link rel="stylesheet" href="{{asset('sneat/vendor/css/pages/page-auth.css')}}"/>
</head>

<body>
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Card -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center">
                        <a href="{{ route('home') }}" class="app-brand-link gap-2">
                            <img src="{{ asset('logo-black.png') }}" alt="{{ config('app.name') }}" width="75px">
                        </a>
                    </div>

                    <h4 class="mb-2 text-center">{{ __('menu.auth.register') }}</h4>
                    <p class="mb-4 text-center">Silakan daftar untuk akses sistem pengelolaan surat</p>

                    <!-- Form -->
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

                        <div class="mb-3">
                            <x-input-form name="name" type="text" :label="__('Nama Lengkap')" />
                        </div>

                        <div class="mb-3">
                            <x-input-form name="email" type="email" :label="__('Email')" />
                        </div>
                        
                        <div class="mb-3">
                            <x-input-form
                                name="nip"
                                type="text"
                                :label="'NIP'"
                            />
                        </div>

                        <div class="mb-3">
                            <x-input-form name="password" type="password" :label="__('model.user.password')" />
                        </div>

                        <div class="mb-3">
                            <x-input-form name="password_confirmation" type="password" :label="__('model.user.confirm_password')" />
                        </div>

                        <button type="submit" class="btn btn-primary d-grid w-100">
                            {{ __('menu.auth.register') }}
                        </button>
                    </form>

                    <p class="text-center mt-3">
                        Sudah punya akun?
                        <a href="{{ route('login') }}">
                            {{ __('menu.auth.login') }}
                        </a>
                    </p>
                </div>
            </div>
            <!-- /Card -->
        </div>
    </div>
</div>
</body>
</html>