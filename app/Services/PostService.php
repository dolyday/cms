<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\PostResource;

class PostService
{
    /**
     * Retrieves a paginated list of posts, optionally filtered by a search term.
     *
     * @param Request $request
     * @return array
     */
    public function allPosts(Request $request): array
    {
        $perPage = $request->input('perPage', 10);
        $posts = Post::latest('id');


        if ($request->filled('search')) {
            $posts->where('title', 'LIKE', "%{$request->search}%");
        }

        $posts = $posts->paginate($perPage);

        return [
            'data' => PostResource::collection($posts),
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
        ];
    }


    /**
     * Create a new post.
     *
     * @param Request $request
     * @return array
     */
    public function createPost(Request $request): array
    {
        $data = $request->all();

        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->image->store('posts', 'public');
        }

        $post = Post::create($data);

        $post->tags()->attach($request->tags);

        return [
            'message' => 'Added successfully.'
        ];
    }


    /**
     * Retrieve a post by ID.
     *
     * @param string $id
     * @return array
     */
    public function getPost(string $id): array
    {
        $post = Post::with(['category', 'tags'])->find($id);

        if (!$post) {
            return [
                'error' => 'Record not found.'
            ];
        }

        return [
            'post' => new PostResource($post)
        ];
    }


    /**
     * Update an existing post.
     *
     * @param Request $request
     * @param string $id
     * @return array
     */
    public function updatePost(Request $request, string $id): array
    {
        $post = Post::find($id);

        if (!$post) {
            return [
                'error' => 'Record not found.'
            ];
        }

        Gate::authorize('update', $post);

        $data = $request->all();

        if ($request->hasFile('image')) {
            $this->deleteImage($post->image);
            $data['image'] = $request->image->store('posts', 'public');
        }

        $post->update($data);
        $post->tags()->sync($request->tags);

        return [
            'message' => 'Updated successfully.'
        ];
    }


    /**
     * Delete an existing post.
     *
     * @param string $id
     * @return array
     */
    public function deletePost(string $id): array
    {
        $post = Post::find($id);

        if (!$post) {
            return [
                'error' => 'Record not found.'
            ];
        }

        Gate::authorize('delete', $post);

        $this->deleteImage($post->image);
        $post->tags()->detach();
        $post->delete();

        return [
            'message' => 'Deleted successfully.'
        ];
    }


    /**
     * Delete an existing image file.
     *
     * @param string $imagePath
     * @return void
     */
    protected function deleteImage(string $imagePath): void
    {
        $imagePath = storage_path('app/public/' . $imagePath);

        if ($imagePath && File::exists($imagePath)) {
            File::delete($imagePath);
        }
    }
}