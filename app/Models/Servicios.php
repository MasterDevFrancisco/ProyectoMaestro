<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Servicios extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = ['nombre', 'razon_social_id', 'eliminado'];

    public function razonSocial()
    {
        return $this->belongsTo(RazonSocial::class, 'razon_social_id');
    }

    public function elementos()
    {
        return $this->hasMany(Elementos::class, 'servicios_id');
    }
}

