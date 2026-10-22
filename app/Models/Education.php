<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Education extends Model
{
    protected $table = 'education';

    protected $fillable = ['id_mhs', 'level', 'institution', 'year_start', 'year_end', 'major', 'certificate', 'transcript', 'other'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
