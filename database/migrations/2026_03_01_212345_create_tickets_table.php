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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->onDelete('set null');
            $table->foreignId('contacto_id')->nullable()->constrained('contactos')->onDelete('set null');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_soporte')->onDelete('set null');
            $table->foreignId('tecnico_id')->nullable()->constrained('tecnicos')->onDelete('set null');
            $table->foreignId('creado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->string('titulo');
            $table->text('descripcion');
            $table->enum('estado', ['abierto','asignado','en_proceso','en_espera','resuelto','cerrado'])->default('abierto');
            $table->enum('prioridad', ['baja','media','alta','critica'])->default('media');
            $table->timestamp('fecha_inicio')->nullable();
            $table->timestamp('fecha_resolucion')->nullable();
            $table->text('observaciones_internas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
