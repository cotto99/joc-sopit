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
        Schema::table('facturas', function (Blueprint $table) {
            $table->boolean('aplica_iva')->default(false)->after('subtotal');
            $table->decimal('porcentaje_iva', 5, 2)->default(12)->after('aplica_iva');
            // impuesto y total ya existen, solo los usamos diferente
        });
    }
    
    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropColumn(['aplica_iva', 'porcentaje_iva']);
        });
    }
};
