<x-guest-layout>
    <!-- Add Boxicons CDN if not already included -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf
        <div class="text-center mb-3">
            <p class="mt-2" style="color: #374151;">Masukkan Email & Password yang Sudah Terdaftar!</p>
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <div style="position: relative; margin-top: 0.25rem;">
                <x-text-input id="password" class="block w-full" type="password" name="password" required
                    autocomplete="current-password" style="padding-right: 2.5rem;" />
                <button type="button" onclick="togglePassword('password')"
                    style="position: absolute; top: 0; right: 0; height: 100%; width: 2.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer; background: transparent; border: none;">
                    <i id="password-icon" class="bx bx-hide" style="color: #9CA3AF; font-size: 1.125rem;"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <!-- Forgot Password Link -->
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                    {{ __('Lupa Password?') }}
                </a>
            @endif

            <!-- Login Button -->
            <x-primary-button class="ml-3">
                {{ __('Login') }}
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

        // Add hover effect
        document.addEventListener('DOMContentLoaded', function() {
            const button = document.querySelector('button[onclick="togglePassword(\'password\')"]');
            if (button) {
                button.addEventListener('mouseenter', function() {
                    const icon = this.querySelector('i');
                    icon.style.color = '#6B7280';
                });
                button.addEventListener('mouseleave', function() {
                    const icon = this.querySelector('i');
                    const input = document.getElementById('password');
                    icon.style.color = input.type === 'password' ? '#9CA3AF' : '#6B7280';
                });
            }
        });
    </script>
</x-guest-layout>
