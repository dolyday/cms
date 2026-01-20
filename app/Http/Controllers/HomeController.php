<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use App\Models\Category;
use App\Http\Resources\PostResource;

class HomeController extends Controller
{
    /**
     * Display the homepage with visible posts, categories, and tags.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $categories = Category::showInHome()->get();

        $tags = Tag::showInHome()->get();

        $posts = Post::whereIn('category_id', $categories->pluck('id'))
            ->with('author')
            ->take(4)
            ->approved()
            ->get();

        return sendResponse([
            'posts' => PostResource::collection($posts),
            'tags' => $tags,
            'categories' => $categories
        ]);
    }
}