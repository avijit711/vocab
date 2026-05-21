<?php

use App\Models\Word;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public function removeFavorite(int $wordId): void
    {
        $word = Word::where('user_id', Auth::id())->findOrFail($wordId);
        $word->update(['is_favorite' => false]);
    }

    public function with(): array
    {
        return [
            'words' => Word::where('user_id', Auth::id())
                ->favorites()
                ->orderBy('updated_at', 'desc')
                ->paginate(10),
        ];
    }
}; ?>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('⭐ Favorite Words') }}
        </h2>

        @if ($words->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 border border-gray-200 dark:border-gray-700 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-lg">No favorite words yet.</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm mt-1">
                    Star words while <a href="{{ route('words.read') }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">reading</a>
                    or <a href="{{ route('practice.index') }}" wire:navigate class="text-indigo-600 dark:text-indigo-400 hover:underline">practicing</a>.
                </p>
            </div>
        @endif

        <div class="space-y-3">
            @foreach ($words as $word)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $word->english_word }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ $word->bangla_meaning }}</p>
                        <span class="inline-block mt-1 text-xs px-2 py-0.5 rounded-full
                            @if($word->status === 'mastered') bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300
                            @elseif(in_array($word->status, ['learning', 'reviewing'])) bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300
                            @else bg-gray-100 dark:bg-gray-700 text-gray-500 @endif">
                            {{ ucfirst($word->status) }}
                        </span>
                    </div>
                    <button wire:click="removeFavorite({{ $word->id }})"
                            class="px-3 py-1.5 text-sm bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/50 transition">
                        Remove
                    </button>
                </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $words->links() }}
        </div>
    </div>
</div>
