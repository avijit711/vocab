<?php

use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Word;
use Illuminate\Support\Facades\Auth;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    public $file;
    public int $imported = 0;
    public int $updated = 0;
    public bool $done = false;

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:txt|max:2048',
        ];
    }

    public function import(): void
    {
        $this->validate();

        $content = $this->file->get();
        $lines = explode("\n", $content);
        $userId = Auth::id();

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = preg_split('/[-–—]+/', $line, 2);

            if (count($parts) !== 2) continue;

            $english = trim($parts[0]);
            $bangla = trim($parts[1]);

            if (empty($english) || empty($bangla)) continue;

            $word = Word::where('user_id', $userId)
                ->where('english_word', $english)
                ->first();

            if ($word) {
                $word->update(['bangla_meaning' => $bangla]);
                $this->updated++;
            } else {
                Word::create([
                    'user_id' => $userId,
                    'english_word' => $english,
                    'bangla_meaning' => $bangla,
                    'status' => 'unseen',
                ]);
                $this->imported++;
            }
        }

        $this->done = true;
        $this->file = null;
    }
}; ?>

<div class="py-12">
    <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Import Vocabulary') }}
        </h2>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Upload a <code>.txt</code> file with one word per line in the format:<br>
                <code class="text-indigo-600 dark:text-indigo-400">English Word - Bangla Meaning</code>
            </p>

            <form wire:submit="import">
                <div class="flex items-center gap-4">
                    <input type="file" wire:model="file" accept=".txt"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50">
                </div>

                @error('file')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <div class="mt-6">
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition disabled:opacity-50">
                        <span wire:loading.remove>Import Words</span>
                        <span wire:loading>Importing...</span>
                    </button>
                </div>
            </form>
        </div>

        @if ($done)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-3">Import Results</h3>
                <div class="space-y-2">
                    @if ($imported > 0)
                        <p class="text-emerald-600 dark:text-emerald-400">✅ {{ $imported }} new words imported</p>
                    @endif
                    @if ($updated > 0)
                        <p class="text-amber-600 dark:text-amber-400">🔄 {{ $updated }} existing words updated</p>
                    @endif
                </div>
                <div class="mt-4 flex gap-3">
                    <a href="{{ route('words.import') }}" wire:navigate
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Import another file</a>
                    <a href="{{ route('dashboard') }}" wire:navigate
                       class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View Dashboard</a>
                </div>
            </div>
        @endif
    </div>
</div>
