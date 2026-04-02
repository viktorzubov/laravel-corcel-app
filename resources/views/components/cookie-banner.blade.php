<div id="cookie-banner"
     style="transform: translateY(100%); transition: transform 0.5s cubic-bezier(0.16, 1, 0.3, 1);"
     class="fixed bottom-0 inset-x-0 z-50"
     role="dialog" aria-live="polite" aria-label="Cookie consent">
    <div class="bg-white dark:bg-gray-900 border-t-2 border-indigo-500 shadow-[0_-8px_30px_rgba(0,0,0,0.12)] dark:shadow-[0_-8px_30px_rgba(0,0,0,0.4)]">
        <div class="max-w-8xl mx-auto px-18 py-5 flex flex-col sm:flex-row sm:items-center gap-5">
            <div class="flex items-start gap-4 flex-1 min-w-0">
                <div class="shrink-0 mt-0.5 w-9 h-9 rounded-full bg-indigo-50 dark:bg-indigo-950 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                        <path d="M21.598 11.064a1.006 1.006 0 0 0-.854-.172A2.938 2.938 0 0 1 20 11c-1.654 0-3-1.346-3.003-2.937.005-.034.016-.136.017-.17a.998.998 0 0 0-1.254-1.006A2.963 2.963 0 0 1 15 7c-1.654 0-3-1.346-3-3 0-.217.031-.444.099-.716a1 1 0 0 0-1.067-1.236A10.083 10.083 0 0 0 2 12c0 5.514 4.486 10 10 10s10-4.486 10-10c0-.049-.003-.097-.005-.146a1.002 1.002 0 0 0-.397-.79zM12 20c-4.411 0-8-3.589-8-8a8.088 8.088 0 0 1 6.854-7.964C10.732 4.644 10.98 5.304 11 6c0 2.757 2.243 5 5 5 .192 0 .381-.025.568-.056C16.83 12.522 18.757 13 20 13c0 .056-.004.109-.004.165C19.947 17.454 16.391 20 12 20z"/>
                        <circle cx="9" cy="13" r="1.25"/><circle cx="12" cy="17" r="1.25"/><circle cx="15" cy="13" r="1.25"/>
                    </svg>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-0.5">We value your privacy</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                        We use cookies to improve your browsing experience and analyse site traffic.
                        <a href="#" class="text-indigo-600 dark:text-indigo-400 hover:underline underline-offset-2 transition-colors">Learn more</a>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 shrink-0 sm:pl-4">
                <button id="cookie-decline"
                        class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                    Decline
                </button>
                <button id="cookie-accept"
                        class="px-5 py-2 rounded-lg text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 transition-colors shadow-sm cursor-pointer">
                    Accept All
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        var banner = document.getElementById('cookie-banner');

        if (!localStorage.getItem('cookie_consent')) {
            setTimeout(function () {
                banner.style.transform = 'translateY(0)';
            }, 600);
        }

        function dismiss(choice) {
            localStorage.setItem('cookie_consent', choice);
            banner.style.transform = 'translateY(100%)';
        }

        document.getElementById('cookie-accept').addEventListener('click', function () {
            dismiss('accepted');
        });

        document.getElementById('cookie-decline').addEventListener('click', function () {
            dismiss('declined');
        });
    })();
</script>
