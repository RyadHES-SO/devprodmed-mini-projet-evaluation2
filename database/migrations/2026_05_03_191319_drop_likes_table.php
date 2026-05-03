<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // On supprime la table likes car on la remplace par le système de rating
        Schema::dropIfExists('likes');
    }

    public function down(): void
    {
        // Si on annule la migration, on recrée la table likes
        Schema::create('likes', function ($table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->enum('reaction', ['like', 'love', 'haha', 'wow', 'sad', 'angry'])->default('like');
            $table->timestamps();
            $table->unique(['user_id', 'post_id']);
        });
    }
};