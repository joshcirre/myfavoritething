<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Feed extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'days_active',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($feed) {
            $feed->posts()->delete();
            $feed->favoritedBy()->detach();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'feed_user')->withTimestamps();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where(function ($query) use ($value) {
            $query->where('slug', $value)
                ->orWhere(function ($query) use ($value) {
                    if (DB::connection()->getDriverName() === 'pgsql') {
                        $query->orWhereRaw('id::text = ?', [$value]);
                    } else {
                        $query->orWhere('id', $value);
                    }
                });
        })->firstOrFail();
    }

    public function isActive()
    {
        return $this->created_at->addDays($this->days_active)->isFuture();
    }

    public function canAccess(User $user)
    {
        return $this->isActive() ||
               $this->user_id === $user->id ||
               $this->favoritedBy->contains($user);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'feed_user')->withTimestamps();
    }

    public function favoriteVotes()
    {
        return $this->belongsToMany(User::class, 'feed_user')
            ->withPivot('favorite_post_id')
            ->withTimestamps();
    }
}
