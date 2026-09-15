<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class VerificarManutencaoJob implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        //
    }

    /**
     * Job
     *
     * Esse código não depende de um usuário interagindo
     *
     * ele será executado automaticamente pelo Scheduler
     */
    public function handle(): void
    {
        $carros = Carro::where('quilometragem', '>=', 10000)->get();

        foreach ($carros as $carro) {

            echo "Carro {$carro->placa} precisa de manutenção." . PHP_EOL;

        }
    }
}
