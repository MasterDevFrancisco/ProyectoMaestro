<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Elementos extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = [
        'nombre', 'servicios_id', 'eliminado'
    ];
    
    public function servicio()
    {
        return $this->belongsTo(Servicios::class, 'servicios_id');
    }

    public function usuariosElementos()
    {
        return $this->hasMany(UsuariosElemento::class, 'elemento_id');
    }
}

