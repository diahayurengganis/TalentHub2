<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentProfile extends Model
{
    protected $table = 'student_profile';

    protected $fillable = [
        'id_mhs', 'profile', 'gpa', 'personal_email', 'profile_picture',
        'address', 'phone', 'skill', 'twitter', 'instagram', 'linkedin', 'facebook',
        'views_count'
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
