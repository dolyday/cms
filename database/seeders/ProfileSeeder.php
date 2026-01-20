<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfileSeeder extends Seeder {
   public function run(): void {
      DB::table('profiles')->insert([
         [
            'mobile'   => '0677889900',
            'whatsapp' => '0677889900',
            'facebook' => 'https://facebook.com',
            'created_at'   => now(),
            'updated_at'   => now()
         ]
      ]);
   }
}
