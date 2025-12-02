<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function translationKeys(): BelongsToMany
    {
        return $this->belongsToMany(TranslationKey::class, 'tag_translation_key', 'tag_id', 'translation_key_id');
    }
}
