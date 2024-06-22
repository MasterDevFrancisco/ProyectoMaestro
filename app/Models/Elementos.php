<?php

// App\Models\Elementos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Elementos extends Model
{
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'nombre', 'campos', 'servicios_id', 'eliminado'
    ];
    
    public function servicio()
    {
        return $this->belongsTo(Servicios::class, 'servicios_id');
    }
}
