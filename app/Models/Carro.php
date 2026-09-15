<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Carro extends Model
{
    protected $fillable = [
        'placa',
        'modelo',
        'marca',
        'status',
        'quilometragem',
        'valor_diaria'
    ];

    public function alugueis()
    {
        return $this->hasMany(Alugueis::class);
    }

}
