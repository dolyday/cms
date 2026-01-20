<?php

namespace App\Http\Controllers\Auth;

use App\Models\Post;
use App\Models\Draft;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Resources\PostResource;
use App\Http\Controllers\Controller;


class DashboardController extends Controller
{
    /**
     * Get dashboard summary data for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    function index()
    {
        $drafts = Draft::where('user_id', auth()->id())->latest()->get();
        $last_post = Post::where('user_id', auth()->id())->orderBy('id', 'desc')->first();

        $last_comment = Comment::with('post')
            ->whereHas('post', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->orderBy('id', 'desc')
            ->first();

        return sendResponse([
            'drafts' => $drafts,
            'last_comment' => $last_comment,
            'last_post' => new PostResource($last_post)
        ]);
    }


    /**
     * Logout the authenticated user out by deleting their current access token.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return sendResponse([
            'message' => 'Logout successfully.'
        ]);
    }
}