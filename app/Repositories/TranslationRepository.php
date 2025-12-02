<?php

namespace App\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TranslationRepository
{
    /**
     * Return associative array key => value for a language (and optional tags).
     */
    public function fetchKeyValuePairsByLocaleAndTags(int $languageId, ?array $tags = null): array
    {
        $query = DB::table('translations')
            ->select('translation_keys.key as key', 'translations.value as value')
            ->join('translation_keys', 'translations.translation_key_id', '=', 'translation_keys.id')
            ->where('translations.language_id', $languageId);

        if (! empty($tags)) {
            // join pivot and tags table
            $query->join('tag_translation_key', 'translation_keys.id', '=', 'tag_translation_key.translation_key_id')
                ->join('tags', 'tag_translation_key.tag_id', '=', 'tags.id')
                ->whereIn('tags.name', $tags)
                ->groupBy('translation_keys.id', 'translations.value', 'translation_keys.key');
        }

        // Use pluck to get key => value
        $rows = $query->pluck('value', 'key')->toArray();

        return $rows;
    }

    /**
     * Get language id by code.
     */
    public function getLanguageIdByCode(string $code): ?int
    {
        $row = DB::table('languages')->select('id')->where('code', $code)->first();

        return $row?->id ?? null;
    }

    /**
     * Invalidate caches is handled in service (we maintain key format there).
     */
}
