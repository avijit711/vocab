<?php

use App\Models\Word;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    use WithPagination;

    public function removeReadLater(int $wordId): void
    {
        $word = Word::where('user_id', Auth::id())->findOrFail($wordId);
        $word->update(['read_later' => false]);
    }

    public function with(): array
    {
        return [
            'words' => Word::where('user_id', Auth::id())
                ->readLater()
                ->orderBy('updated_at', 'desc')
                ->paginate(10),
        ];
    }
}; ?>

<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('🕐 Read Later') }}
        </h2>

        @if ($words->isEmpty())
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 border border-gray-200 dark:border-gray-700 text-center">
                <p class="text-gray-500 dark:text-gray-400 text-lg">No words saved for later.</p>
            </div>
        @endif

        <div class="space-y-3">
            @foreach ($words as $word)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 border border-gray-200 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $word->english_word }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-0.5">{{ $word->bangla_meaning }}</p>
                    </div>
                    <button wire:click="removeReadLater({{ $word->id }})"
                            class="px-3 py-1.5 text-sm bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/50 transition">
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
