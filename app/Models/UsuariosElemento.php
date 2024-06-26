<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class UsuariosElemento extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'usuario_id',
        'elemento_id',
        'eliminado',
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
