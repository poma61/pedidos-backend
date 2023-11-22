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
        Schema::create('users', function (Blueprint $table) {
            $table->engine = 'InnoDB ROW_FORMAT=DYNAMIC';
            
            $table->id();
            $table->string('usuario')->unique();
            $table->text('password');
            $table->unsignedBigInteger('id_personal');
            $table->boolean('status');
            $table->timestamps();
           
            $table->foreign('id_personal')->references('id')->on('personals');    

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
