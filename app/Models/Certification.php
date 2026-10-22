<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certification extends Model
{
    protected $table = 'certifications';

    protected $fillable = [
        'id_mhs',
        'title',
        'start_date', // 🌟 Diperbarui
        'end_date',   // 🌟 Ditambahkan
        'institution',
        'description',
        'attachment'
    ];

    // 🌟 Otomatis mengonversi string tanggal menjadi objek Date mutasi Laravel
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /**
     * Relasi balik ke model Student (Many to One)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
