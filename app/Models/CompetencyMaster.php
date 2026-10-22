<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompetencyMaster extends Model
{
    protected $table = 'competency_master';
    protected $primaryKey = 'kode_matkul';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['kode_matkul', 'kode_jurusan', 'nama_matkul', 'competency', 'description'];

    public function competencies(): HasMany
    {
        return $this->hasMany(Competency::class, 'kode_matkul', 'kode_matkul');
    }
}
