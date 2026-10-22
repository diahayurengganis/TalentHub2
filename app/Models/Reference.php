<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reference extends Model
{
    protected $table = 'reference';

    protected $fillable = ['id_mhs', 'name', 'title', 'institution', 'email', 'telp'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
