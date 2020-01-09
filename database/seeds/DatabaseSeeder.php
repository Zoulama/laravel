<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('RoleTableSeeder');
        $this->command->info('Role table seeded!');

        $this->call('UserTableSeeder');
        $this->command->info('User table seeded!');

        $this->call('HiveTableSeeder');
        $this->command->info('Hive table seeded!');

        $this->call('ReportTableSeeder');
        $this->command->info('Report table seeded!');
    }
}
