<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Academic extends Model
{
    protected $table = 'academic';

    protected $fillable = ['id_mhs', 'year', 'title', 'description', 'institution', 'attachment'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
