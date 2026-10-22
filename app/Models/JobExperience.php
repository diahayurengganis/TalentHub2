<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobExperience extends Model
{
    protected $table = 'job_experience';

    protected $fillable = ['id_mhs', 'title', 'company', 'location', 'start_date', 'end_date', 'employment_type'];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
