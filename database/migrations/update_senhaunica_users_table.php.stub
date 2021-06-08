<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSenhaunicaUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Caso necessário, ajuste para refletir suas necessidades
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change(); # deixar opcional
            if (!Schema::hasColumn('users', 'codpes')) {
                $table->integer('codpes');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Não vamos remover as colunas para preservar os dados
        //$table->dropColumn('codpes');
    }
}
