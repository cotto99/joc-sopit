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
    Schema::table('ticket_seguimientos', function (Blueprint $table) {
        $table->string('foto_evidencia')->nullable()->after('visible_cliente');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('ticket_seguimientos', function (Blueprint $table) {
        $table->dropColumn('foto_evidencia');
    });
}
};
