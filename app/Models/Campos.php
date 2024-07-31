<?php
// app/Models/Campos.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campos extends Model
{
    use HasFactory;

    protected $fillable = ['tablas_id', 'nombre_columna', 'linkname', 'status'];

    
}
