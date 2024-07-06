<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuariosElemento extends Model
{
    use HasFactory;

    protected $fillable = [
        'elemento_id', 'usuario_id', 'eliminado'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function elemento()
    {
        return $this->belongsTo(Elementos::class, 'elemento_id');
    }
}

