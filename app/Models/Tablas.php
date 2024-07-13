<?php
// app/Models/Tablas.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tablas extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'elementos_id'];
}

// app/Models/Campos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campos extends Model
{
    use HasFactory;

    protected $fillable = ['tablas_id', 'nombre_columna', 'status'];
}
