<?php

namespace Tests\Feature;

//use App\Enums\Category as CategoryEnum;
use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use App\Repositories\ExpenseRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExpenseTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_api_get_expenses_returns_a_successful_response(): void
    {
        $response = $this->get('/api/expenses');

        $response->assertStatus(200);
    }

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
//            'category',
            'category_id',
            'date'
        ]);
    }

    public function test_date_wrong_format(): void
    {
        $response = $this->post('/api/expenses', [
            'date' => '12/11/2026',
//            'category' => CategoryEnum::OFFICE->value,
            'category_id' => Category::factory()->create()->id,
            'user_id' => 1,
            'amount' => 24,
            'title' => 'New expense'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'date'
        ]);

        $response->assertJsonMissingValidationErrors([
//            'category',
            'category_id',
            'amount',
            'title',
        ]);
    }

    public function test_wrong_category_failed(): void
    {
        $response = $this->post('/api/expenses', [
            'date' => '2026-10-10',
//            'category' => 'value',
            'category_id' => 0,
            'user_id' => 1,
            'amount' => 24,
            'title' => 'New expense'
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
//            'category'
            'category_id'
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
        $category = Category::query()->create(['label' => 'category 1']);

        $response = $this->post('/api/expenses', [
            'date' => '2026-10-10',
//            'category' => Category::OTHER->value,
            'category_id' => $category->id,
            'user_id' => $user->id,
            'amount' => 24,
            'title' => 'New expense'
        ]);


        $response->assertStatus(200);
        $this->assertDatabaseCount(Expense::class, $expensesCount + 1);
    }

    public function test_valid_stats(): void
    {
        $amount1 = 10;
        $amount2 = 34;

        $this->createExpenses($amount1, $amount2);

        $expenseRepository = new ExpenseRepository();
        $data = $expenseRepository->getStats();
        $this->assertTrue($data->where('total_amount', '=', $amount1 + $amount2)->count() > 0);
    }


    public function test_date_filters_on_expenses_list(): void
    {
        $this->createExpenses(date1: '2026-03-14', date2: '2026-06-12');

        $response = $this->get('/api/expenses?' . http_build_query([
            'from' => '2026-06-11'
        ]));

        $response->assertJsonCount(1, 'data.data');

        $response = $this->get('/api/expenses?' . http_build_query([
            'from' => '2026-08-11'
        ]));

        $response->assertJsonCount(0, 'data.data');

        $response = $this->get('/api/expenses?' . http_build_query([
            'to' => '2026-06-11'
        ]));

        $response->assertJsonCount(1, 'data.data');

        $response = $this->get('/api/expenses?' . http_build_query([
            'to' => '2026-02-11'
        ]));

        $response->assertJsonCount(0, 'data.data');

        $response = $this->get('/api/expenses?' . http_build_query([
            'to' => '2026-12-11',
            'from' => '2026-02-11'
        ]));

        $response->assertJsonCount(2, 'data.data');

        $response = $this->get('/api/expenses');

        $response->assertJsonCount(2, 'data.data');
    }


    public function test_user_id_filter_on_expenses_list(): void
    {
        /** @var User $user1 */
        $user1 = User::factory()->create();
        $this->createExpenses(user1: $user1);

        $response = $this->get('/api/expenses');
        $response->assertJsonCount(2, 'data.data');

        $response = $this->get('/api/expenses?' . http_build_query([
            'user_id' => $user1->id,
        ]));

        $response->assertJsonCount(1, 'data.data');

        /** @var User $user2 */
        $user2 = User::factory()->create();
        $response = $this->get('/api/expenses?' . http_build_query([
            'user_id' => $user2->id,
        ]));

        $response->assertJsonCount(0, 'data.data');
    }

    public function createExpenses(int $amount1 = 23, int $amount2 = 34, ?string $date1 = null, ?string $date2 = null, ?User $user1 = null): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::query()->create(['label' => 'category 1']);

        Expense::query()
            ->create([
                'date' => !empty($date1) ? $date1 : '2026-10-10',
//                'category' => CategoryEnum::OTHER->value,
                'category_id' => $category->id,
                'user_id' => $user->id,
                'amount' => $amount1,
                'title' => 'New expense'
            ]);


        Expense::query()
            ->create([
                'date' => !empty($date2) ? $date2 : '2026-10-10',
//                'category' => CategoryEnum::OTHER->value,
                'category_id' => $category->id,
                'user_id' => !empty($user1) ? $user1->id : $user->id,
                'amount' => $amount2,
                'title' => 'New expense'
            ]);
    }
}
