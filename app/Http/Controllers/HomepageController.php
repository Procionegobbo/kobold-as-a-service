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
        $kobold = Cache::remember(
            'homepage.kobold',
            15,
            fn (): array => $service->generate('en'),
        );

        return view('home', compact('kobold'));
    }
}
