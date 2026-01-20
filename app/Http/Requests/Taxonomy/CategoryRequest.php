<?php

namespace App\Http\Requests\Taxonomy;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
      $id = $this->route('category');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories'.($id ? ',slug,'.$id : '')
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'The category already exists.',
        ];
    }

   /**
    * Hook to prepare the request before validation.
    * Checks if the category exists when updating.
    */
    protected function prepareForValidation(): void
    {
        $id = $this->route('category');

        if ($id) {
           $category = \App\Models\Category::find($id);
   
           if (!$category) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Record not found.');
           }
        }

    }
}
