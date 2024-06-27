<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campos extends Model
{
    use HasFactory;

    protected $table = 'campos';

    protected $fillable = [
        'tablas_id',
        'nombre_columna',
        'status',
    ];

    public function tabla()
    {
        return $this->belongsTo(Tablas::class, 'tablas_id');
    }
}
