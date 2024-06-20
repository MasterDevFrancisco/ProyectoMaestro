<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Documento extends Model
{
    use HasFactory;
    use HasRoles;

    public function documentable()
    {
        return $this->morphTo();
    }
}
