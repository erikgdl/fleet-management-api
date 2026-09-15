<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alugueis extends Model
{
    protected $fillable = [
        'carro_id',
        'data_inicio',
        'data_fim',
        'valor_diario',
        'valor_total',
        'status',
    ];

    public function carro()
    {
        return $this->belongsTo(Carro::class);
    }

}
