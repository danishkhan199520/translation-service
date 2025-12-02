<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TranslationKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'context',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(Translation::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'tag_translation_key', 'translation_key_id', 'tag_id');
    }
}
