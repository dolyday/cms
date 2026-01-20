<?php

namespace App\Http\Controllers\Auth;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Services\TaxonomyService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Taxonomy\CategoryRequest;

class CategoryController extends Controller
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
        $this->middleware('permission:add_category', ['only' => 'store']);
        $this->middleware('permission:update_category', ['only' => 'update']);
        $this->middleware('permission:delete_category', ['only' => 'destroy']);

        $this->taxonomyService = $taxonomyService;
        $this->taxonomyService->setModel(Category::class);
    }


    /**
     * Display a listing of the categories.
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
     * Store a newly created category.
     *
     * @param CategoryRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoryRequest $request)
    {
        $result = $this->taxonomyService->create($request);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result, 422);
    }


    /**
     * Display the specified category.
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
     * Update the specified category.
     *
     * @param CategoryRequest $request
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoryRequest $request, string $id)
    {
        $result = $this->taxonomyService->update($request, $id);

        return isset($result['message'])
            ? sendResponse($result)
            : sendError($result);
    }


    /**
     * Remove the specified category.
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