<?php

namespace App\Actions;

use App\Models\Alugueis;
use App\Models\Carro;

class CriarAluguelAction
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function execute(Carro $carro, array $dados) : Alugueis
    {
        return DB::transaction(function () use ($carro, $dados) {
            if ($carro->status !== 'disponivel') {
                throw new \Exception(
                    'O carro não está disponível para aluguel.'
                );
            }

            $aluguel = Alugueis::create([
                'carro_id' => $carro->id,
                'data_inicio' => $dados['data_inicio'],
                'data_fim' => $dados['data_fim'],
                'valor_diario' => $dados['valor_diario'],
                'status' => 'ativo',
            ]);

            $carro-> update([
                'status' => 'disponivel'
            ]);

            return $aluguel;
        });
    }
}
