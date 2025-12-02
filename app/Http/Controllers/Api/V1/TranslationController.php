<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\TranslationService;
use App\Models\TranslationKey;
use App\Models\Language;
use App\Models\Translation;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTranslationRequest;
use App\Http\Requests\UpdateTranslationRequest;

class TranslationController extends Controller
{
    public function __construct(
        protected TranslationService $service
    ) {}

    /**
     * Export all translations for a locale (+ optional tags)
     */
    public function export($locale, Request $request)
    {
        $tags = $request->query('tags');  
        $tagsArray = $tags ? explode(',', $tags) : null;

        $data = $this->service->exportLocale($locale, $tagsArray);

        return response()->json([
            'locale' => $locale,
            'translations' => $data,
        ]);
    }

    /**
     * Create a translation entry
     */
    public function store(StoreTranslationRequest $request)
    {
        $validated = $request->validated();

        // Find or create translation key
        $translationKey = TranslationKey::firstOrCreate([
            'key' => $validated['key']
        ]);

        // Get language
        $language = Language::where('code', $validated['locale'])->firstOrFail();

        // Create or update translation
        Translation::updateOrCreate(
            [
                'translation_key_id' => $translationKey->id,
                'language_id' => $language->id,
            ],
            [
                'value' => $validated['value']
            ]
        );

        // Attach tags if provided
        if (!empty($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $translationKey->tags()->sync($tagIds);
        }

        // Invalidate cache for this locale
        $this->service->invalidateLocaleCaches($validated['locale']);

        return response()->json([
            'message' => 'Translation created successfully.'
        ], 201);
    }

    /**
     * Show a translation by key + locale
     */
    public function show($key, Request $request)
    {
        $locale = $request->query('locale');

        if (!$locale) {
            return response()->json(['error' => 'Locale is required'], 422);
        }

        $translationKey = TranslationKey::where('key', $key)->firstOrFail();
        $language = Language::where('code', $locale)->firstOrFail();

        $translation = Translation::where('translation_key_id', $translationKey->id)
            ->where('language_id', $language->id)
            ->first();

        return response()->json([
            'key' => $key,
            'locale' => $locale,
            'value' => $translation ? $translation->value : null
        ]);
    }

    /**
     * Update a translation
     */
    public function update($key, UpdateTranslationRequest $request)
    {
        $validated = $request->validated();

        $translationKey = TranslationKey::firstOrCreate([
            'key' => $key
        ]);

        $language = Language::where('code', $validated['locale'])->firstOrFail();

        Translation::updateOrCreate(
            [
                'translation_key_id' => $translationKey->id,
                'language_id' => $language->id,
            ],
            [
                'value' => $validated['value']
            ]
        );

        if (!empty($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(['name' => $tagName]);
                $tagIds[] = $tag->id;
            }
            $translationKey->tags()->sync($tagIds);
        }

        // Invalidate locale cache
        $this->service->invalidateLocaleCaches($validated['locale']);

        return response()->json([
            'message' => 'Translation updated successfully.'
        ]);
    }

    /**
     * Search translations
     */
    public function index(Request $request)
    {
        $key = $request->query('key');
        $content = $request->query('content');
        $tags = $request->query('tags');

        $query = TranslationKey::query();

        if ($key) $query->where('key', 'LIKE', "%$key%");
        if ($tags) {
            $tagArray = explode(',', $tags);
            $query->whereHas('tags', function($q) use ($tagArray) {
                $q->whereIn('name', $tagArray);
            });
        }

        $results = $query->with(['translations.language', 'tags'])->paginate(20);

        return response()->json($results);
    }
}
