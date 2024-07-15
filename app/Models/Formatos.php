<?php

// app/Models/Formatos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formatos extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'ruta_pdf', 'elementos_id', 'eliminado'];
}
