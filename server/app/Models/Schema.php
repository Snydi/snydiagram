<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Schema extends Model
{
    use HasFactory;
    public function project() : BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
    public function tables() : HasMany
    {
        return $this->hasMany(Table::class);
    }
}
