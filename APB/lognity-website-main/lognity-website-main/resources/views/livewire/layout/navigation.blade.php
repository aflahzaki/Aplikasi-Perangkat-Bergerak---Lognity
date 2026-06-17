<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ 
        open: false, 
        scrolled: false,
        darkMode: false,
        init() {
            // Cek local storage saat komponen dimuat
            this.darkMode = localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches);
            
            // Paksa sync class di HTML tag agar sesuai icon
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        },
        toggleTheme() {
            this.darkMode = !this.darkMode;
            localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
            
            if (this.darkMode) document.documentElement.classList.add('dark');
            else document.documentElement.classList.remove('dark');
        }
     }" 
     @scroll.window="scrolled = (window.pageYOffset > 20)"
     :class="{'bg-white/80 dark:bg-dark-card/80 backdrop-blur-lg shadow-sm': scrolled, 'bg-transparent': !scrolled}"
     class="sticky top-0 z-50 transition-all duration-300 w-full border-b border-gray-200/50 dark:border-gray-700/50">
    
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20"> 
            <div class="flex">
                <!-- Logo LOGNITY -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" wire:navigate class="group flex items-center gap-2">
                        <div class="w-10 h-10 bg-gradient-to-tr from-lognity-500 to-fun-purple rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition transform duration-200">
                            <span class="text-white font-bold text-xl">L</span>
                        </div>
                        <span class="font-bold text-2xl tracking-tight text-gray-800 dark:text-white group-hover:text-lognity-600 dark:group-hover:text-lognity-400 transition">
                            LOGNITY
                        </span>
                    </a>
                </div>

                <!-- Navigation Links (Desktop) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-12 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate 
                        class="text-base font-medium hover:text-lognity-600 transition-colors {{ request()->routeIs('dashboard') ? 'text-lognity-600 border-b-2 border-lognity-600' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('forum.index')" :active="request()->routeIs('forum.*')" wire:navigate 
                        class="text-base font-medium hover:text-lognity-600 transition-colors {{ request()->routeIs('forum.*') ? 'text-lognity-600 border-b-2 border-lognity-600' : 'text-gray-500 dark:text-gray-400' }}">
                        {{ __('Forum') }}
                    </x-nav-link>

                    <x-nav-link :href="route('library.index')" :active="request()->routeIs('library.*')" wire:navigate>
                        {{ __('E-Library') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                
                <!-- Day/Night Toggle Button -->
                <button @click="toggleTheme()" class="p-2 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 transition focus:outline-none transform active:scale-95">
                    <!-- Sun Icon -->
                    <svg x-show="!darkMode" class="w-5 h-5 text-fun-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <!-- Moon Icon -->
                    <svg x-show="darkMode" x-cloak class="w-5 h-5 text-fun-purple" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>

                <!-- Settings Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-semibold rounded-full text-gray-500 dark:text-gray-300 bg-gray-50 dark:bg-gray-800 hover:bg-white hover:shadow-md transition ease-in-out duration-200 focus:outline-none">
                            <div x-data="{{ json_encode(['name' => auth()->user()->username]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                            <div class="ms-2">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="p-2">
                            @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Superadmin']))
                                <x-dropdown-link :href="route('admin.users')" wire:navigate class="rounded-md hover:bg-indigo-50 dark:hover:bg-gray-700 hover:text-indigo-600">
                                    {{ __('Admin Panel') }}
                                </x-dropdown-link>
                            @endif
                            <x-dropdown-link :href="route('profile.edit')" wire:navigate class="rounded-md hover:bg-lognity-50 dark:hover:bg-gray-700 hover:text-lognity-600">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <button wire:click="logout" class="w-full text-start">
                                <x-dropdown-link class="rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </button>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger & Mobile Toggle -->
            <div class="-me-2 flex items-center sm:hidden gap-3">
                 <button @click="toggleTheme()" class="p-2 rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg x-show="!darkMode" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg x-show="darkMode" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </button>
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white dark:bg-gray-800 shadow-lg border-t border-gray-100 dark:border-gray-700">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('forum.index')" :active="request()->routeIs('forum.*')" wire:navigate>
                {{ __('Forum') }}
            </x-responsive-nav-link>
            <x-nav-link :href="route('library.index')" :active="request()->routeIs('library.*')" wire:navigate>
                {{ __('E-Library') }}
            </x-nav-link>
        </div>
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200" x-data="{{ json_encode(['name' => auth()->user()->username]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                @if(auth()->check() && in_array(auth()->user()->role, ['Admin', 'Superadmin']))
                    <x-responsive-nav-link :href="route('admin.users')" wire:navigate>
                        {{ __('Admin Panel') }}
                    </x-responsive-nav-link>
                @endif
                <x-responsive-nav-link :href="route('profile.edit')" wire:navigate>
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>{{ __('Log Out') }}</x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>