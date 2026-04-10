<?php

namespace App\Http\Controllers;

use App\Services\SearchHighlighter;
use App\Services\SearchService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(private readonly SearchService $searchService) {}

    public function __invoke(Request $request): View
    {
        $query = $request->string('q')->trim()->value();
        $posts = $this->searchService->search($query);
        $highlighter = new SearchHighlighter($query);

        return view('search', compact('posts', 'query', 'highlighter'));
    }
}
