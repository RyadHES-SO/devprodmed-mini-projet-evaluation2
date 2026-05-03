<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\Rating;
use App\Models\User;

class RatingPolicy
{
    /**
     * Règle : un utilisateur peut noter un post seulement s'il n'en est pas l'auteur.
     * On ne peut pas noter sa propre photo — ça n'aurait pas de sens.
     */
    public function create(User $user, Post $post): bool
    {
        // $user->id !== $post->user_id = l'utilisateur n'est pas l'auteur du post
        return $user->id !== $post->user_id;
    }

    /**
     * Règle : un utilisateur peut modifier/supprimer uniquement son propre rating.
     */
    public function update(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id;
    }

    /**
     * delete utilise la même règle que update.
     */
    public function delete(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id;
    }
}