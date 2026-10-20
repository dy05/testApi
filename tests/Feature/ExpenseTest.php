<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Enums\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_api_get_expenses_returns_a_successful_response(): void
    {
        $response = $this->get('/api/expenses');

        $response->assertStatus(200);
    }

    /**
     * A basic test example.
     */
    public function test_validation_failed_if_wrong_data(): void
    {
        $response = $this->post('/api/expenses', [
            'user_id' => 0,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'user_id',
            'title',
            'amount',
            'date',
            'category'
        ]);
    }

    public function test_date_wrong_format(): void
    {
        $response = $this->post('/api/expenses', [
            'date' => '12/11/2026',
            'category' => Category::OFFICE->value,
            'user_id' => 1,
            'amount' => 24,
            'title' => 'New expense'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'date'
        ]);

        $response->assertJsonMissingValidationErrors([
            'category',
            'amount',
            'title',
        ]);
    }

    public function test_wrong_category_failed(): void
    {
        $response = $this->post('/api/expenses', [
            'date' => '2026-10-10',
            'category' => 'value',
            'user_id' => 1,
            'amount' => 24,
            'title' => 'New expense'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'category'
        ]);

        $response->assertJsonMissingValidationErrors([
            'date',
            'amount',
            'title',
        ]);
    }

    public function test_create_category_successfully(): void
    {
        $expensesCount = Expense::query()->count();
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->post('/api/expenses', [
            'date' => '2026-10-10',
            'category' => Category::OTHER->value,
            'user_id' => $user->id,
            'amount' => 24,
            'title' => 'New expense'
        ]);


        $response->assertStatus(200);
        $this->assertDatabaseCount(Expense::class, $expensesCount + 1);
    }
}
