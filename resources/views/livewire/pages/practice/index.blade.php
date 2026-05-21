<?php

use App\Models\Word;
use App\Models\StudyLog;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    public array $wordQueue = [];
    public ?Word $currentWord = null;
    public bool $done = false;
    public int $total = 0;
    public int $reviewed = 0;

    public function mount(): void
    {
        $this->refillQueue();
    }

    public function refillQueue(): void
    {
        $words = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($words->isEmpty()) {
            $this->done = true;
            return;
        }

        $this->wordQueue = $words->all();
        $this->currentWord = $this->wordQueue[0];
        $this->total = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->count();
    }

    public function rate(string $rating): void
    {
        if (empty($this->wordQueue)) return;

        $word = $this->wordQueue[0];

        if ($rating === 'easy') {
            $word->update(['status' => 'mastered']);
            $this->reviewed++;
        }

        StudyLog::logStudy(Auth::id());

        array_shift($this->wordQueue);

        if (empty($this->wordQueue)) {
            $this->refillQueue();
        } else {
            $this->currentWord = $this->wordQueue[0];
        }

        if (!$this->currentWord) {
            $this->done = true;
        }
    }

    public function toggleFavorite(): void
    {
        if (empty($this->wordQueue)) return;
        $this->wordQueue[0]->update(['is_favorite' => !$this->wordQueue[0]->is_favorite]);
    }

    public function toggleReadLater(): void
    {
        if (empty($this->wordQueue)) return;
        $this->wordQueue[0]->update(['read_later' => !$this->wordQueue[0]->read_later]);
    }
}; ?>

<div class="py-8 sm:py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8 space-y-6">

        @if ($done)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 text-center">
                <p class="text-5xl mb-4">🎉</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200">All caught up!</p>
                <p class="text-gray-500 dark:text-gray-400 mt-2">No words waiting for practice right now.</p>
                <div class="mt-6 flex justify-center gap-3">
                    <a href="{{ route('words.read') }}" wire:navigate
                       class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-semibold text-sm text-white transition">
                        Read Words
                    </a>
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="px-6 py-2.5 bg-gray-600 hover:bg-gray-500 rounded-xl font-semibold text-sm text-white transition">
                        Dashboard
                    </a>
                </div>
            </div>

        @elseif ($currentWord)
            <div x-data="{ flipped: false }" wire:key="practice-{{ $currentWord->id }}">

                {{-- Progress bar --}}
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 ease-out"
                             style="width: {{ $total > 0 ? (($reviewed) / max($reviewed + $total, 1)) * 100 : 0 }}%;
                                    background: linear-gradient(90deg, #6366f1, #a855f7);"></div>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium tabular-nums whitespace-nowrap">
                        <span class="hidden sm:inline">Mastered </span>{{ $reviewed }}/{{ $reviewed + $total }}
                    </span>
                </div>

                {{-- Card --}}
                <div @click="flipped = true"
                     class="relative w-full cursor-pointer select-none bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 sm:p-12 flex flex-col items-center justify-center transition-shadow hover:shadow-xl min-h-[260px] sm:min-h-[300px] mt-4">

                    {{-- Front --}}
                    <div x-show="!flipped" class="text-center w-full">
                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 leading-relaxed">
                            {{ $currentWord->english_word }}
                        </p>
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-6">Tap to reveal meaning</p>
                    </div>

                    {{-- Back --}}
                    <div x-show="flipped" class="text-center w-full">
                        <p class="text-lg sm:text-xl font-medium text-gray-500 dark:text-gray-400 mb-2">
                            {{ $currentWord->english_word }}
                        </p>
                        <p class="text-2xl sm:text-3xl font-bold text-indigo-600 dark:text-indigo-400 leading-relaxed">
                            {{ $currentWord->bangla_meaning }}
                        </p>
                    </div>
                </div>

                {{-- Toolbar --}}
                <div class="flex justify-center gap-2 mt-4">
                    <button wire:click="toggleReadLater"
                            class="p-2.5 rounded-xl text-lg transition
                                @if ($currentWord->read_later)
                                    bg-purple-100 dark:bg-purple-900/30 text-purple-600
                                @else
                                    bg-gray-100 dark:bg-gray-700 text-gray-400 hover:text-purple-500
                                @endif">
                        🕐
                    </button>
                    <button wire:click="toggleFavorite"
                            class="p-2.5 rounded-xl text-lg transition
                                @if ($currentWord->is_favorite)
                                    bg-amber-100 dark:bg-amber-900/30 text-amber-600
                                @else
                                    bg-gray-100 dark:bg-gray-700 text-gray-400 hover:text-amber-500
                                @endif">
                        ⭐
                    </button>
                </div>

                {{-- Rating buttons --}}
                <template x-if="flipped">
                    <div class="flex justify-center gap-3 mt-6">
                        <button wire:click="rate('hard')"
                                wire:loading.attr="disabled"
                                class="flex-1 max-w-44 px-5 py-3.5 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-xl transition-all active:scale-[0.97] disabled:opacity-50 text-sm sm:text-base">
                            <span wire:loading.remove wire:target="rate('hard')">🔴 Hard</span>
                            <span wire:loading wire:target="rate('hard')" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Hard
                            </span>
                        </button>
                        <button wire:click="rate('easy')"
                                wire:loading.attr="disabled"
                                class="flex-1 max-w-44 px-5 py-3.5 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-semibold rounded-xl transition-all active:scale-[0.97] disabled:opacity-50 text-sm sm:text-base">
                            <span wire:loading.remove wire:target="rate('easy')">🟢 Easy</span>
                            <span wire:loading wire:target="rate('easy')" class="inline-flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                Easy
                            </span>
                        </button>
                    </div>
                </template>

                {{-- Tap hint --}}
                <template x-if="!flipped">
                    <p class="text-center text-sm text-gray-400 dark:text-gray-500 mt-6">
                        Tap the card to see the Bangla meaning
                    </p>
                </template>

            </div>
        @endif
    </div>
</div>
