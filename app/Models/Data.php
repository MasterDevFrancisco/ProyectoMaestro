<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Data extends Model
{
    use HasFactory;

    protected $fillable = [
        'rowID', 'valor', 'campos_id', 'users_id', 'created_at', 'updated_at'
    ];

    public function campo()
    {
        return $this->belongsTo(Campos::class, 'campos_id');
    }
}
