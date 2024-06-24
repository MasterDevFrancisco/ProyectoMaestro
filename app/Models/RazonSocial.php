<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class RazonSocial extends Model
{
    use HasFactory;
    use HasRoles;

    protected $fillable = [
        'name',
        'eliminado',
    ];

    // RazonSocial.php
    public function users()
    {
        return $this->hasMany(User::class, 'razon_social_id');
    }
}
