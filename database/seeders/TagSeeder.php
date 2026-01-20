<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder {
   public function run(): void {
      DB::table('tags')->insert([
         [
            'name'         => 'Tag 1',
            'slug'         => 'tag-1',
            'show_in_home' => 'yes',
            'user_id'      => 1,
            'created_at'   => now(),
            'updated_at'   => now()
         ],
         [
            'name'         => 'Tag 2',
            'slug'         => 'tag-2',
            'show_in_home' => 'yes',
            'user_id'      => 1,
            'created_at'   => now(),
            'updated_at'   => now()
         ]
      ]);
   }
}
