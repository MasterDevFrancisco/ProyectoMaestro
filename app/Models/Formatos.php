<?php

// app/Models/Formatos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formatos extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'ruta_pdf', 'formatos_id', 'eliminado'];
    public function campos()
    {
        return $this->hasMany(Campos::class);
    }
}
