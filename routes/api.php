<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AlugueisController;
use App\Http\Controllers\CarroController;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(CarroController::class)->group(function () {
    Route::get('/carro', 'index') -> name('carro');
    Route::post('/carros', 'store') -> name('carro.store');
    Route::get('/carro/{carro}', 'show') -> name('carro.show');
    Route::put('/carro/{carro}', 'update') -> name('carro.update');
    Route::delete('/carro/{carro}', 'destroy') -> name('carro.destroy');
});

Route::controller(AlugueisController::class)->group(function () {
    Route::get('/alugueis', 'index') -> name('alugueis');
    Route::post('/alugueis', 'store') -> name('alugueis.store');
    Route::get('/alugueis/{aluguel}', 'show') -> name('alugueis.show');
    Route::post('/alugueis/{aluguel}/finalizar', 'execute') -> name('alugueis.finalizar');
});


