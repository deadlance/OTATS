<?php

use Illuminate\Database\Seeder;

class HourtypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Role Types
         *
         */
        $Hourtypes = [
            [
                'name'        => 'PTO',
                'slug'        => 'pto',
                'description' => 'Paid Time Off',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Vacation',
                'slug'        => 'vacation',
                'description' => 'Vacation Hours',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Sick Leave',
                'slug'        => 'sickleave',
                'description' => 'Sick Leave',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'Comp Time',
                'slug'        => 'comptime',
                'description' => 'Comp Time',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],

        ];

        /*
         * Add Hourtypes Items
         *
         */
        foreach ($Hourtypes as $Hourtype) {
            DB::table('hourtype')->insert([
                'name' => $Hourtype['name'],
                'slug' => $Hourtype['slug'],
                'description' => $Hourtype['description'],
                'created_at' => $Hourtype['created_at'],
                'updated_at' => $Hourtype['updated_at'],
            ]);
        }
    }
}
