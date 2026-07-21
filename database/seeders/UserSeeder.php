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
        // Buat Super Admin dulu
        User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'nip' => '000000000000000000',
                'role' => Role::SUPER_ADMIN->status(),
                'bidang' => null,
                'password' => bcrypt('password'),
            ]
        );

        $bidangList = ['Intelijen', 'Pengawasan', 'Umum', 'Perjalanan'];

        foreach ($bidangList as $bidang) {
            $email = strtolower("admin_$bidang@example.com");

            // Buat admin tiap bidang (hanya jika belum ada)
            $admin = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => "Admin $bidang",
                    'nip' => '000000000000000000',
                    'role' => Role::ADMIN->status(),
                    'bidang' => $bidang,
                    'password' => bcrypt('password'),
                ]
            );

            // Buat 2 staff untuk admin ini (hanya jika belum punya staff)
            $existingStaff = User::where('admin_id', $admin->id)->count();
            if ($existingStaff === 0) {
                User::factory(2)->create([
                    'role' => Role::STAFF->status(),
                    'bidang' => $bidang,
                    'admin_id' => $admin->id,
                ]);
            }
        }
    }
}
