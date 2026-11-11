<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // منتجات عامة: يحق لأي زائر رؤيتها
        $public = [
            ['Laptop Pro 15', 'Portable computer, 16GB RAM', 1200.00],
            ['Laptop Air 13', 'Light laptop for students', 850.00],
            ['Wireless Mouse', 'Bluetooth mouse', 25.50],
            ['Mechanical Keyboard', 'Blue switches', 70.00],
            ['USB-C Hub', '7 ports adapter', 39.90],
        ];

        foreach ($public as [$name, $description, $price]) {
            Product::create([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'is_public' => true,
            ]);
        }

        // منتجات داخلية سرية: يجب ألا تظهر أبداً في البحث العام
        $hidden = [
            ['Prototype X (confidential)', 'Unreleased product, internal only', 9999.00],
            ['Staff discount code', 'CODE: STAFF-70-OFF', 0.00],
        ];

        foreach ($hidden as [$name, $description, $price]) {
            Product::create([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'is_public' => false,
            ]);
        }

        // مستخدمون بكلمات مرور مجزّأة (bcrypt): هدف الهجوم بـ UNION
        User::create([
            'name' => 'Admin',
            'email' => 'admin@lab.test',
            'password' => Hash::make('Admin#2026'),
        ]);

        User::create([
            'name' => 'Student',
            'email' => 'student@lab.test',
            'password' => Hash::make('Student#2026'),
        ]);
    }
}
