<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    public function schema() : BelongsTo
    {
        return $this->belongsTo(Schema::class);
    }
    public function fields() : HasMany
    {
        return $this->hasMany(Field::class);

    }

}
