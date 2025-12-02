<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguagesSeeder extends Seeder
{
    public function run(): void
    {
        $languages = [
            ['code' => 'en', 'name' => 'English'],
            ['code' => 'sv', 'name' => 'Swedish'],
            ['code' => 'fi', 'name' => 'Finnish'],
        ];

        foreach ($languages as $lang) {
            Language::firstOrCreate(['code' => $lang['code']], $lang);
        }
    }
}
