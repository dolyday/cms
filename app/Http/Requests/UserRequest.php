<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
      $id = $this->route('user');
      $roles = \Spatie\Permission\Models\Role::pluck('name')->toArray();

      $rules = [
         'name'     => 'required|max:20',
         'email'    => 'required|email|unique:users',
         'role'     => 'required|in:'.implode(',', $roles)
      ];
  
      if (!$id) {
          $rules['email']    = 'required|email|unique:users';
          $rules['password'] = 'required|min:6';
      } else {
          $rules['email']    = 'required|email|unique:users,email,'.$id;
      }
  
      return $rules;
    }

    /**
    * Hook to prepare the request before validation.
    * Checks if the category exists when updating.
    */
    protected function prepareForValidation(): void
    {
        $id = $this->route('user');

        if ($id) {
         $user = \App\Models\User::find($id);

         if (!$user) {
               throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Record not found.');
         }
        }
    }
}
