<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Services\PostService;
use App\Http\Requests\PostRequest;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    /**
     * @var PostService
     */
    protected $postService;


    /**
     * Class constructor.
     *
     * @param PostService $postService
     */
    public function __construct(PostService $postService)
    {
        $this->middleware('permission:add_post', ['only' => 'store']);
        $this->middleware('permission:update_post', ['only' => 'update']);
        $this->middleware('permission:delete_post', ['only' => 'destroy']);

        $this->postService = $postService;
    }

    /**
     * Display a listing of the posts.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $posts = $this->postService->allPosts($request);

        return sendResponse($posts);
    }


    /**
     * Store a newly created post.
     *
     * @param PostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(PostRequest $request)
    {
        $result = $this->postService->createPost($request);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 422);
    }

    /**
     * Display the specified post.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $result = $this->postService->getPost($id);

        return isset($result['error'])
            ? sendError($result, 404)
            : sendResponse($result);
    }


    /**
     * Update the specified post.
     *
     * @param PostRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PostRequest $request, string $id)
    {
        $result = $this->postService->updatePost($request, $id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result);
    }


    /**
     * Remove the specified post.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $result = $this->postService->deletePost($id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 404);
    }
}