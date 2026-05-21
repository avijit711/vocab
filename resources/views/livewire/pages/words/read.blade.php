<?php

use App\Models\Word;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public string $filter = 'unseen';

    public function toggleStatus(int $wordId): void
    {
        $word = Word::where('user_id', Auth::id())->findOrFail($wordId);
        $word->update([
            'status' => $word->status === 'unseen' ? 'learning' : 'unseen',
        ]);
    }

    public function toggleFavorite(int $wordId): void
    {
        $word = Word::where('user_id', Auth::id())->findOrFail($wordId);
        $word->update(['is_favorite' => !$word->is_favorite]);
    }

    public function toggleReadLater(int $wordId): void
    {
        $word = Word::where('user_id', Auth::id())->findOrFail($wordId);
        $word->update(['read_later' => !$word->read_later]);
    }

    public function markPageAsRead(): void
    {
        $ids = Word::where('user_id', Auth::id())
            ->where('status', 'unseen')
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->pluck('id');

        Word::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->update(['status' => 'learning']);
    }

    public function unmarkPage(): void
    {
        $ids = Word::where('user_id', Auth::id())
            ->where('status', 'learning')
            ->orderBy('created_at', 'asc')
            ->paginate(10)
            ->pluck('id');

        Word::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->update(['status' => 'unseen']);
    }

    public function markAllAsRead(): void
    {
        Word::where('user_id', Auth::id())
            ->where('status', 'unseen')
            ->update(['status' => 'learning']);
    }

    public function with(): array
    {
        return [
            'words' => Word::where('user_id', Auth::id())
                ->where('status', $this->filter)
                ->orderBy('created_at', 'asc')
                ->paginate(10),
            'unseenCount' => Word::where('user_id', Auth::id())->unseen()->count(),
            'learningCount' => Word::where('user_id', Auth::id())->learning()->count(),
        ];
    }
}; ?>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Read Mode') }}
            </h2>
            <div class="flex flex-wrap gap-2">
                <button wire:click="markPageAsRead"
                        class="px-4 py-2 bg-blue-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 transition">
                    Mark Page as Read
                </button>
                <button wire:click="unmarkPage"
                        class="px-4 py-2 bg-amber-500 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-amber-400 transition">
                    Unmark Page
                </button>
                <button wire:click="markAllAsRead"
                        class="px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 transition">
                    Mark All as Read
                </button>
                <a href="{{ route('words.import') }}" wire:navigate
                   class="px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 transition">
                    + Import
                </a>
            </div>
        </div>

        <div class="flex gap-2">
            <button wire:click="$set('filter', 'unseen')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition
                        {{ $filter === 'unseen' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Unseen ({{ $unseenCount }})
            </button>
            <button wire:click="$set('filter', 'learning')"
                    class="px-4 py-2 rounded-lg text-sm font-medium transition
                        {{ $filter === 'learning' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                Read ({{ $learningCount }})
            </button>
        </div>

        @if ($words->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 border border-gray-200 dark:border-gray-700 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-lg">
                    @if ($filter === 'unseen')
                        No unseen words left!
                    @else
                        No read words yet.
                    @endif
                </p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
                    @if ($filter === 'unseen')
                        <a href="{{ route('words.import') }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">Import more</a>
                        or <a href="{{ route('practice.index') }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">start practicing</a>
                    @else
                        <button wire:click="$set('filter', 'unseen')" class="text-indigo-600 dark:text-indigo-400 hover:underline">View unseen words</button>
                        to mark them as read.
                    @endif
                </p>
            </div>
        @endif

        <div class="space-y-3">
            @foreach ($words as $word)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $word->english_word }}</h3>
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($word->status === 'unseen') bg-gray-100 dark:bg-gray-700 text-gray-500
                                @else bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 @endif">
                                {{ $word->status }}
                            </span>
                        </div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ $word->bangla_meaning }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="toggleReadLater({{ $word->id }})"
                                class="p-2 rounded-lg text-lg {{ $word->read_later ? 'text-purple-500' : 'text-gray-400 hover:text-purple-500' }} transition">
                            🕐
                        </button>
                        <button wire:click="toggleFavorite({{ $word->id }})"
                                class="p-2 rounded-lg text-lg {{ $word->is_favorite ? 'text-amber-500' : 'text-gray-400 hover:text-amber-500' }} transition">
                            ⭐
                        </button>
                        <button wire:click="toggleStatus({{ $word->id }})"
                                class="px-4 py-2 rounded-lg font-semibold text-xs uppercase tracking-widest transition
                                    {{ $word->status === 'unseen'
                                        ? 'bg-blue-600 text-white hover:bg-blue-500'
                                        : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/50' }}">
                            {{ $word->status === 'unseen' ? 'Mark Read' : 'Unmark' }}
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $words->links() }}
        </div>
    </div>
</div>
