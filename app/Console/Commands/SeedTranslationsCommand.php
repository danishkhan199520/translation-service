<?php

namespace App\Console\Commands;

use App\Models\Language;
use App\Models\Tag;
use App\Models\Translation;
use App\Models\TranslationKey;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedTranslationsCommand extends Command
{
    protected $signature = 'translations:seed {--count=100000}';
    protected $description = 'Seed a large number of translations for testing';

    public function handle()
    {
        $count = (int) $this->option('count');
        $languages = Language::all();

        if ($languages->count() === 0) {
            $this->error("No languages found! Run: php artisan db:seed --class=LanguagesSeeder");
            return;
        }

        $this->info("Seeding $count translation keys...");
        $batchSize = 1000;

        for ($i = 0; $i < $count; $i += $batchSize) {
            $batch = [];

            for ($x = 0; $x < $batchSize; $x++) {
                $batch[] = [
                    'key' => 'key_' . uniqid() . '_' . rand(1000, 9999),
                    'context' => 'Generated context',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            TranslationKey::insert($batch);
            $this->info("Inserted " . ($i + $batchSize) . " keys...");
        }

        $this->info("Now generating translations...");
        $keys = TranslationKey::pluck('id');

        foreach ($languages as $language) {
            $this->info("Generating translations for language: {$language->code}");

            $translationBatch = [];

            foreach ($keys as $keyId) {
                $translationBatch[] = [
                    'translation_key_id' => $keyId,
                    'language_id' => $language->id,
                    'value' => 'Value for ' . $keyId . ' (' . $language->code . ')',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (count($translationBatch) === $batchSize) {
                    Translation::insert($translationBatch);
                    $translationBatch = [];
                }
            }

            if (!empty($translationBatch)) {
                Translation::insert($translationBatch);
            }
        }

        $this->info("Seeding complete!");
    }
}
