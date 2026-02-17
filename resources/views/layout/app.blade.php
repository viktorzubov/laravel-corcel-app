<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Anti-FOUC: apply saved/preferred theme before first paint --}}
    <script>
        (function () {
            const saved = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (saved === 'dark' || (!saved && prefersDark)) {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>

    {{-- Primary --}}
    <title>@yield('title', 'Blog')</title>
    <meta name="description" content="@yield('description', config('app.name'))">
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="{{ route('sitemap') }}">

    {{-- OpenGraph --}}
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'Blog')">
    <meta property="og:description" content="@yield('description', config('app.name'))">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    @hasSection('og_image')
        <meta property="og:image" content="@yield('og_image')">
    @endif

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="@yield('title', 'Blog')">
    <meta name="twitter:description" content="@yield('description', config('app.name'))">
    @hasSection('og_image')
        <meta name="twitter:image" content="@yield('og_image')">
        <meta name="twitter:card" content="summary_large_image">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    <x-feed-links />
</head>
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 min-h-screen flex flex-col">
    @stack('body_start')

    <header class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 sticky top-0 z-10">
        <div class="max-w-8xl mx-auto px-18 py-4 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-xl font-bold tracking-tight text-gray-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors shrink-0">
                Blog
            </a>
            <form action="{{ route('search') }}" method="GET" role="search" class="flex-1 max-w-sm">
                <div class="relative">
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search posts…"
                           class="w-full rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 px-4 py-1.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 dark:text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z"/>
                        </svg>
                    </button>
                </div>
            </form>
            <nav class="flex items-center gap-4 text-sm font-medium text-gray-600 dark:text-gray-300 shrink-0">
                <a href="{{ route('posts.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Posts</a>
                <a href="/feed" title="RSS feed" class="p-1.5 rounded-lg text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-orange-500 dark:hover:text-orange-400 transition-colors" aria-label="RSS feed">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor"><path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19.01 7.38 20 6.18 20C4.98 20 4 19.01 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93V10.1z"/></svg>
                </a>
                <button id="theme-toggle"
                        class="p-1.5 rounded-lg text-gray-400 dark:text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                        aria-label="Toggle dark mode">
                    {{-- Moon — shown in light mode --}}
                    <svg class="h-4 w-4 block dark:hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    {{-- Sun — shown in dark mode --}}
                    <svg class="h-4 w-4 hidden dark:block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m8.66-9h-1M4.34 12h-1m15.07-6.07-.71.71M6.34 17.66l-.71.71M17.66 17.66l.71.71M6.34 6.34l-.71-.71M12 5a7 7 0 1 0 0 14A7 7 0 0 0 12 5z"/>
                    </svg>
                </button>
            </nav>
        </div>
    </header>

    <main class="flex-1 max-w-8xl mx-auto w-full px-18 pb-10 pt-10">
        @yield('content')
    </main>

    <footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 text-sm text-gray-500 dark:text-gray-400">
        <div class="max-w-8xl mx-auto px-18 py-6 text-center">
            &copy; {{ date('Y') }} Blog. Powered by WordPress &amp; Laravel.
        </div>
    </footer>

    <script>
        document.getElementById('theme-toggle').addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
    </script>

    <x-cookie-banner />

</body>
</html>
