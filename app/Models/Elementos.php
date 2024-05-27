<?php

// App\Models\Elementos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Elementos extends Model
{
    use HasFactory;

    public function servicio()
    {
        return $this->belongsTo(Servicios::class, 'servicios_id');
    }
}
