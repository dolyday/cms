<?php

namespace App\Http\Controllers\Auth;

use App\Models\Tag;
use Illuminate\Http\Request;
use App\Services\TaxonomyService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Taxonomy\TagRequest;

class TagController extends Controller
{
    /**
     * @var TaxonomyService
     */
    protected $taxonomyService;

    /**
     * Class constructor.
     *
     * @param TaxonomyService $taxonomyService
     */
    public function __construct(TaxonomyService $taxonomyService)
    {
        $this->middleware('permission:add_tag', ['only' => 'store']);
        $this->middleware('permission:update_tag', ['only' => 'update']);
        $this->middleware('permission:delete_tag', ['only' => 'destroy']);

        $this->taxonomyService = $taxonomyService;
        $this->taxonomyService->setModel(Tag::class);
    }


    /**
     * Display a listing of the tags.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->taxonomyService->all($request);

        return sendResponse($data);
    }


    /**
     * Store a newly created tag.
     *
     * @param TagRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(TagRequest $request)
    {
        $result = $this->taxonomyService->create($request);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 422);
    }


    /**
     * Display the specified tag.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id)
    {
        $result = $this->taxonomyService->get($id);

        return isset($result['error'])
            ? sendError($result, 404)
            : sendResponse($result);
    }

    /**
     * Update the specified tag.
     *
     * @param TagRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(TagRequest $request, string $id)
    {
        $result = $this->taxonomyService->update($request, $id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result);
    }

    /**
     * Remove the specified tag.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        $result = $this->taxonomyService->delete($id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 404);
    }
}