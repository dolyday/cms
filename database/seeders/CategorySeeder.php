<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder {
   public function run(): void {
      DB::table('categories')->insert([
         [
            'name'         => 'First',
            'slug'         => 'first',
            'show_in_home' => 'yes',
            'user_id'      => 1,
            'created_at'   => now(),
            'updated_at'   => now()
         ],
         [
            'name'         => 'Second',
            'slug'         => 'second',
            'show_in_home' => 'yes',
            'user_id'      => 1,
            'created_at'   => now(),
            'updated_at'   => now()
         ]
      ]);
   }
}
