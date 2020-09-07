<?php

use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
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
        $Statuses = [
            [
                'name'        => 'New',
                'slug'        => 'new',
                'description' => 'New Timesheet',
                'sort_order'  => 0,
                'editable'    => 1,
                'submittable' => 1,
            ],
            [
                'name'        => 'Pending',
                'slug'        => 'pending',
                'description' => 'Timesheet Pending Approval',
                'sort_order'  => 1,
                'editable'    => 0,
                'submittable' => 0,
            ],
            [
                'name'        => 'Returned',
                'slug'        => 'returned',
                'description' => 'Timesheet Returned',
                'sort_order'  => 2,
                'editable'    => 1,
                'submittable' => 1,
            ],
            [
                'name'        => 'Denied',
                'slug'        => 'denied',
                'description' => 'Timesheet Denied',
                'sort_order'  => 3,
                'editable'    => 0,
                'submittable' => 0,
            ],
            [
                'name'        => 'Approved',
                'slug'        => 'approved',
                'description' => 'Timesheet Approved',
                'sort_order'  => 4,
                'editable'    => 0,
                'submittable' => 0,
            ],
        ];

        /*
         * Add Role Items
         *
         */
        foreach ($Statuses as $Status) {
            DB::table('statuses')->insert([
                'name' => $Status['name'],
                'slug' => $Status['slug'],
                'description' => $Status['description'],
                'sort_order' => $Status['sort_order'],
                'editable' => $Status['editable'],
                'submittable' => $Status['submittable'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
