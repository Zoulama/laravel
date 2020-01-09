<?php

use Illuminate\Database\Seeder;
use App\User;
use App\Role;

class UserTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->delete();

        # aaZZ!!55
        $password = '$2y$10$qWxLODPpUJOC4wHPNMcFyez0Fg1ctQLnO7EMqdWrzl4xR0MNa4IHu';
        # les rôles
        $roleAdmin = Role::getByLabel(Role::$ROLE['ADMIN']);
        $roleUser = Role::getByLabel(Role::$ROLE['USER']);

        # l'admin
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@sbeeh.fr',
            'password' => $password,
        ]);
        # puis son rôle
        // $admin->roles()->attach($roleAdmin);
        $roleAdmin->users()->attach($admin);

        # d'autres utilisateurs
        $names = array(
            'Dominique',
            'Rozenn',
            'Fabrice',
            'Sylvain',
            'Alan',
        );

        foreach ($names as $name) {
            $user = User::create([
                'name' => $name,
                'email' => strtolower($name) . '@sbeeh.fr',
                'password' => $password,
            ]);
            # puis son rôle
            // $user->roles()->attach($roleUser);
            $roleUser->users()->attach($user);
        }
    }
}