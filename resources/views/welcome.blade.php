<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="theme-color" content="#4f46e5">
        <link rel="manifest" href="/manifest.json">
        <link rel="icon" href="/icons/icon-192.svg">

        <title>VocabMaster Pro — Smart Vocabulary Learning</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .gradient-text {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }
            .hero-gradient {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            .card-hover:hover {
                transform: translateY(-4px);
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            }
        </style>
    </head>
    <body class="antialiased font-sans bg-gray-50 dark:bg-gray-950">
        <div class="relative min-h-screen">
            <nav class="relative z-10 flex items-center justify-between px-6 py-5 max-w-7xl mx-auto">
                <a href="/" class="flex items-center gap-2.5">
                    <x-application-logo class="w-8 h-8" />
                    <span class="text-xl font-bold text-gray-900 dark:text-white">VocabMaster</span>
                </a>
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" wire:navigate
                               class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" wire:navigate
                               class="text-sm font-medium text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white transition">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" wire:navigate
                                   class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-sm font-semibold shadow-md hover:shadow-lg transition-all">
                                    Get Started
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </nav>

            <main class="relative z-10 max-w-7xl mx-auto px-6">
                <section class="pt-20 pb-16 md:pt-32 md:pb-24 text-center">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs font-medium mb-6">
                        🚀 Smart Learning Platform
                    </div>
                    <h1 class="text-4xl md:text-6xl lg:text-7xl font-extrabold text-gray-900 dark:text-white leading-tight mb-6">
                        Master Vocabulary<br>
                        <span class="gradient-text">with Spaced Repetition</span>
                    </h1>
                    <p class="max-w-2xl mx-auto text-lg md:text-xl text-gray-500 dark:text-gray-400 mb-10">
                        Turn static word lists into an interactive learning system. Import, read, practice, and retain — all in one beautiful app.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" wire:navigate
                               class="px-8 py-3.5 rounded-xl hero-gradient text-white font-semibold text-base shadow-lg hover:shadow-xl transition-all">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" wire:navigate
                               class="px-8 py-3.5 rounded-xl hero-gradient text-white font-semibold text-base shadow-lg hover:shadow-xl transition-all">
                                Start Learning Free
                            </a>
                            <a href="{{ route('login') }}" wire:navigate
                               class="px-8 py-3.5 rounded-xl bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold text-base shadow-md hover:shadow-lg border border-gray-200 dark:border-gray-700 transition-all">
                                Sign In
                            </a>
                        @endauth
                    </div>
                </section>

                <section class="py-16 md:py-24">
                    <div class="text-center mb-14">
                        <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">How it works</h2>
                        <p class="text-gray-500 dark:text-gray-400 max-w-lg mx-auto">Three simple steps to vocabulary mastery</p>
                    </div>
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-sm border border-gray-200 dark:border-gray-800 card-hover transition-all duration-300">
                            <div class="w-14 h-14 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-2xl mb-5">📥</div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Import Words</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Upload any .txt file with English-Bangla word pairs. Our smart parser deduplicates automatically.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-sm border border-gray-200 dark:border-gray-800 card-hover transition-all duration-300">
                            <div class="w-14 h-14 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-2xl mb-5">📖</div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Read & Learn</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Browse words in bite-sized pages. Mark them as read to unlock flashcard practice.</p>
                        </div>
                        <div class="bg-white dark:bg-gray-900 rounded-2xl p-8 shadow-sm border border-gray-200 dark:border-gray-800 card-hover transition-all duration-300">
                            <div class="w-14 h-14 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-2xl mb-5">🧠</div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Practice & Retain</h3>
                            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">Flip flashcards, test your recall, and mark words as mastered. Track your streak daily.</p>
                        </div>
                    </div>
                </section>

                <section class="py-16 md:py-24">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-sm border border-gray-200 dark:border-gray-800 overflow-hidden">
                        <div class="grid md:grid-cols-2">
                            <div class="p-10 md:p-14 flex flex-col justify-center">
                                <span class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-3">Features</span>
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-6">Everything you need to</h2>
                                <ul class="space-y-4">
                                    <li class="flex items-start gap-3">
                                        <span class="text-emerald-500 mt-0.5">✓</span>
                                        <span class="text-gray-600 dark:text-gray-300">Spaced Repetition System for long-term retention</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="text-emerald-500 mt-0.5">✓</span>
                                        <span class="text-gray-600 dark:text-gray-300">Smart deduplication — existing words get updated meanings</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="text-emerald-500 mt-0.5">✓</span>
                                        <span class="text-gray-600 dark:text-gray-300">Daily streak tracking to keep you motivated</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="text-emerald-500 mt-0.5">✓</span>
                                        <span class="text-gray-600 dark:text-gray-300">Favorites & Read Later for personalized learning</span>
                                    </li>
                                    <li class="flex items-start gap-3">
                                        <span class="text-emerald-500 mt-0.5">✓</span>
                                        <span class="text-gray-600 dark:text-gray-300">Progressive Web App — install on your phone</span>
                                    </li>
                                </ul>
                            </div>
                            <div class="hero-gradient p-10 md:p-14 flex flex-col items-center justify-center text-center">
                                <x-application-logo class="w-20 h-20 mb-6 opacity-90" />
                                <h3 class="text-2xl font-bold text-white mb-3">Ready to start?</h3>
                                <p class="text-white/70 mb-8 max-w-sm">Join now and turn your vocabulary list into mastered words.</p>
                                @auth
                                    <a href="{{ url('/dashboard') }}" wire:navigate
                                       class="px-8 py-3 rounded-xl bg-white text-indigo-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                                        Go to Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('register') }}" wire:navigate
                                       class="px-8 py-3 rounded-xl bg-white text-indigo-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                                        Create Free Account
                                    </a>
                                @endauth
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="relative z-10 border-t border-gray-200 dark:border-gray-800 py-8 mt-8">
                <div class="max-w-7xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2.5">
                        <x-application-logo class="w-6 h-6" />
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">VocabMaster Pro</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500">&copy; {{ date('Y') }} VocabMaster Pro. Built with Laravel & Livewire.</p>
                </div>
            </footer>
        </div>
    </body>
</html>
