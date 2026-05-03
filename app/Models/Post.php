<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
}
