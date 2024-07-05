<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tablas extends Model
{
    use HasFactory;

    protected $table = 'tablas';

    protected $fillable = [
        'nombre',
    ];

    public function campos()
    {
        return $this->hasMany(Campos::class, 'tablas_id');
    }
    public function elemento()
    {
        return $this->belongsTo(Elementos::class, 'elementos_id');
    }
}
