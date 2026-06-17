<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Welcome Back! 👋</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Masuk untuk melanjutkan aktivitasmu.</p>
    </div>

    <form wire:submit="login" class="space-y-5">
        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="ml-1 mb-1 text-gray-600 dark:text-gray-300 font-semibold" />
            <div class="relative">
                <x-text-input wire:model="form.email" id="email" 
                    class="block w-full px-4 py-3 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800/50 focus:ring-lognity-400 focus:border-lognity-400 transition-all shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700" 
                    type="email" name="email" required autofocus autocomplete="username" placeholder="hello@lognity.com" />
            </div>
            <x-input-error :messages="$errors->get('form.email')" class="mt-2 ml-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="ml-1 mb-1 text-gray-600 dark:text-gray-300 font-semibold" />
            <x-text-input wire:model="form.password" id="password" 
                class="block w-full px-4 py-3 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800/50 focus:ring-lognity-400 focus:border-lognity-400 transition-all shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                type="password"
                name="password"
                required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2 ml-1" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember" class="inline-flex items-center cursor-pointer group">
                <input wire:model="form.remember" id="remember" type="checkbox" class="rounded-lg border-gray-300 dark:border-gray-600 text-lognity-500 shadow-sm focus:ring-lognity-400 dark:bg-gray-800 dark:focus:ring-offset-gray-900 w-5 h-5 transition-colors">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400 group-hover:text-lognity-600 dark:group-hover:text-lognity-300 transition">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-lognity-500 to-fun-purple hover:from-lognity-600 hover:to-purple-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-lognity-500">
                {{ __('Log in') }}
            </button>
        </div>
        
        <div class="flex items-center justify-between text-sm mt-6">
            @if (Route::has('password.request'))
                <a class="text-gray-500 dark:text-gray-400 hover:text-fun-pink dark:hover:text-fun-pink transition-colors" href="{{ route('password.request') }}" wire:navigate>
                    {{ __('Forgot password?') }}
                </a>
            @endif
             <a href="{{ route('register') }}" wire:navigate class="text-lognity-600 dark:text-lognity-400 font-bold hover:underline">
                Buat Akun Baru
            </a>
        </div>
    </form>

    <!-- Banned Modal (Redesigned) -->
    @if (session()->has('banned'))
        <div x-data="{ showModal: true }" 
             x-show="showModal"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             style="display: none;" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            
            <!-- Backdrop -->
            <div class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm" @click="showModal = false"></div>
            
            <!-- Modal Content -->
            <div class="relative bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 overflow-hidden border border-red-100 dark:border-red-900">
                <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-red-500 to-orange-500"></div>
                
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900/30 mb-6 animate-pulse">
                    <svg class="h-10 w-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-center text-gray-800 dark:text-white mb-2">Akses Ditolak 🚫</h2>
                
                <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 mb-6 border border-red-100 dark:border-red-800">
                    <p class="text-sm text-center text-red-600 dark:text-red-300 font-medium whitespace-pre-line">
                        {{ session('banned') }}
                    </p>
                </div>

                <div class="flex justify-center">
                    <button @click="showModal = false" class="w-full inline-flex justify-center rounded-xl px-4 py-3 bg-gray-800 dark:bg-gray-700 text-white font-medium hover:bg-gray-900 focus:outline-none transform active:scale-95 transition-all">
                        Saya Mengerti
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>