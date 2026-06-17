<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $validated['points'] = 50;
        $validated['current_level'] = 'Maba';
        $validated['role'] = 'User'; 

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Join the Fun! 🚀</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Buat akun LOGNITY barumu sekarang.</p>
    </div>

    <form wire:submit="register" class="space-y-4">
        <!-- Name -->
        <div>
            <x-input-label for="username" :value="__('Username')" class="ml-1 mb-1 text-gray-600 dark:text-gray-300 font-semibold" />
            <x-text-input wire:model="username" id="username" 
                class="block w-full px-4 py-3 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800/50 focus:ring-fun-purple focus:border-fun-purple transition-all shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                type="text" name="username" required autofocus autocomplete="username" placeholder="SuperUser123" />
            <x-input-error :messages="$errors->get('username')" class="mt-2 ml-1" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="ml-1 mb-1 text-gray-600 dark:text-gray-300 font-semibold" />
            <x-text-input wire:model="email" id="email" 
                class="block w-full px-4 py-3 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800/50 focus:ring-fun-purple focus:border-fun-purple transition-all shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                type="email" name="email" required autocomplete="username" placeholder="email@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 ml-1" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" class="ml-1 mb-1 text-gray-600 dark:text-gray-300 font-semibold" />
            <x-text-input wire:model="password" id="password" 
                class="block w-full px-4 py-3 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800/50 focus:ring-fun-purple focus:border-fun-purple transition-all shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 ml-1" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="ml-1 mb-1 text-gray-600 dark:text-gray-300 font-semibold" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" 
                class="block w-full px-4 py-3 rounded-2xl border-gray-200 dark:border-gray-700 dark:bg-gray-800/50 focus:ring-fun-purple focus:border-fun-purple transition-all shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700"
                type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ulangi password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 ml-1" />
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full py-3 px-4 bg-gradient-to-r from-fun-purple to-fun-pink hover:from-purple-600 hover:to-pink-600 text-white font-bold rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-fun-purple">
                {{ __('Register') }}
            </button>
        </div>

        <div class="text-center mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none" href="{{ route('login') }}" wire:navigate>
                {{ __('Already registered?') }}
            </a>
        </div>
    </form>
</div>