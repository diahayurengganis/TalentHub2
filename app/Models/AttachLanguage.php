<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttachLanguage extends Model
{
    protected $table = 'attach_language';

    protected $fillable = ['language_id', 'certificate', 'score', 'attachment'];

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
