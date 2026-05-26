<?php

namespace App\Services;

use JsonException;
use Polygen\Polygen;
use RuntimeException;

class KoboldGeneratorService
{
    private const DEFAULT_LANGUAGE = 'it';

    private const GRAMMAR_DIRECTORY = 'grm';

    private const GRAMMAR_PREFIX = 'kobold_json_';

    /**
     * Generate a kobold character sheet for the given language.
     *
     * Falls back to the default language when no matching grammar file exists.
     *
     * @return array<string, mixed>
     *
     * @throws RuntimeException When the resolved grammar file cannot be read.
     * @throws JsonException When the generated output is not valid JSON.
     */
    public function generate(string $language = self::DEFAULT_LANGUAGE): array
    {
        $grammarFile = $this->resolveGrammarFile($language);

        $output = Polygen::fromFile($grammarFile)->generate();

        return json_decode($output, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * Resolve the grammar file path for the given language, falling back to the
     * default language when no matching file exists.
     */
    private function resolveGrammarFile(string $language): string
    {
        $directory = base_path(self::GRAMMAR_DIRECTORY);

        $requested = $directory.DIRECTORY_SEPARATOR.self::GRAMMAR_PREFIX.strtolower($language).'.grm';

        if (is_file($requested)) {
            return $requested;
        }

        return $directory.DIRECTORY_SEPARATOR.self::GRAMMAR_PREFIX.self::DEFAULT_LANGUAGE.'.grm';
    }
}
