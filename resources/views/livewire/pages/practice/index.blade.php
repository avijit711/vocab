<?php

use App\Models\Word;
use App\Models\StudyLog;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    public ?Word $currentWord = null;
    public bool $flipped = false;
    public bool $done = false;
    public int $total = 0;
    public int $reviewed = 0;

    public function mount(): void
    {
        $this->loadNext();
    }

    public function loadNext(): void
    {
        $this->currentWord = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->inRandomOrder()
            ->first();

        $this->total = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->count();

        $this->flipped = false;

        if (!$this->currentWord) {
            $this->done = true;
        }
    }

    public function flip(): void
    {
        $this->flipped = true;
    }

    public function rate(string $rating): void
    {
        if (!$this->currentWord) return;

        if ($rating === 'easy') {
            $this->currentWord->update(['status' => 'mastered']);
        }

        StudyLog::logStudy(Auth::id());
        $this->reviewed++;

        $this->loadNext();
    }

    public function toggleFavorite(): void
    {
        if (!$this->currentWord) return;
        $this->currentWord->update(['is_favorite' => !$this->currentWord->is_favorite]);
    }

    public function toggleReadLater(): void
    {
        if (!$this->currentWord) return;
        $this->currentWord->update(['read_later' => !$this->currentWord->read_later]);
    }
}; ?>

<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Practice Arena') }}
        </h2>

        @if ($done)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 border border-gray-200 dark:border-gray-700 text-center">
                <p class="text-2xl mb-2">🎉</p>
                <p class="text-lg font-semibold text-gray-800 dark:text-gray-200">All caught up!</p>
                <p class="text-gray-500 dark:text-gray-400 mt-1">No words waiting for practice.</p>
                <div class="mt-4 flex justify-center gap-3">
                    <a href="{{ route('words.read') }}" wire:navigate
                       class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                        Read More Words
                    </a>
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="px-4 py-2 bg-gray-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 transition">
                        Dashboard
                    </a>
                </div>
            </div>
        @elseif ($currentWord)
            <div class="text-center text-sm text-gray-500 dark:text-gray-400 mb-2">
                {{ $reviewed }} reviewed · {{ $total }} remaining
            </div>

            <div class="perspective-1000">
                <div wire:click="flip"
                     class="relative w-full cursor-pointer select-none"
                     style="min-height: 280px;">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 flex flex-col items-center justify-center transition-all duration-300"
                         style="min-height: 280px;">
                        @if (!$flipped)
                            <p class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4 text-center">
                                {{ $currentWord->english_word }}
                            </p>
                            <p class="text-sm text-gray-400 dark:text-gray-500">Tap to reveal meaning</p>
                        @else
                            <p class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mb-4 text-center">
                                {{ $currentWord->english_word }}
                            </p>
                            <p class="text-2xl text-gray-700 dark:text-gray-300 text-center">
                                {{ $currentWord->bangla_meaning }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex justify-center gap-3">
                <button wire:click="toggleReadLater"
                        class="px-4 py-3 rounded-xl text-lg {{ $currentWord->read_later ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 hover:text-purple-500' }} transition">
                    🕐
                </button>
                <button wire:click="toggleFavorite"
                        class="px-4 py-3 rounded-xl text-lg {{ $currentWord->is_favorite ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 hover:text-amber-500' }} transition">
                    ⭐
                </button>
            </div>

            @if ($flipped)
                <div class="flex justify-center gap-4">
                    <button wire:click="rate('hard')"
                            class="flex-1 max-w-48 px-6 py-3 bg-red-500 hover:bg-red-600 text-white font-semibold rounded-xl transition">
                        🔴 Hard — practice again
                    </button>
                    <button wire:click="rate('easy')"
                            class="flex-1 max-w-48 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white font-semibold rounded-xl transition">
                        🟢 Easy — I know this
                    </button>
                </div>
            @else
                <div class="text-center text-sm text-gray-400 dark:text-gray-500">
                    Tap the card to see the meaning
                </div>
            @endif
        @endif
    </div>
</div>
