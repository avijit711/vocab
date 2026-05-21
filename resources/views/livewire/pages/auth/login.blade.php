<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 text-center mb-1">Welcome back</h2>
    <p class="text-sm text-gray-500 dark:text-gray-400 text-center mb-6">Sign in to continue your learning journey</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-5">
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
            <input wire:model="form.email" id="email" type="email" name="email" required autofocus autocomplete="username"
                   class="block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm px-4 py-3">
            <x-input-error :messages="$errors->get('form.email')" class="mt-1" />
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
            <input wire:model="form.password" id="password" type="password" name="password" required autocomplete="current-password"
                   class="block w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-400 text-sm px-4 py-3">
            <x-input-error :messages="$errors->get('form.password')" class="mt-1" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="inline-flex items-center">
                <input wire:model="form.remember" id="remember" type="checkbox"
                       class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-400">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" wire:navigate
                   class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-medium">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit"
                class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-semibold text-sm shadow-md hover:shadow-lg transition-all duration-200">
            Sign in
        </button>

        <p class="text-center text-sm text-gray-500 dark:text-gray-400">
            Don't have an account?
            <a href="{{ route('register') }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 font-medium">Sign up</a>
        </p>
    </form>
</div>
