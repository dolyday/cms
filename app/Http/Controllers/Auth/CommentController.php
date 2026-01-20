<?php

namespace App\Http\Controllers\Auth;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Retrieve a paginated list of comments.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getComments(Request $request)
    {
        $perPage = $request->input('perPage', 10);
        $comments = Comment::latest('post_comments.post_id')
            ->leftJoin('posts', 'posts.id', '=', 'post_comments.post_id')
            ->whereHas('post', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->select('post_comments.*');


        $comments = $comments->paginate($perPage);

        return sendResponse([
            'comments' => $comments
        ]);
    }


    /**
     * Change the status of a specific comment.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus(Request $request, string $id)
    {
        $comment = Comment::whereHas('post', function ($query) {
            $query->where('user_id', auth()->id());
        })->find($id);

        if (!$comment) {
            return sendError([
                'message' => 'Record not found.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return sendError([
                'errors' => $validator->errors()
            ]);
        }

        $comment->status = $request->status;
        $comment->save();

        return sendResponse([
            'message' => 'Status changed successfully.'
        ]);
    }
}