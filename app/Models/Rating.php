<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    protected $fillable = ['user_id', 'post_id', 'stars'];

    /**
     * Un rating appartient à un utilisateur.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Un rating appartient à un post.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}