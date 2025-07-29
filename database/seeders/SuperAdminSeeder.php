<?php

namespace Database\Seeders;

use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate([
            'email' => 'root@mail.com'
        ], [
            'name' => 'Root',
            'password' => bcrypt('password')
        ]);

        // Buat role "superadmin" jika belum ada
        $role = Role::firstOrCreate(['name' => 'superadmin']);

        // Berikan role ke user
        $user->assignRole($role);

        Artisan::call('shield:install admin');
        Artisan::call('shield:generate', ['--all' => true, '--panel' => 'admin']);
        Artisan::call('shield:super-admin', ['--user' => 1, '--panel' => 'admin']);
    }
}
