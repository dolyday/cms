<?php

namespace App\Http\Requests\Taxonomy;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('tag');

        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:tags'.($id ? ',slug,'.$id : '')
        ];
    }

    public function messages(): array
    {
        return [
            'slug.unique' => 'The tag already exists.',
        ];
    }


   /**
    * Hook to prepare the request before validation.
    * Checks if the category exists when updating.
    */
    protected function prepareForValidation(): void
    {
        $id = $this->route('tag');

        if ($id) {
           $tag = \App\Models\Tag::find($id);
   
           if (!$tag) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Record not found.');
           }
        }

    }
}
