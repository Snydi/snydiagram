<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    use HasFactory;

    public function table() : BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
    public function types() : HasMany
    {
        return $this->hasMany(Type::class);
    }
    public function modifiers() : HasMany
    {
        return $this->hasMany(Modifier::class);
    }
}
