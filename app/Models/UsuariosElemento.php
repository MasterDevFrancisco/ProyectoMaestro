<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsuariosElemento extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario_id',
        'elemento_id',
        'eliminado',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
