<?php

namespace App\Services;

use Illuminate\Support\Str;

class SearchHighlighter
{
    public function __construct(private readonly string $query) {}

    /**
     * Wrap every occurrence of the query in a <mark> tag.
     * Input is HTML-escaped before matching.
     */
    public function highlight(string $text): string
    {
        if (! $this->query) {
            return e($text);
        }

        return preg_replace(
            '/('.preg_quote($this->query, '/').')/iu',
            '<mark class="bg-yellow-100 dark:bg-yellow-900/50 text-inherit rounded px-0.5">$1</mark>',
            e($text)
        );
    }

    /**
     * Extract a short plain-text snippet around the first query match.
     */
    public function snippet(string $text): string
    {
        $plain = strip_tags($text);
        $pos = stripos($plain, $this->query);

        if ($pos === false) {
            return Str::limit($plain, 160);
        }

        $start = max(0, $pos - 60);

        return ($start > 0 ? '…' : '').substr($plain, $start, 220).'…';
    }
}
