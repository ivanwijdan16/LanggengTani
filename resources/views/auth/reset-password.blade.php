<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="user-id" content="{{ auth()->id() }}">

    <title>{{ config('app.name', 'LanggengTani') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon/Logo Tab.png') }}" />
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/img/favicon/Logo Tab.png') }}" />

    <!-- Boxicons CDN -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-cover bg-center bg-no-repeat relative"
        style="background-image: url('{{ asset('images/bg.jpg') }}');">
        <div class="absolute inset-0 bg-black opacity-60"></div>
        <div class="relative z-10">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg relative z-10">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <div class="text-center mb-3">
                    <p class="mt-2" style="color: #374151;">Reset Password</p>
                </div>
                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <!-- Email Address -->
                <div>
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                        :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <div style="position: relative; margin-top: 0.25rem;">
                        <x-text-input id="password" class="block w-full" type="password" name="password" required
                            autocomplete="new-password" style="padding-right: 2.5rem;" />
                        <button type="button" onclick="togglePassword('password')"
                            style="position: absolute; top: 0; right: 0; height: 100%; width: 2.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; background: transparent; border: none;">
                            <i id="password-icon" class="bx bx-hide" style="color: #9CA3AF; font-size: 1.125rem;"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <div style="position: relative; margin-top: 0.25rem;">
                        <x-text-input id="password_confirmation" class="block w-full" type="password"
                            name="password_confirmation" required autocomplete="new-password"
                            style="padding-right: 2.5rem;" />
                        <button type="button" onclick="togglePassword('password_confirmation')"
                            style="position: absolute; top: 0; right: 0; height: 100%; width: 2.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; background: transparent; border: none;">
                            <i id="password_confirmation-icon" class="bx bx-hide"
                                style="color: #9CA3AF; font-size: 1.125rem;"></i>
                        </button>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button>
                        {{ __('Submit') }}
                    </x-primary-button>
                </div>
            </form>

            <script>
                function togglePassword(inputId) {
                    const input = document.getElementById(inputId);
                    const icon = document.getElementById(inputId + '-icon');

                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('bx-hide');
                        icon.classList.add('bx-show');
                        icon.style.color = '#6B7280'; // Darker when visible
                    } else {
                        input.type = 'password';
                        icon.classList.remove('bx-show');
                        icon.classList.add('bx-hide');
                        icon.style.color = '#9CA3AF'; // Lighter when hidden
                    }
                }

                // Add hover effects for both password fields
                document.addEventListener('DOMContentLoaded', function() {
                    // Password field
                    const passwordButton = document.querySelector('button[onclick="togglePassword(\'password\')"]');
                    if (passwordButton) {
                        passwordButton.addEventListener('mouseenter', function() {
                            const icon = this.querySelector('i');
                            icon.style.color = '#6B7280';
                        });
                        passwordButton.addEventListener('mouseleave', function() {
                            const icon = this.querySelector('i');
                            const input = document.getElementById('password');
                            icon.style.color = input.type === 'password' ? '#9CA3AF' : '#6B7280';
                        });
                    }

                    // Password confirmation field
                    const confirmButton = document.querySelector(
                        'button[onclick="togglePassword(\'password_confirmation\')"]');
                    if (confirmButton) {
                        confirmButton.addEventListener('mouseenter', function() {
                            const icon = this.querySelector('i');
                            icon.style.color = '#6B7280';
                        });
                        confirmButton.addEventListener('mouseleave', function() {
                            const icon = this.querySelector('i');
                            const input = document.getElementById('password_confirmation');
                            icon.style.color = input.type === 'password' ? '#9CA3AF' : '#6B7280';
                        });
                    }
                });
            </script>
        </div>
    </div>
</body>

</html>
