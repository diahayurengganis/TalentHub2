<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RelatedProduct extends Model
{
    protected $table = 'related_product';

    protected $fillable = ['id_mhs', 'title', 'description', 'thumbnail', 'link'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'id_mhs', 'id_mhs');
    }
}
