<?php

namespace App\Actions;

use App\Models\Alugueis;
use Illuminate\Support\Facades\DB;

class FinalizarAluguelAction
{

    /*
     * Action
     *
     * aqui fica a Regra de negocio.
     *
     * A regra é:
     * 1 - descobrir quantos dias foram usados
     * 2 - calcular o valor total
     * 3 - finalizar o aluguel
     * 4- liberar o carro
     *
     */

    public function execute(Alugueis $aluguel)
    { DB::transaction(function () use ($aluguel) {

        // Verifica se o aluguel está ativo
        if ($aluguel->status !== 'ativo') {
            throw new \Exception('Esse aluguel já foi finalizado.');
        }

        // Calcula a quantidade de dias, diffInDays() == “diferença em dias”
        $dias = $aluguel->data_inicio->diffInDays($aluguel->data_fim);

        // Calcula o valor total
        $aluguel->update([
            'valor_total' => $dias * $aluguel->valor_diario,
            'status' => 'finalizado',
        ]);

        // salva o status
        $aluguel->carro->update([
            'status' => 'disponivel'
        ]);

    });

    }


}
