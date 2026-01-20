<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('post');

        $rules = [
         'title'       => 'required|string|max:50',
         'intro'       => 'required|string|max:100',
         'content'     => 'required|string|max:300',
         'tags'        => 'required|array|min:1',
         'tags.*'      => 'required|integer|exists:tags,id',
         'status'      => 'in:draft,published',
         'category_id' => 'required|exists:categories,id'
        ];
    
        if (!$id) {
            $rules['slug']  = 'required|unique:posts';
            $rules['image'] = 'required|image|mimes:png,jpg,svg|max:2048';
        } else {
            $rules['slug']  = 'required|unique:posts,slug,'.$id;
            $rules['image'] = 'nullable|image|mimes:png,jpg,svg|max:2048';
        }
    
        return $rules;
    }

    public function messages(): array
    {
        return [
            'slug.unique'   => 'The post already exists.',
            'tags.required' => 'You must select at least one tag.'
        ];
    }

   /**
    * Hook to prepare the request before validation.
    * Checks if the category exists when updating.
    */
    protected function prepareForValidation(): void
    {
        $id = $this->route('post');

        if ($id) {
         $post = \App\Models\Post::find($id);

         if (!$post) {
               throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Record not found.');
         }
        }
    }
}
