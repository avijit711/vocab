<?php

namespace App\Http\Controllers\Word;

use App\Http\Controllers\Controller;
use App\Models\Word;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportController extends Controller
{
    public function index()
    {
        return view('import');
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:txt|max:2048',
        ]);

        $content = file_get_contents($request->file('file')->getRealPath());
        $lines = explode("\n", $content);
        $userId = Auth::id();
        $imported = 0;
        $updated = 0;

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
                $updated++;
            } else {
                Word::create([
                    'user_id' => $userId,
                    'english_word' => $english,
                    'bangla_meaning' => $bangla,
                    'status' => 'unseen',
                ]);
                $imported++;
            }
        }

        return redirect()->route('words.import')
            ->with('imported', $imported)
            ->with('updated', $updated);
    }
}
