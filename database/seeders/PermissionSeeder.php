<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $accountPermission = Permission::create(['name' => 'manage accounts', 'guard_name' => 'api']);
        // $valuePermission = Permission::create(['name' => 'manage data', 'guard_name' => 'api']);

        // $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'api']);

        // $adminRole->syncPermissions([$accountPermission, $valuePermission]);

        $user = User::create(['email' => 'admin@guerilla360.com', 'password' => bcrypt('GuerillaThree60!'), 'name' => 'Vince']);
        $user->assignRole('admin');
    }
}
