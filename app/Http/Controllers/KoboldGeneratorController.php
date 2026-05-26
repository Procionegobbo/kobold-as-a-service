<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateKoboldRequest;
use App\Services\KoboldGeneratorService;
use Illuminate\Http\JsonResponse;
use JsonException;
use RuntimeException;

class KoboldGeneratorController extends Controller
{
    /**
     * Generate a kobold character sheet for the requested language.
     */
    public function generate(GenerateKoboldRequest $request, KoboldGeneratorService $service): JsonResponse
    {
        $language = $request->string('language', 'it')->lower()->value();

        try {
            $kobold = $service->generate($language);
        } catch (RuntimeException) {
            return response()->json(['error' => 'Grammar file could not be loaded.'], 500);
        } catch (JsonException) {
            return response()->json(['error' => 'Generated output was not valid JSON.'], 500);
        }

        return response()->json($kobold);
    }
}
