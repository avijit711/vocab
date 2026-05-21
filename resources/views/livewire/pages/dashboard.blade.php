<?php

use App\Models\Word;
use App\Models\StudyLog;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    public int $totalWords = 0;
    public int $masteredWords = 0;
    public int $learningWords = 0;
    public int $unseenWords = 0;
    public int $readLaterCount = 0;
    public int $favoriteCount = 0;
    public float $progressPercent = 0;
    public int $streak = 0;
    public int $todaysReview = 0;

    public function mount(): void
    {
        $userId = Auth::id();

        $this->totalWords = Word::where('user_id', $userId)->count();
        $this->masteredWords = Word::where('user_id', $userId)->mastered()->count();
        $this->learningWords = Word::where('user_id', $userId)->whereIn('status', ['learning', 'reviewing'])->count();
        $this->unseenWords = Word::where('user_id', $userId)->unseen()->count();
        $this->readLaterCount = Word::where('user_id', $userId)->readLater()->count();
        $this->favoriteCount = Word::where('user_id', $userId)->favorites()->count();
        $this->streak = StudyLog::currentStreak($userId);
        $this->todaysReview = StudyLog::where('user_id', $userId)
            ->where('date', now()->toDateString())
            ->value('words_reviewed') ?? 0;

        $this->progressPercent = $this->totalWords > 0
            ? round(($this->masteredWords / $this->totalWords) * 100, 1)
            : 0;
    }
}; ?>

<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="flex items-center gap-2 text-lg">
                <span>🔥</span>
                <span class="font-bold text-orange-500">{{ $streak }} day streak</span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">Total Vocab</p>
                <p class="text-3xl font-bold text-gray-900 dark:text-gray-100">{{ $totalWords }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">Mastered 🏆</p>
                <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">{{ $masteredWords }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">Learning</p>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $learningWords }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-500 dark:text-gray-400">Read Later 🕐</p>
                <p class="text-3xl font-bold text-purple-600 dark:text-purple-400">{{ $readLaterCount }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200">Mastery Progress</h3>
                <span class="text-sm text-gray-500">{{ $progressPercent }}% complete</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                <div class="bg-gradient-to-r from-emerald-400 to-emerald-600 h-4 rounded-full transition-all duration-500"
                     style="width: {{ $progressPercent }}%"></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-gray-500 dark:text-gray-400">
                <span>{{ $unseenWords }} unseen</span>
                <span>{{ $todaysReview }} reviewed today</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <a href="{{ route('words.import') }}" wire:navigate
                   class="flex flex-col items-center gap-2 p-4 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition">
                    <span class="text-2xl">📥</span>
                    <span class="text-sm font-medium text-indigo-700 dark:text-indigo-300">Import</span>
                </a>
                <a href="{{ route('words.read') }}" wire:navigate
                   class="flex flex-col items-center gap-2 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                    <span class="text-2xl">📖</span>
                    <span class="text-sm font-medium text-blue-700 dark:text-blue-300">Read</span>
                </a>
                <a href="{{ route('practice.index') }}" wire:navigate
                   class="flex flex-col items-center gap-2 p-4 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition">
                    <span class="text-2xl">🧠</span>
                    <span class="text-sm font-medium text-emerald-700 dark:text-emerald-300">Practice</span>
                </a>
                <a href="{{ route('words.favorites') }}" wire:navigate
                   class="flex flex-col items-center gap-2 p-4 bg-amber-50 dark:bg-amber-900/30 rounded-xl hover:bg-amber-100 dark:hover:bg-amber-900/50 transition">
                    <span class="text-2xl">⭐</span>
                    <span class="text-sm font-medium text-amber-700 dark:text-amber-300">Favorites</span>
                </a>
            </div>
        </div>
    </div>
</div>
