<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RatingController extends Controller
{
    /**
     * Soumettre ou mettre à jour une note.
     * On combine création et mise à jour en une seule méthode
     * car l'utilisateur ne peut avoir qu'une note par post.
     */
    public function store(Request $request, string $postId)
    {
        $post = Post::findOrFail($postId);

        // Vérifie la policy : l'utilisateur ne peut pas noter son propre post
        Gate::authorize('create', [Rating::class, $post]);

        $validated = $request->validate([
            // La note doit être un entier entre 1 et 5
            'stars' => 'required|integer|min:1|max:5',
        ]);

        $user = $request->user();

        // updateOrCreate : met à jour si existe, crée sinon
        // Premier tableau = conditions de recherche
        // Deuxième tableau = valeurs à mettre à jour/créer
        Rating::updateOrCreate(
            ['user_id' => $user->id, 'post_id' => $post->id],
            ['stars' => $validated['stars']]
        );

        return redirect("/posts/$post->id");
    }

    /**
     * Supprimer sa note.
     */
    public function destroy(string $postId)
    {
        $post = Post::findOrFail($postId);
        $user = request()->user();

        $rating = Rating::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->firstOrFail();

        // Vérifie que c'est bien la note de l'utilisateur connecté
        Gate::authorize('delete', $rating);

        $rating->delete();

        return redirect("/posts/$post->id");
    }
}