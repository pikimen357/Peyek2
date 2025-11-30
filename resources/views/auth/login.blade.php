{{--<x-guest-layout>--}}
{{--    <!-- Session Status -->--}}
{{--    <x-auth-session-status class="mb-4" :status="session('status')" />--}}

{{--    <form method="POST" action="{{ route('login') }}">--}}
{{--        @csrf--}}

{{--        <!-- Email Address -->--}}
{{--        <div>--}}
{{--            <x-input-label for="email" :value="__('Email')" />--}}
{{--            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />--}}
{{--            <x-input-error :messages="$errors->get('email')" class="mt-2" />--}}
{{--        </div>--}}

{{--        <!-- Password -->--}}
{{--        <div class="mt-4">--}}
{{--            <x-input-label for="password" :value="__('Password')" />--}}

{{--            <x-text-input id="password" class="block mt-1 w-full"--}}
{{--                            type="password"--}}
{{--                            name="password"--}}
{{--                            required autocomplete="current-password" />--}}

{{--            <x-input-error :messages="$errors->get('password')" class="mt-2" />--}}
{{--        </div>--}}

{{--        <!-- Remember Me -->--}}
{{--        <div class="block mt-4">--}}
{{--            <label for="remember_me" class="inline-flex items-center">--}}
{{--                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">--}}
{{--                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>--}}
{{--            </label>--}}
{{--        </div>--}}

{{--        <div class="flex items-center justify-end mt-4">--}}
{{--            @if (Route::has('password.request'))--}}
{{--                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('password.request') }}">--}}
{{--                    {{ __('Forgot your password?') }}--}}
{{--                </a>--}}
{{--            @endif--}}

{{--            <x-primary-button class="ms-3">--}}
{{--                {{ __('Log in') }}--}}
{{--            </x-primary-button>--}}
{{--        </div>--}}
{{--    </form>--}}
{{--</x-guest-layout>--}}


@extends('customer.layout.master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('assets/login/style.css') }}">
@endsection

@section('content')
     <div id="loginContainer" class="container shadow">
        <div class="p-4 d-flex flex-column align-items-center">

            <h4 class="mb-4 text-xl fw-bold">Login Akun</h4>

            <form action="{{ route('login') }}" method="POST" class="w-100">
                @csrf
                <div class="mb-3">
                    <label for="nama" class="form-label fw-bold ">Nama</label>
                    <input type="text" name="nama" id="nama" required class="form-control" >
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label fw-bold ">Password</label>
                    <input type="password" name="password" id="password" required class="form-control">
                </div>
                <div class="text-start mb-4 mt-4">
                    <p style="font-size: 13px;" class="">
                        Belum punya akun? <a href="{{ route('register') }}" class="text-decoration-underline">Buat akun</a>
                    </p>
                </div>
                <div class="d-grid gap-2 mb-3">
                    <input id="submit" type="submit" value="Login" name="submit" class="btn w-100">
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
@endsection
