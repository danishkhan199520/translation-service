<?php

namespace App\Services;

use App\Repositories\TranslationRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TranslationService
{
    public function __construct(
        protected TranslationRepository $repository
    ) {
    }

    /**
     * Export translations for a locale (string code) optionally filtered by tags.
     *
     * Returns associative array: key => value
     */
    public function exportLocale(string $localeCode, ?array $tags = null): array
    {
        $languageId = $this->repository->getLanguageIdByCode($localeCode);

        if ($languageId === null) {
            return [];
        }

        $canonicalTags = $this->canonicalizeTags($tags);
        $cacheKey = $this->makeCacheKey($localeCode, $canonicalTags);

        // Try cache
        $cached = Cache::get($cacheKey);
        if (is_array($cached)) {
            return $cached;
        }

        // Not cached -> fetch optimized from repository
        $rows = $this->repository->fetchKeyValuePairsByLocaleAndTags($languageId, $canonicalTags);

        // Ensure we return array
        $rows = is_array($rows) ? $rows : [];

        // Cache result
        $ttl = config('translations.cache_ttl', 300); // seconds
        Cache::put($cacheKey, $rows, $ttl);

        // Track the cache key for quick invalidation per-locale (store set)
        $this->rememberCacheKeyForLocale($localeCode, $cacheKey, $ttl);

        return $rows;
    }

    protected function canonicalizeTags(?array $tags): array|null
    {
        if (empty($tags)) {
            return null;
        }

        $tags = array_filter($tags, fn ($t) => is_string($t) && trim($t) !== '');
        $tags = array_map('trim', $tags);
        sort($tags, SORT_STRING);

        return array_values($tags);
    }

    protected function makeCacheKey(string $localeCode, ?array $tags = null): string
    {
        if (empty($tags)) {
            return "translations:export:{$localeCode}:all";
        }

        $tagString = implode(',', $tags);
        $hash = md5($tagString);

        return "translations:export:{$localeCode}:tags:{$hash}";
    }

    protected function rememberCacheKeyForLocale(string $localeCode, string $cacheKey, int $ttl): void
    {
        $setKey = "translations:cache_keys:{$localeCode}";
        // maintain a small redis set of cache keys so we can invalidate them on writes
        Cache::store()->put($cacheKey, Cache::get($cacheKey), $ttl); // ensure key exists (already set above)
        // store the set (we store an array - using put on a key that holds array of known keys)
        $existing = Cache::get($setKey, []);
        if (! in_array($cacheKey, $existing, true)) {
            $existing[] = $cacheKey;
            Cache::put($setKey, $existing, $ttl);
        }
    }

    /**
     * Invalidate all cache entries for a locale (call on writes affecting that locale).
     */
    public function invalidateLocaleCaches(string $localeCode): void
    {
        $setKey = "translations:cache_keys:{$localeCode}";
        $existing = Cache::get($setKey, []);
        if (is_array($existing)) {
            foreach ($existing as $key) {
                Cache::forget($key);
            }
        }
        // Clean the set
        Cache::forget($setKey);
    }
}
