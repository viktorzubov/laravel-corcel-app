@props(['headings', 'variant' => 'mobile'])

@if ($variant === 'mobile')
    {{-- Mobile / tablet collapsible TOC --}}
    <details class="group mb-8 rounded-xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900 overflow-hidden xl:hidden" open>
        <summary class="flex items-center justify-between px-5 py-3.5 cursor-pointer select-none text-sm font-semibold text-gray-700 dark:text-gray-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors list-none">
            <span class="flex items-center gap-2">
                <svg class="size-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
                Contents
            </span>
            <svg class="size-4 shrink-0 transition-transform group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
        </summary>
        <nav class="px-5 pb-4">
            <ul class="space-y-1.5">
                @foreach ($headings as $heading)
                    <li>
                        <a href="#{{ $heading['id'] }}"
                           data-toc-id="{{ $heading['id'] }}"
                           class="toc-link block text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors py-0.5">
                            {{ $heading['text'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </details>
@else
    {{-- Desktop sticky sidebar TOC --}}
    {{-- Safelist for JS-toggled classes: text-indigo-600 dark:text-indigo-400 font-medium border-indigo-500 border-transparent text-gray-500 dark:text-gray-400 --}}
    <aside class="hidden xl:block sticky top-24 self-start">
        <p class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-4">Contents</p>
        <nav>
            <ul class="space-y-1">
                @foreach ($headings as $heading)
                    <li>
                        <a href="#{{ $heading['id'] }}"
                           data-toc-id="{{ $heading['id'] }}"
                           class="toc-link block text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors py-1 pl-3 border-l-2 border-transparent leading-snug">
                            {{ $heading['text'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </aside>
@endif
