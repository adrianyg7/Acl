<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AclDatabaseSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // Users
        $users = [
            [
                'first_name' => 'Su',
                'last_name' => 'Acl',
                'email' => 'su@acl.com',
                'password' => bcrypt('123'),
                'superuser' => TRUE,
                'remember_token' => str_random(10),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        DB::table('users')->insert($users);

        // Roles
        $roles = [
            [
                'name' => 'Admin',
                'description' => 'Admin',
            ],
        ];

        DB::table('roles')->insert($roles);
    }

}