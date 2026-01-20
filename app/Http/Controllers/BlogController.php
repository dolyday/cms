<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Validator;

class BlogController extends Controller
{
    /**
     * Display a list of approved posts that belong to visible categories.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $categories = Category::showInHome()->get();

        $posts = Post::whereIn('category_id', $categories->pluck('id'))
            ->with('category')
            ->approved();

        if ($request->has('search')) {
            $search = $request->get('search');
            $posts = $posts->where('title', 'LIKE', "%{$search}%");
        }

        $posts = $posts->paginate($perPage);

        return sendResponse([
            'posts' => PostResource::collection($posts),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'links' => [
                    'next' => $posts->nextPageUrl(),
                    'previous' => $posts->previousPageUrl()
                ]
            ]
        ]);
    }


    /**
     * Show a single post with its related data based on slug.
     *
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function details(string $slug)
    {
        $tags = Tag::showInHome()->get();
        $authors = User::has('posts')->get();
        $categories = Category::showInHome()->get();

        $post = Post::where('slug', $slug)
            ->with(['category', 'author', 'tags', 'comments'])
            ->approved()
            ->first();

        if ($post) {
            $related_posts = Post::where('category_id', $post->category->id)
                ->where('id', '!=', $post->id)
                ->approved()
                ->get();

            return sendResponse([
                'post' => new PostResource($post),
                'tags' => $tags,
                'authors' => $authors,
                'categories' => $categories,
                'related_posts' => PostResource::collection($related_posts)
            ]);
        }

        return sendError([
            'error' => 'Record not found.'
        ], 404);
    }


    /**
     * List of posts by category slug.
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function category(Request $request, string $slug)
    {
        $perPage = $request->input('perPage', 10);
        $category = Category::showInHome()
            ->where('slug', $slug)
            ->first();

        if ($category) {
            $posts = Post::where('category_id', $category->id)
                ->with(['category', 'tags', 'author'])
                ->approved();

            if ($request->has('search')) {
                $search = $request->get('search');
                $posts = $posts->where('title', 'LIKE', "%{$search}%");
            }

            $posts = $posts->paginate($perPage);

            return sendResponse([
                'posts' => PostResource::collection($posts),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'links' => [
                        'next' => $posts->nextPageUrl(),
                        'previous' => $posts->previousPageUrl()
                    ]
                ]
            ]);
        }

        return sendError([
            'error' => 'Record not found.'
        ], 404);
    }


    /**
     * List of posts by tag slug.
     *
     * @param Request $request
     * @param string $slug
     * @return \Illuminate\Http\JsonResponse
     */
    public function tag(Request $request, string $slug)
    {
        $perPage = $request->input('perPage', 10);
        $tag = Tag::showInHome()->where('slug', $slug)->first();

        if ($tag) {
            $posts = $tag->posts()
                ->approved()
                ->with(['category', 'tags', 'author']);

            if ($request->has('search')) {
                $search = $request->get('search');
                $posts = $posts->where('title', 'LIKE', "%{$search}%");
            }

            $posts = $posts->paginate($perPage);

            return sendResponse([
                'posts' => PostResource::collection($posts),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'links' => [
                        'next' => $posts->nextPageUrl(),
                        'previous' => $posts->previousPageUrl()
                    ]
                ]
            ]);
        }

        return sendError([
            'error' => 'Record not found'
        ], 404);
    }


    /**
     * List of posts by author name.
     *
     * @param Request $request
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function author(Request $request, $name)
    {
        $perPage = $request->input('perPage', 10);
        $author = User::where('name', $name)->first();

        if ($author) {
            $posts = Post::where('user_id', $author->id)
                ->with(['category', 'tags', 'author'])
                ->approved();

            if ($request->has('search')) {
                $search = $request->get('search');
                $posts = $posts->where('title', 'LIKE', "%{$search}%");
            }

            $posts = $posts->paginate($perPage);

            return sendResponse([
                'posts' => PostResource::collection($posts),
                'meta' => [
                    'current_page' => $posts->currentPage(),
                    'last_page' => $posts->lastPage(),
                    'total' => $posts->total(),
                    'per_page' => $posts->perPage(),
                    'links' => [
                        'next' => $posts->nextPageUrl(),
                        'previous' => $posts->previousPageUrl()
                    ]
                ]
            ]);
        }

        return sendError([
            'error' => 'Record not found.'
        ], 404);
    }


    /**
     * Save a comment or reply to a blog post.
     *
     * @param Request $request
     * @param int $postID The ID of the post being commented on.
     * @param int|null $parentID (Optional) ID of the parent comment.
     * @param int|null $replyID (Optional) ID of the comment being replied to.
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveComment(Request $request, int $postID, int $parentID = null, int $replyID = null)
    {
        $post = Post::find($postID);

        if (!$post) {
            return sendError([
                'error' => 'Post not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:20',
            'body' => 'required|min:10|max:50',
            'email' => 'required|email|max:20'
        ]);

        if ($parentID) {
            $row_exists = Comment::where('post_id', $postID)
                ->where('id', $parentID)
                ->exists();


            if (!$row_exists) {
                return sendError([
                    'error' => 'The comment you want to reply to does not exist.'
                ], 400);
            }
        }

        if ($replyID) {
            $row_exists = Comment::where('post_id', $postID)
                ->where('parent_id', $parentID)
                ->where('id', $replyID)
                ->exists();


            if (!$row_exists) {
                return sendError([
                    'error' => 'The comment you want to reply to does not exist.'
                ], 400);
            }
        }

        if ($validator->passes()) {
            $post_comment = new Comment();
            $post_comment->name = $request->name;
            $post_comment->email = $request->email;
            $post_comment->body = $request->body;
            $post_comment->post_id = $postID;
            $post_comment->parent_id = $parentID;
            $post_comment->reply_id = $replyID;
            $post_comment->save();

            return sendResponse([
                'message' => 'Thank you for your comments.'
            ]);
        }

        return sendError([
            'errors' => $validator->errors()
        ]);
    }
}