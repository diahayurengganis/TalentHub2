<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Language extends Model
{
    protected $table = 'language';

    protected $fillable = ['id_mhs', 'language', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(AttachLanguage::class, 'language_id', 'id');
    }
}
