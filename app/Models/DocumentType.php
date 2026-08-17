<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DocumentType extends Model
{
    protected $fillable = ['code', 'name', 'description', 'allowed_mime_types', 'maximum_size_kb', 'active'];

    protected function casts(): array
    {
        return ['allowed_mime_types' => 'array', 'active' => 'boolean'];
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
