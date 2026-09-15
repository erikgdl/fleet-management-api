<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alugueis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('carro_id')
                ->constrained('carros')
                ->onDelete('cascade');

            $table->date('data_inicio');
            $table->date('data_fim')->nullable();

            $table->decimal('valor_diario', 10, 2);
            $table->decimal('valor_total', 10, 2)->nullable();

            $table->string('status')->default('ativo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alugueis');
    }
};
