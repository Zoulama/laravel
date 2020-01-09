<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('roles')->delete();

        foreach (Role::$ROLE as $role => $label) {
            Role::create([
                'label' => $label,
            ]);
        }
    }
}