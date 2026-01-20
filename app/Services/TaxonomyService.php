<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class TaxonomyService
{
    /**
     * The model instance used by the service.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Set the model class used for operations.
     *
     * @param string $modelClass
     * @return void
     */
    public function setModel(string $modelClass)
    {
        $this->model = new $modelClass;
    }


    /**
     * Retrieve all records, optionally filtered by search query with pagination.
     *
     * @param Request $request
     * @return array
     */
    public function all(Request $request)
    {

        $perPage = $request->input('perPage', 10);
        $query = $this->model->latest('id');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        $data = $query->showInHome()->paginate($perPage);

        return [
            'data' => $data
        ];
    }


    /**
     * Create a new record.
     *
     * @param Request $request
     * @return array
     */
    public function create(Request $request): array
    {
        $data = $request->all();
        $data['user_id'] = auth()->id();

        $this->model->create($data);

        return [
            'message' => 'Added successfully.'
        ];
    }


    /**
     * Retrieve a single record by ID.
     *
     * @param string $id
     * @return array
     */
    public function get(string $id): array
    {
        $record = $this->model->find($id);

        if (!$record) {
            return [
                'error' => 'Record not found.'
            ];
        }

        return [
            'record' => $record
        ];
    }


    /**
     * Update an existing record.
     *
     * @param Request $request
     * @param string $id
     * @return array
     */
    public function update(Request $request, string $id): array
    {
        $record = $this->model->find($id);

        if (!$record) {
            return [
                'error' => 'Record not found.'
            ];
        }

        Gate::authorize('update', $record);

        $record->update($request->all());

        return [
            'message' => 'Updated successfully.'
        ];
    }


    /**
     * Delete an existing record.
     *
     * @param string $id
     * @return array
     */
    public function delete(string $id): array
    {
        $record = $this->model->find($id);

        if (!$record) {
            return [
                'error' => 'Record not found.'
            ];
        }

        Gate::authorize('delete', $record);

        $record->delete();

        return [
            'message' => 'Deleted successfully.'
        ];
    }
}