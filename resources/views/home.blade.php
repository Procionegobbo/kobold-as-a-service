<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Kobold As A Service</title>

    @fonts

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#FDFDFC] text-[#1b1b18] dark:bg-[#0a0a0a] dark:text-[#EDEDEC] antialiased">

    {{-- Navigation --}}
    <header class="sticky top-0 z-20 border-b border-[#19140035] bg-[#FDFDFC]/80 backdrop-blur dark:border-[#3E3E3A] dark:bg-[#0a0a0a]/80">
        <nav class="mx-auto flex max-w-5xl items-center justify-between gap-4 px-4 py-4 sm:px-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2 font-semibold tracking-tight">
                <span class="text-lg">🐉</span>
                <span class="text-sm sm:text-base">Kobold As A Service</span>
            </a>
            <a href="#api"
               class="rounded-md border border-[#19140035] px-3 py-1.5 text-sm font-medium transition hover:border-[#1915014a] hover:bg-[#1b1b18]/5 dark:border-[#3E3E3A] dark:hover:border-[#62605b] dark:hover:bg-white/5">
                Try the API
            </a>
        </nav>
    </header>

    <main class="mx-auto max-w-5xl px-4 sm:px-6">

        {{-- Hero --}}
        <section class="py-16 text-center sm:py-24">
            <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">Kobold As A Service</h1>
            <p class="mx-auto mt-6 max-w-2xl text-base leading-relaxed text-[#706f6c] sm:text-lg dark:text-[#A1A09A]">
                A procedural generation HTTP API that conjures whimsical Kobold RPG character sheets on demand.
                Every name, origin, and backstory is woven from Polygen grammars — fetch a fresh kobold with a single request.
            </p>
            <div class="mt-10 flex flex-col items-center justify-center gap-4 sm:flex-row">
                <a href="#api"
                   class="inline-flex w-full items-center justify-center rounded-md bg-[#1b1b18] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#000000] sm:w-auto dark:bg-[#EDEDEC] dark:text-[#1b1b18] dark:hover:bg-white">
                    Try the API
                </a>
                <a href="#links"
                   class="inline-flex w-full items-center justify-center rounded-md border border-[#19140035] px-6 py-3 text-sm font-semibold transition hover:border-[#1915014a] hover:bg-[#1b1b18]/5 sm:w-auto dark:border-[#3E3E3A] dark:hover:border-[#62605b] dark:hover:bg-white/5">
                    Explore the ecosystem
                </a>
            </div>
        </section>

        {{-- API code examples --}}
        <section id="api" class="scroll-mt-20 py-12 sm:py-16">
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Call the API</h2>
            <p class="mt-3 max-w-2xl text-[#706f6c] dark:text-[#A1A09A]">
                Send a <code class="rounded bg-[#1b1b18]/5 px-1.5 py-0.5 text-sm dark:bg-white/10">POST</code>
                request to <code class="rounded bg-[#1b1b18]/5 px-1.5 py-0.5 text-sm dark:bg-white/10">/api/generate-kobold</code>
                and get a complete character sheet back. Pick your language:
            </p>

            <div class="mt-8 overflow-hidden rounded-xl border border-[#19140035] dark:border-[#3E3E3A]"
                 data-code-tabs>
                {{-- Tab buttons --}}
                <div class="flex overflow-x-auto border-b border-[#19140035] bg-[#1b1b18]/5 dark:border-[#3E3E3A] dark:bg-white/5"
                     role="tablist">
                    @foreach (['php' => 'PHP', 'javascript' => 'JavaScript', 'go' => 'Go', 'python' => 'Python', 'curl' => 'cURL'] as $key => $label)
                        <button type="button"
                                data-tab-button="{{ $key }}"
                                class="shrink-0 border-b-2 border-transparent px-4 py-2.5 text-sm font-medium text-[#706f6c] transition hover:text-[#1b1b18] dark:text-[#A1A09A] dark:hover:text-[#EDEDEC]">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- Code panels --}}
                <div class="bg-[#161615] text-[#EDEDEC]">
                    <div data-tab-panel="php">
<pre class="overflow-x-auto p-4 text-sm leading-relaxed"><code>use GuzzleHttp\Client;

$client = new Client();

$response = $client->post('https://kaas.procionegobbo.it/api/generate-kobold', [
    'json' => ['language' => 'en'],
]);

$kobold = json_decode((string) $response->getBody(), true);</code></pre>
                    </div>

                    <div data-tab-panel="javascript" hidden>
<pre class="overflow-x-auto p-4 text-sm leading-relaxed"><code>const response = await fetch('https://kaas.procionegobbo.it/api/generate-kobold', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ language: 'en' }),
});

const kobold = await response.json();</code></pre>
                    </div>

                    <div data-tab-panel="go" hidden>
<pre class="overflow-x-auto p-4 text-sm leading-relaxed"><code>package main

import (
    "bytes"
    "net/http"
)

func main() {
    body := bytes.NewBufferString(`{"language":"en"}`)
    http.Post("https://kaas.procionegobbo.it/api/generate-kobold", "application/json", body)
}</code></pre>
                    </div>

                    <div data-tab-panel="python" hidden>
<pre class="overflow-x-auto p-4 text-sm leading-relaxed"><code>import requests

response = requests.post(
    "https://kaas.procionegobbo.it/api/generate-kobold",
    json={"language": "en"},
)

kobold = response.json()</code></pre>
                    </div>

                    <div data-tab-panel="curl" hidden>
<pre class="overflow-x-auto p-4 text-sm leading-relaxed"><code>curl -X POST https://kaas.procionegobbo.it/api/generate-kobold \
  -H "Content-Type: application/json" \
  -d '{"language": "en"}'</code></pre>
                    </div>
                </div>
            </div>
        </section>

        {{-- Live output example --}}
        <section class="py-12 sm:py-16">
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Sample Output</h2>
            <p class="mt-3 max-w-2xl text-[#706f6c] dark:text-[#A1A09A]">
                Every field below is generated live by the Polygen grammar engine and changes on each page load.
            </p>

            @if ($kobold)
                <div class="mt-8 overflow-hidden rounded-xl border border-[#19140035] bg-[#161615] dark:border-[#3E3E3A]">
                    <div class="flex items-center gap-2 border-b border-white/10 px-4 py-2.5">
                        <span class="h-3 w-3 rounded-full bg-[#ff5f57]"></span>
                        <span class="h-3 w-3 rounded-full bg-[#febc2e]"></span>
                        <span class="h-3 w-3 rounded-full bg-[#28c840]"></span>
                        <span class="ml-2 text-xs text-[#A1A09A]">POST /api/generate-kobold</span>
                    </div>
<pre class="overflow-x-auto whitespace-pre-wrap break-words p-4 text-sm leading-relaxed text-[#EDEDEC]"><code>{{ json_encode($kobold, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</code></pre>
                </div>
            @endif
        </section>

        {{-- Inspirations & Credits --}}
        <section class="py-12 sm:py-16">
            <div class="rounded-xl border border-[#19140035] bg-[#1b1b18]/[0.02] p-6 sm:p-8 dark:border-[#3E3E3A] dark:bg-white/[0.02]">
                <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Standing on the shoulders of Kobolds</h2>
                <p class="mt-4 max-w-3xl leading-relaxed text-[#706f6c] dark:text-[#A1A09A]">
                    KAAS is powered by the
                    <a href="https://polygen.org/" target="_blank" rel="noopener noreferrer"
                       class="font-medium text-[#1b1b18] underline decoration-dotted underline-offset-4 hover:decoration-solid dark:text-[#EDEDEC]">Polygen</a>
                    generative grammar engine, which turns compact grammar files into endless procedural prose.
                    The project was inspired by the tabletop adventures and podcasts of
                    <a href="https://www.fumblegdr.it" target="_blank" rel="noopener noreferrer"
                       class="font-medium text-[#1b1b18] underline decoration-dotted underline-offset-4 hover:decoration-solid dark:text-[#EDEDEC]">FumbleGDR</a>
                    — they don't ship a character generator, but their stories sparked the idea for this one.
                </p>
            </div>
        </section>

        {{-- Ecosystem links --}}
        <section id="links" class="scroll-mt-20 py-12 sm:py-16">
            <h2 class="text-2xl font-bold tracking-tight sm:text-3xl">Ecosystem</h2>
            <p class="mt-3 max-w-2xl text-[#706f6c] dark:text-[#A1A09A]">
                Explore the tools and community behind KAAS.
            </p>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2">
                @php
                    $ecosystemLinks = [
                        ['label' => 'polygen-php on GitHub', 'url' => 'https://github.com/procionegobbo/polygen-php'],
                        ['label' => 'polygen-php on Packagist', 'url' => 'https://packagist.org/packages/procionegobbo/polygen-php'],
                        ['label' => 'Laravel', 'url' => 'https://laravel.com'],
                        ['label' => 'procionegobbo.it', 'url' => 'https://procionegobbo.it'],
                    ];
                @endphp

                @foreach ($ecosystemLinks as $link)
                    <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer"
                       class="group flex items-center justify-between rounded-lg border border-[#19140035] px-5 py-4 transition hover:border-[#1915014a] hover:bg-[#1b1b18]/5 dark:border-[#3E3E3A] dark:hover:border-[#62605b] dark:hover:bg-white/5">
                        <span class="font-medium">{{ $link['label'] }}</span>
                        <span class="text-[#706f6c] transition group-hover:translate-x-0.5 dark:text-[#A1A09A]">↗</span>
                    </a>
                @endforeach
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="mt-8 border-t border-[#19140035] dark:border-[#3E3E3A]">
        <div class="mx-auto max-w-5xl px-4 py-8 text-center text-sm text-[#706f6c] sm:px-6 dark:text-[#A1A09A]">
            Made with ❤️ by
            <a href="https://procionegobbo.it" target="_blank" rel="noopener noreferrer"
               class="font-medium text-[#1b1b18] underline decoration-dotted underline-offset-4 hover:decoration-solid dark:text-[#EDEDEC]">Federico "Procionegobbo" Maiorini</a>
        </div>
    </footer>

    {{-- Tab switcher (vanilla JS, progressive enhancement) --}}
    <script>
        document.querySelectorAll('[data-code-tabs]').forEach((container) => {
            const buttons = container.querySelectorAll('[data-tab-button]');
            const panels = container.querySelectorAll('[data-tab-panel]');
            const activeClasses = ['border-[#EDEDEC]', 'text-[#1b1b18]', 'dark:text-[#EDEDEC]', 'bg-white/10'];

            const activate = (key) => {
                panels.forEach((panel) => { panel.hidden = panel.dataset.tabPanel !== key; });
                buttons.forEach((button) => {
                    button.classList.toggle('border-transparent', button.dataset.tabButton !== key);
                    activeClasses.forEach((cls) => button.classList.toggle(cls, button.dataset.tabButton === key));
                });
            };

            buttons.forEach((button) => button.addEventListener('click', () => activate(button.dataset.tabButton)));
            activate('php');
        });
    </script>
</body>
</html>
