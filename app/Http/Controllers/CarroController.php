<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CarroRequest;
use App\Models\Carro;
class CarroController extends Controller
{
    // Lista todos os carros
    public function index()
    {
        return response()->json(
            Carro::all()
        );
    }

    // Cria um novo carro
    public function store(CarroRequest $request)
    {
        $carro = Carro::create([
            'placa' => $request->placa,
            'modelo' => $request->modelo,
            'status' => 'disponivel',
            'quilometragem' => $request->quilometragem,
        ]);

        return response()->json($carro, 201);
    }

    // Mostra um carro específico
    public function show(Carro $carro)
    {
        return response()->json($carro);
    }

    // Atualiza um carro
    public function update(CarroRequest $request, Carro $carro)
    {
        $carro->update([
            'placa' => $request->placa,
            'modelo' => $request->modelo,
            'quilometragem' => $request->quilometragem,
            'status' => $request->status,
        ]);

        return response()->json($carro);
    }

    // Exclui um carro
    public function destroy(Carro $carro)
    {
        $carro->delete();

        return response()->json([
            'message' => 'Carro excluído com sucesso!'
        ]);
    }
}
