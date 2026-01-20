<?php

namespace App\Http\Controllers\Auth;

use App\Models\Draft;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class DraftController extends Controller
{
    /**
     * Store a new draft in the database.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:20',
            'slug' => 'required|unique:drafts',
            'content' => 'required|max:50'
        ], [
            'slug.unique' => 'Draft is already exists.',
        ]);

        if ($validator->passes()) {
            $draft = new Draft();
            $draft->title = $request->title;
            $draft->slug = $request->slug;
            $draft->content = $request->content;
            $draft->user_id = auth()->id();
            $draft->save();

            return sendResponse([
                'message' => 'Added successfully.'
            ]);
        }

        return sendError([
            'errors' => $validator->errors(),
        ]);
    }

    /**
     * Delete an existing draft by ID.
     *
     * @param string  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $draft = Draft::where('user_id', auth()->id())->find($id);

        if (!$draft) {
            return sendError([
                'message' => 'Record not found.'
            ], 404);
        }

        $draft->delete();

        return sendResponse([
            'message' => 'Deleted successfully.'
        ]);
    }
}