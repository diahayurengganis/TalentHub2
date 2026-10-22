<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id_mhs';
    public $incrementing = false; // Karena NIM berupa string
    protected $keyType = 'string';

    protected $fillable = ['id_mhs', 'name', 'prodi', 'jurusan'];

    // Relasi One-to-One ke Profil Detail
    public function profile(): HasOne
    {
        return $this->hasOne(StudentProfile::class, 'id_mhs', 'id_mhs');
    }

    public function certifications(): HasMany
    {
        return $this->hasMany(Certification::class, 'id_mhs', 'id_mhs');
    }

    public function academics(): HasMany
    {
        return $this->hasMany(Academic::class, 'id_mhs', 'id_mhs');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'id_mhs', 'id_mhs');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(Education::class, 'id_mhs', 'id_mhs');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(JobExperience::class, 'id_mhs', 'id_mhs');
    }
}