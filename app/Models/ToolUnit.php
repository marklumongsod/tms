<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'tool_id',
        'serial_code',
        'qr_path',
        'status', // 'available', 'borrowed', 'returned'
        'for_maintenance', // boolean
    ];

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }
}
