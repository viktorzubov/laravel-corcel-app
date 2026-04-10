<?php

namespace App\Http\Controllers;

use App\Services\SitemapService;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __construct(private readonly SitemapService $sitemapService) {}

    public function __invoke(): Response
    {
        return response($this->sitemapService->render(), 200, ['Content-Type' => 'application/xml']);
    }
}
