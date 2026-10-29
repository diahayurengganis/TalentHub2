<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentVerification extends Model
{
    protected $table = 'student_verifications';

    protected $fillable = [
        'id_mhs',
        'status_verifikasi',
        'catatan',
        'verified_by',
    ];
}
