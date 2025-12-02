<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // translation_keys table
        Schema::table('translation_keys', function (Blueprint $table) {
            $table->unique('key', 'idx_translation_keys_key');
        });

        // languages table
        Schema::table('languages', function (Blueprint $table) {
            $table->unique('code', 'idx_languages_code');
        });

        // translations table
        Schema::table('translations', function (Blueprint $table) {
            $table->unique(['translation_key_id', 'language_id'], 'idx_translations_key_lang');
        });

        // tags table
        Schema::table('tags', function (Blueprint $table) {
            $table->unique('name', 'idx_tags_name');
        });

        // tag_translation_key pivot table
        Schema::table('tag_translation_key', function (Blueprint $table) {
            $table->index('translation_key_id', 'idx_pivot_key');
            $table->index('tag_id', 'idx_pivot_tag');
            $table->unique(['translation_key_id', 'tag_id'], 'idx_pivot_unique');
        });
    }

    public function down(): void
    {
        Schema::table('translation_keys', function (Blueprint $table) {
            $table->dropUnique('idx_translation_keys_key');
        });

        Schema::table('languages', function (Blueprint $table) {
            $table->dropUnique('idx_languages_code');
        });

        Schema::table('translations', function (Blueprint $table) {
            $table->dropUnique('idx_translations_key_lang');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique('idx_tags_name');
        });

        Schema::table('translation_key_tag', function (Blueprint $table) {
            $table->dropIndex('idx_pivot_key');
            $table->dropIndex('idx_pivot_tag');
            $table->dropUnique('idx_pivot_unique');
        });
    }
};
