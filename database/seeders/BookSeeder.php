<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class BookSeeder extends Seeder
{
    private const PALETTES = [
        ['#1e3a5f', '#4a90d9'],
        ['#5f1e3a', '#d94a7a'],
        ['#1e5f3a', '#4ad98a'],
        ['#5f4a1e', '#d9b04a'],
        ['#3a1e5f', '#8a4ad9'],
        ['#1e4f5f', '#4ac7d9'],
    ];

    public function run(): void
    {
        $dir = public_path('_uploads');
        File::ensureDirectoryExists($dir);

        Book::factory(100)->create()->each(function (Book $book) use ($dir) {
            $filename = 'seed-book-' . $book->id . '.svg';

            File::put($dir . '/' . $filename, $this->cover($book));

            $book->update(['photo' => $filename]);
        });
    }

    /**
     * Build an SVG cover so seeded books never depend on uploaded files.
     */
    private function cover(Book $book): string
    {
        [$dark, $light] = self::PALETTES[$book->id % count(self::PALETTES)];

        $lines = array_slice(explode("\n", wordwrap($book->title, 18, "\n", true)), 0, 4);

        $text = '';
        foreach ($lines as $i => $line) {
            $text .= '<text x="150" y="' . (150 + $i * 34) . '" text-anchor="middle" '
                . 'font-family="Georgia, serif" font-size="26" fill="#ffffff">'
                . htmlspecialchars($line, ENT_XML1) . '</text>';
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 400" width="300" height="400">'
            . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0" stop-color="' . $dark . '"/><stop offset="1" stop-color="' . $light . '"/>'
            . '</linearGradient></defs>'
            . '<rect width="300" height="400" fill="url(#g)"/>'
            . '<rect x="14" y="14" width="272" height="372" fill="none" stroke="#ffffff" stroke-opacity=".5"/>'
            . $text
            . '</svg>';
    }
}
