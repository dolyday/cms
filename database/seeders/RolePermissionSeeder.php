<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder {
   public function run(): void {
      $authorities = config('permission.authorities');

      $list_of_permissions = [];
      $admin_permissions = [];
      $editor_permissions = [];
      $author_permissions = [];

      foreach ($authorities as $label => $permissions) {
         foreach ($permissions as $permission) {
            $list_of_permissions[] = [
               'name'       => $permission,
               'guard_name' => 'web',
               'created_at' => now(),
               'updated_at' => now()
            ];
            
            /**
             * Admin permissions
            */
            $admin_permissions[] = $permission;
   
            /**
             * Editor permissions
            */
            if (in_array($label, ['Manage Posts', 'Manage Categories', 'Manage Tags'])) {
               $editor_permissions[] = $permission;
            }

            /**
             * Author permissions
            */
            if (in_array($label, ['Manage Posts'])) {
               $author_permissions[] = $permission;
            }
         }
      }

      /**
       * Insert permissions
       */
      Permission::insert($list_of_permissions);

      /**
       * Create admin role
       */
      $admin = Role::create([
         'name'       => 'admin',
         'guard_name' => 'web',
         'created_at' => now(),
         'updated_at' => now()
      ]);

      /**
       * Create editor role
       */
      $editor = Role::create([
         'name'       => 'editor',
         'guard_name' => 'web',
         'created_at' => now(),
         'updated_at' => now()
      ]);

      /**
       * Create author role
       */
      $author = Role::create([
         'name'       => 'author',
         'guard_name' => 'web',
         'created_at' => now(),
         'updated_at' => now()
      ]);


      /**
       * Assign permissions to roles
       */
      $admin->givePermissionTo($admin_permissions);
      $editor->givePermissionTo($editor_permissions);
      $author->givePermissionTo($author_permissions);

      /**
       * Assign user (id:1) to admin role
       */
      User::find(1)->assignRole('admin');
   }
}
