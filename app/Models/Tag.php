<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'user_id',
        'show_in_home'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tag', 'tag_id', 'post_id')->withTimestamps();
    }


    public function scopeShowInHome($query)
    {
        return $query->where('show_in_home', true);
    }
}