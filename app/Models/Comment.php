<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'body',
        'post_id',
        'reply_id',
        'parent_id'
    ];


    public function post()
    {
        return $this->belongsTo(Post::class);
    }


    public function peer()
    {
        return $this->belongsTo(self::class, 'reply_id');
    }


    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }


    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id');
    }
}