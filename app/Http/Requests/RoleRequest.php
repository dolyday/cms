<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
      $id = $this->route('role');
      $permissions = \Spatie\Permission\Models\Permission::pluck('name')->toArray();

      $rules = [
         'permissions'   => 'required|array',
         'permissions.*' => 'in:'.implode(',', $permissions)
      ];

      if (!$id) {
         $rules['name']    = 'required|unique:roles';
     } else {
         $rules['name']    = 'required|unique:roles,name,'.$id;
     }

      return $rules;
    }

   /**
    * Hook to prepare the request before validation.
    * Checks if the category exists when updating.
    */
    protected function prepareForValidation(): void
    {
        $id = $this->route('role');

        if ($id) {
         $role = \Spatie\Permission\Models\Role::find($id);

         if (!$role) {
               throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException('Record not found.');
         }
        }
    }
}
