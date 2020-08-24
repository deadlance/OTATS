<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
         * Permission Types
         *
         */
        $Permissionitems = [
            [
                'name'        => 'Can View Users',
                'slug'        => 'view.users',
                'description' => 'Can view users',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Create Users',
                'slug'        => 'create.users',
                'description' => 'Can create new users',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Edit Users',
                'slug'        => 'edit.users',
                'description' => 'Can edit users',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Delete Users',
                'slug'        => 'delete.users',
                'description' => 'Can delete users',
                'model'       => 'Permission',
            ],

            /*
             * Added by John LeVan 5 August 2020
             *
             * Timesheet related permissions
             */
            [
                'name'        => 'Can Approve Timesheets',
                'slug'        => 'approve.timesheet',
                'description' => 'Can approve timesheets',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Deny Timesheets',
                'slug'        => 'deny.timesheet',
                'description' => 'Can deny timesheets',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can View Timesheets',
                'slug'        => 'view.timesheet',
                'description' => 'Can view timesheets',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Create Timesheets',
                'slug'        => 'create.timesheet',
                'description' => 'Can create timesheets',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Delete Timesheets',
                'slug'        => 'delete.timesheet',
                'description' => 'Can delete timesheets',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Edit Timesheets',
                'slug'        => 'edit.timesheet',
                'description' => 'Can edit timesheets',
                'model'       => 'Permission',
            ],

            /*
             * Employee related permissions
             */
            [
                'name'        => 'Can Assign Employees',
                'slug'        => 'assign.employee',
                'description' => 'Can assign employees',
                'model'       => 'Permission',
            ],
            [
                'name'        => 'Can Unassign Employees',
                'slug'        => 'unassign.employee',
                'description' => 'Can unassign employees',
                'model'       => 'Permission',
            ],


        ];

        /*
         * Add Permission Items
         *
         */
        foreach ($Permissionitems as $Permissionitem) {
            $newPermissionitem = config('roles.models.permission')::where('slug', '=', $Permissionitem['slug'])->first();
            if ($newPermissionitem === null) {
                $newPermissionitem = config('roles.models.permission')::create([
                    'name'          => $Permissionitem['name'],
                    'slug'          => $Permissionitem['slug'],
                    'description'   => $Permissionitem['description'],
                    'model'         => $Permissionitem['model'],
                ]);
            }
        }
    }
}
