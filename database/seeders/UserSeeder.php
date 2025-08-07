<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bidangList = ['Intelijen', 'Pengawasan', 'Umum', 'Perjalanan'];

        foreach ($bidangList as $bidang) {
            // Pakai factory untuk admin tiap bidang
            $admin = User::factory()->create([
                'name' => "Admin $bidang",
                'email' => strtolower("admin_$bidang@example.com"),
                'role' => Role::ADMIN->status(),
                'bidang' => $bidang,
            ]);

            // Pakai factory juga untuk 2 staff, disambungkan ke admin di atas
            User::factory(2)->create([
                'role' => Role::STAFF->status(),
                'bidang' => $bidang,
                'admin_id' => $admin->id,
            ]);
        }

        // Tambah super admin
        User::factory(2)->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'role' => Role::SUPER_ADMIN->status(),
            'bidang' => null,
        ]);
    }
}
