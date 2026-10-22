<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Competency extends Model
{
    protected $table = 'competency';

    protected $fillable = ['id_mhs', 'kode_matkul', 'nilai'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }

    public function master(): BelongsTo
    {
        return $this->belongsTo(CompetencyMaster::class, 'kode_matkul', 'kode_matkul');
    }
}
