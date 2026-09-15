<?php

namespace App\Http\Controllers;

use App\Actions\CriarAluguelAction;
use App\Actions\FinalizarAluguelAction;
use App\Models\Alugueis;
use App\Http\Requests\AluguelRequest;

class AlugueisController extends Controller
{

    // Lista todos os aluguéis
    public function index()
    {
        return response()->json(
            Aluguel::with('carro')->get()
        );
    }

    // Cria um novo aluguel
    public function store(
        AluguelRequest $request,
        CriarAluguelAction $action)
    {
        $carro = Carro::findOrFail($request->carro_id);

        $aluguel = $action->execute(
            $carro,
            $request->validated()
        );

        return response()->json($aluguel, 201);
    }

    // Mostra um aluguel específico
    public function show(Alugueis $aluguel)
    {
        return response()->json(
            $aluguel->load('carro')
        );
    }

    // Recebe a requisição que veio da Route.
    //
    // O Laravel identifica o ID do aluguel na URL
    // e busca automaticamente o Aluguel correspondente.
    //
    // O Controller NÃO calcula valor,
    // NÃO decide quantos dias,
    // NÃO libera o carro.
    //
    // Ele apenas chama a Action responsável
    // por executar essa regra.
     public function finalizar(
         Alugueis $aluguel,
         FinalizarAluguelAction $action
     ) {
         $action->execute($aluguel);

         // Depois que a Action termina,
         // devolvemos uma resposta para quem fez a requisição.
         return response()->json([
             'message' => 'Aluguel finalizado com sucesso!'
         ]);
     }
}
