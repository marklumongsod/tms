<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ToolUnit;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'part_number',
        'description',
        'quantity',
    ];

    public function units()
    {
        return $this->hasMany(ToolUnit::class);
    }
}
