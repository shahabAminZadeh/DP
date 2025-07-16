<?php

namespace Database\Factories;

use App\Models\Expense_requests;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExpenseRequestFactory extends Factory
{
    protected $model = Expense_requests::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'category_id' => \App\Models\Expense_requests::factory(),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->numberBetween(10000, 1000000),
            'sheba' => '11' . $this->faker->numerify('########################'),
            'status' => 'pending',
            'attachment_path' => null,
        ];
    }
}
