<x-app-layout>
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

                <form action="{{ route('words.import.store') }}" method="POST" enctype="multipart/form-data"
                      x-data="{ submitting: false }"
                      @submit="submitting = true">
                    @csrf
                    <div class="flex items-center gap-4">
                        <input type="file" name="file" accept=".txt" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-300 hover:file:bg-indigo-100 dark:hover:file:bg-indigo-900/50">
                    </div>

                    @error('file')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="mt-6">
                        <button type="submit" :disabled="submitting"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition disabled:opacity-50">
                            <span x-show="!submitting">Import Words</span>
                            <span x-show="submitting">Importing...</span>
                        </button>
                    </div>
                </form>
            </div>

            @if (session('imported') !== null)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-lg text-gray-800 dark:text-gray-200 mb-3">Import Results</h3>
                    <div class="space-y-2">
                        @if (session('imported') > 0)
                            <p class="text-emerald-600 dark:text-emerald-400">✅ {{ session('imported') }} new words imported</p>
                        @endif
                        @if (session('updated') > 0)
                            <p class="text-amber-600 dark:text-amber-400">🔄 {{ session('updated') }} existing words updated</p>
                        @endif
                    </div>
                    <div class="mt-4 flex gap-3">
                        <a href="{{ route('words.import') }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">Import another file</a>
                        <a href="{{ route('dashboard') }}"
                           class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">View Dashboard</a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
