<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formatos extends Model
{
    use HasFactory;
    public function documento ()
    {
        return $this ->morphOne('App\Models\Documento','documentable');
    }
}
