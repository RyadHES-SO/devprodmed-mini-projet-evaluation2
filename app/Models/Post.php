<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    // Liste des champs qu'on a le droit de modifier
    protected $fillable = ['title', 'content', 'image', 'user_id', 'exif_data'];

    protected $casts = [
        //Laravel convertit automatiquement le JSON en tableau PHP (sans json_encode(), json_decode())
        'exif_data' => 'array',
    ];
    
    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the users who liked the post.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'likes')->using(Like::class)->withTimestamps()->withPivot('reaction');
    }

    /**
     * Un post a plusieurs ratings.
     * HasMany = "a plusieurs" — un post peut avoir plusieurs notes
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Calcule la note moyenne du post, arrondie à 1 décimale.
     * On définit ça comme une méthode du modèle car c'est
     * une logique liée aux données du post.
     */
    public function averageRating(): float
    {
        // avg() fait la moyenne SQL, ?? 0 retourne 0 si aucune note
        return round($this->ratings()->avg('stars') ?? 0, 1);
    }
}