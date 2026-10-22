<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    protected $table = 'achievement';

    protected $fillable = [
        'id_mhs', 'achievement', 'category', 'title', 'description',
        'time', 'organization', 'level', 'location', 'participant',
        'documentation', 'attachment'
    ];

    protected $casts = [
        'time' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
