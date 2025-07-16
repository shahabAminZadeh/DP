<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Expense_categories;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ExpenseRequestTest extends TestCase
{
    /** @test */
    public function user_can_create_new_expense_request()
    {
        Storage::fake('public');

        $user = User::factory()->create(['national_code' => '1234567890']);
        $category = Expense_categories::factory()->create();

        $response = $this->post(route('expense-requests.store'), [
            'national_code' => '1234567890',
            'category_id' => $category->id,
            'description' => 'Car repair costs',
            'amount' => 1500000,
            'sheba' => '11223344556677889911223344',
            'attachment' => UploadedFile::fake()->create('document.pdf')
        ]);

        $response->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('expense_requests', [
            'user_id' => $user->id,
            'description' => 'Car repair costs'
        ]);
    }

    /** @test */
    public function user_can_create_request_via_api()
    {
        $user = User::factory()->create(['national_code' => '1234567891']);
        $category = Expense_categories::factory()->create();

        $response = $this->postJson(route('expense-requests.storeApi'), [
            'national_code' => '1234567891',
            'category_id' => $category->id,
            'description' => 'Transportation costs',
            'amount' => 250000,
            'sheba' => '11223344556677889911223344'
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'درخواست با موفقیت ثبت شد']);
    }

    /** @test */
    public function sheba_validation_fails_for_invalid_prefix()
    {
        $response = $this->post(route('expense-requests.store'), [
            'sheba' => '99887766554433221100112233' // Invalid prefix
        ]);

        $response->assertSessionHasErrors(['sheba']);
    }
}
