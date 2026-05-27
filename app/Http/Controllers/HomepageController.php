<?php

namespace App\Http\Controllers;

use App\Services\KoboldGeneratorService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class HomepageController extends Controller
{
    /**
     * Render the branded homepage with a freshly generated kobold sample.
     *
     * The grammar engine runs at most once per 15-second window thanks to the
     * cache, keeping the homepage fast while still showcasing live output.
     */
    public function __invoke(KoboldGeneratorService $service): View
    {
        $koboldEn = Cache::remember(
            'homepage.kobold.en',
            15,
            fn (): array => $service->generate('en'),
        );

        $koboldIt = Cache::remember(
            'homepage.kobold.it',
            15,
            fn (): array => $service->generate('it'),
        );

        return view('home', compact('koboldEn', 'koboldIt'));
    }
}
