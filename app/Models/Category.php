<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
        return $this->hasMany(Post::class);
    }


    public function scopeShowInHome($query)
    {
        return $query->where('show_in_home', true);
    }
}