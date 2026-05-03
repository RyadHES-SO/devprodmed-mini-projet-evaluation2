<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ApiRatingController extends Controller
{
    /**
     * GET /api/v1/posts/{post}/ratings
     * Retourne la note moyenne et le nombre de votes du post.
     */
    public function index(string $postId)
    {
        $post = Post::findOrFail($postId);

        return response()->json([
            'post_id'        => $post->id,
            'average_rating' => $post->averageRating(),
            'ratings_count'  => $post->ratings()->count(),
        ]);
    }

    /**
     * POST /api/v1/posts/{post}/ratings
     * Soumet ou met à jour une note via l'API.
     */
    public function store(Request $request, string $postId)
    {
        $post = Post::findOrFail($postId);

        Gate::authorize('create', [Rating::class, $post]);

        $validated = $request->validate([
            'stars' => 'required|integer|min:1|max:5',
        ]);

        $rating = Rating::updateOrCreate(
            ['user_id' => $request->user()->id, 'post_id' => $post->id],
            ['stars' => $validated['stars']]
        );

        // L'API retourne du JSON, pas une redirection
        return response()->json([
            'post_id'        => $post->id,
            'stars'          => $rating->stars,
            'average_rating' => $post->averageRating(),
            'ratings_count'  => $post->ratings()->count(),
        ], 201);
    }

    /**
     * DELETE /api/v1/posts/{post}/ratings
     * Supprime sa note via l'API.
     */
    public function destroy(Request $request, string $postId)
    {
        $post = Post::findOrFail($postId);

        $rating = Rating::where('user_id', $request->user()->id)
            ->where('post_id', $post->id)
            ->firstOrFail();

        Gate::authorize('delete', $rating);

        $rating->delete();

        // 204 = succès sans contenu à retourner
        return response()->json(null, 204);
    }
}