<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('user_roles')->insert(['name' => 'System Admin', 'description' => 'Description',]);
        DB::table('user_roles')->insert(['name' => 'Owner', 'description' => 'System Owner',]);
        DB::table('user_roles')->insert(['name' => 'Admin', 'description' => 'Admin',]);
        DB::table('user_roles')->insert(['name' => 'Editor', 'description' => 'Editor',]);
        DB::table('user_roles')->insert(['name' => 'Viewer', 'description' => 'Viewer',]);
    }
}
