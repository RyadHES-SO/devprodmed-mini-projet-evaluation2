<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            // Clé étrangère vers l'utilisateur qui note
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // Clé étrangère vers le post noté
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            // La note : entre 1 et 5
            $table->unsignedTinyInteger('stars');
            $table->timestamps();

            // Un utilisateur ne peut noter qu'une seule fois un post
            $table->unique(['user_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};