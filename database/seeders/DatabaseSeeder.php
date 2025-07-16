<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Expense_categories;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    // database/seeders/DatabaseSeeder.php
public function run()
{
    // ایجاد دسته‌بندی‌های نمونه با جلوگیری از تکراری‌ها
    $categories = [
        'حمل و نقل',
        'ایاب ذهاب',
        'خرید تجهیزات'
    ];

    foreach ($categories as $category) {
        \App\Models\expense_categories::firstOrCreate(['name' => $category]);
    }

    // ایجاد کاربران نمونه
    \App\Models\User::factory(20)->create();
}
}
