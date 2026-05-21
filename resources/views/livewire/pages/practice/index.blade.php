<?php

use App\Models\Word;
use App\Models\StudyLog;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    public array $jsQueue = [];
    public int $reviewed = 0;
    public int $total = 0;
    public bool $done = false;

    public function mount(): void
    {
        $this->refreshData();
    }

    public function refreshData(): void
    {
        $words = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->inRandomOrder()
            ->limit(30)
            ->get();

        if ($words->isEmpty()) {
            $this->done = true;
            return;
        }

        $this->jsQueue = $words->map(fn(Word $w) => [
            'id' => $w->id,
            'english_word' => $w->english_word,
            'bangla_meaning' => $w->bangla_meaning,
            'read_later' => $w->read_later,
            'is_favorite' => $w->is_favorite,
        ])->values()->toArray();

        $this->total = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->count();

        $this->done = false;
    }

    public function rate(int $wordId, string $rating): void
    {
        $word = Word::find($wordId);
        if (!$word) return;

        if ($rating === 'easy') {
            $word->update(['status' => 'mastered']);
            $this->reviewed++;
        }

        StudyLog::logStudy(Auth::id());
    }

    public function refill(): void
    {
        $words = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->inRandomOrder()
            ->limit(30)
            ->get();

        $this->total = Word::where('user_id', Auth::id())
            ->whereIn('status', ['learning', 'reviewing'])
            ->count();

        if ($words->isEmpty()) {
            $this->done = true;
            $this->jsQueue = [];
            $this->dispatch('queue-refilled', words: [], total: $this->total, done: true);
            return;
        }

        $this->jsQueue = $words->map(fn(Word $w) => [
            'id' => $w->id,
            'english_word' => $w->english_word,
            'bangla_meaning' => $w->bangla_meaning,
            'read_later' => $w->read_later,
            'is_favorite' => $w->is_favorite,
        ])->values()->toArray();

        $this->done = false;

        $this->dispatch('queue-refilled', words: $this->jsQueue, total: $this->total, done: false);
    }

    public function toggleFavorite(int $wordId): void
    {
        $word = Word::find($wordId);
        if ($word) $word->update(['is_favorite' => !$word->is_favorite]);
    }

    public function toggleReadLater(int $wordId): void
    {
        $word = Word::find($wordId);
        if ($word) $word->update(['read_later' => !$word->read_later]);
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
                    <a href="{{ route('words.read') }}"
                       class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-semibold text-sm text-white transition">
                        Read Words
                    </a>
                    <a href="{{ route('dashboard') }}"
                       class="px-6 py-2.5 bg-gray-600 hover:bg-gray-500 rounded-xl font-semibold text-sm text-white transition">
                        Dashboard
                    </a>
                </div>
            </div>

        @elseif (!empty($jsQueue))
            <div wire:key="practice-arena"
                 x-data="{
                    queue: [],
                    currentWord: null,
                    reviewed: 0,
                    total: 0,
                    flipped: false,
                    loading: false,

                    init() {
                        this.queue = @js($jsQueue);
                        this.reviewed = {{ $reviewed }};
                        this.total = {{ $total }};

                        $wire.on('queue-refilled', (data) => {
                            if (data.done) {
                                this.currentWord = null;
                                this.queue = [];
                                this.loading = false;
                                return;
                            }
                            this.queue = [...this.queue, ...data.words];
                            this.total = data.total;
                            if (!this.currentWord && this.queue.length) {
                                this.nextWord();
                            }
                            this.loading = false;
                        });

                        this.nextWord();
                        this.refillQueue();
                    },

                    nextWord() {
                        if (this.queue.length === 0) {
                            if (this.currentWord) {
                                this.currentWord = null;
                            }
                            return;
                        }
                        this.currentWord = this.queue.shift();
                        this.flipped = false;
                        if (this.queue.length < 10) {
                            this.refillQueue();
                        }
                    },

                    rate(rating) {
                        if (!this.currentWord) return;
                        const word = this.currentWord;
                        if (rating === 'easy') this.reviewed++;
                        this.nextWord();
                        $wire.rate(word.id, rating);
                    },

                    refillQueue() {
                        if (this.loading) return;
                        this.loading = true;
                        $wire.refill();
                    },

                    toggleFavorite() {
                        if (!this.currentWord) return;
                        this.currentWord.is_favorite = !this.currentWord.is_favorite;
                        $wire.toggleFavorite(this.currentWord.id);
                    },

                    toggleReadLater() {
                        if (!this.currentWord) return;
                        this.currentWord.read_later = !this.currentWord.read_later;
                        $wire.toggleReadLater(this.currentWord.id);
                    },
                 }" wire:ignore>

                {{-- Progress bar --}}
                <div class="flex items-center gap-3">
                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2.5 overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 ease-out"
                             :style="`width: ${total > 0 ? (reviewed / Math.max(reviewed + total, 1)) * 100 : 0}%; background: linear-gradient(90deg, #6366f1, #a855f7);`"></div>
                    </div>
                    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium tabular-nums whitespace-nowrap">
                        <span class="hidden sm:inline">Mastered </span><span x-text="reviewed"></span>/<span x-text="reviewed + total"></span>
                    </span>
                </div>

                {{-- Done state (client-side) --}}
                <template x-if="!currentWord && !loading">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-8 border border-gray-200 dark:border-gray-700 text-center mt-4">
                        <p class="text-5xl mb-4">🎉</p>
                        <p class="text-xl font-bold text-gray-800 dark:text-gray-200">All caught up!</p>
                        <p class="text-gray-500 dark:text-gray-400 mt-2">No words waiting for practice right now.</p>
                        <div class="mt-6 flex justify-center gap-3">
                            <a href="{{ route('words.read') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-500 rounded-xl font-semibold text-sm text-white transition">Read Words</a>
                            <a href="{{ route('dashboard') }}" class="px-6 py-2.5 bg-gray-600 hover:bg-gray-500 rounded-xl font-semibold text-sm text-white transition">Dashboard</a>
                        </div>
                    </div>
                </template>

                {{-- Loading skeleton --}}
                <template x-if="!currentWord && loading">
                    <div class="w-full bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 sm:p-12 flex flex-col items-center justify-center min-h-[260px] sm:min-h-[300px] mt-4 animate-pulse">
                        <div class="h-8 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-6"></div>
                        <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-1/2"></div>
                    </div>
                </template>

                {{-- Card --}}
                <template x-if="currentWord">
                    <div>
                        <div @click="flipped = true"
                             class="w-full cursor-pointer select-none bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8 sm:p-12 flex flex-col items-center justify-center transition-shadow hover:shadow-xl min-h-[260px] sm:min-h-[300px] mt-4">

                            {{-- Front --}}
                            <div x-show="!flipped" class="text-center w-full">
                                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 leading-relaxed"
                                   x-text="currentWord.english_word"></p>
                                <p class="text-sm text-gray-400 dark:text-gray-500 mt-6">Tap to reveal meaning</p>
                            </div>

                            {{-- Back --}}
                            <div x-show="flipped" class="text-center w-full">
                                <p class="text-lg sm:text-xl font-medium text-gray-500 dark:text-gray-400 mb-2"
                                   x-text="currentWord.english_word"></p>
                                <p class="text-2xl sm:text-3xl font-bold text-indigo-600 dark:text-indigo-400 leading-relaxed"
                                   x-text="currentWord.bangla_meaning"></p>
                            </div>
                        </div>

                        {{-- Toolbar --}}
                        <div class="flex justify-center gap-2 mt-4">
                            <button @click="toggleReadLater()"
                                    class="p-2.5 rounded-xl text-lg transition"
                                    :class="currentWord.read_later ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 hover:text-purple-500'">
                                🕐
                            </button>
                            <button @click="toggleFavorite()"
                                    class="p-2.5 rounded-xl text-lg transition"
                                    :class="currentWord.is_favorite ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 hover:text-amber-500'">
                                ⭐
                            </button>
                        </div>

                        {{-- Rating buttons --}}
                        <template x-if="flipped">
                            <div class="flex justify-center gap-3 mt-6">
                                <button @click="rate('hard')"
                                        class="flex-1 max-w-44 px-5 py-3.5 bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-semibold rounded-xl transition-all active:scale-[0.97] text-sm sm:text-base">
                                    🔴 Hard
                                </button>
                                <button @click="rate('easy')"
                                        class="flex-1 max-w-44 px-5 py-3.5 bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-semibold rounded-xl transition-all active:scale-[0.97] text-sm sm:text-base">
                                    🟢 Easy
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
                </template>

            </div>
        @endif
    </div>
</div>
