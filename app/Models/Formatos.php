<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Formatos extends Model
{
    use HasFactory;
    use HasRoles;

    public function documento ()
    {
        return $this ->morphOne('App\Models\Documento','documentable');
    }
}
