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

        $seen = [];
        $words = [];
        $totalLines = 0;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $parts = preg_split('/[-–—]+/', $line, 2);

            if (count($parts) !== 2) continue;

            $english = trim($parts[0]);
            $bangla = trim($parts[1]);

            if (empty($english) || empty($bangla)) continue;

            $totalLines++;

            if (isset($seen[$english])) {
                $words[$seen[$english]]['bangla'] = $bangla;
                continue;
            }

            $seen[$english] = count($words);
            $words[] = ['english' => $english, 'bangla' => $bangla, 'line_number' => $totalLines];
        }

        if (empty($words)) {
            return redirect()->route('words.import')
                ->with('imported', 0)
                ->with('updated', 0);
        }

        $before = Word::where('user_id', $userId)->count();

        $now = now();
        $rows = array_map(fn ($w) => [
            'user_id' => $userId,
            'english_word' => $w['english'],
            'bangla_meaning' => $w['bangla'],
            'status' => 'unseen',
            'created_at' => $now,
            'updated_at' => $now,
        ], $words);

        Word::upsert($rows, ['user_id', 'english_word'], ['bangla_meaning', 'updated_at']);

        $after = Word::where('user_id', $userId)->count();
        $imported = $after - $before;
        $updated = $totalLines - $imported;

        return redirect()->route('words.import')
            ->with('imported', $imported)
            ->with('updated', $updated);
    }
}
