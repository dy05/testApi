<?php

namespace App\Repositories;

use App\Contracts\IBaseRepository;
use App\Enums\Category;
use App\Models\Expense;
use DateTime;
use Illuminate\Support\Collection;

class ExpenseRepository implements IBaseRepository
{
    public function all(): Collection
    {
        return Expense::all();
    }

    public function find(?int $userId = null): Collection
    {
        $query = Expense::query();

        if (!empty($userId)) {
            $query->where('user_id', '=', $userId);
        }

        return $query->get();
    }

    public function store(int $userId, string $title, string $amount, Category $category, DateTime $date): Expense|null
    {
        return Expense::query()
            ->create([
                'user_id' => $userId,
                'title' => $title,
                'amount' => str_replace(',', '.', $amount),
                'category' => $category,
                'date' => $date
            ]);
    }

    public function getStats()
    {
        return Expense::query()
            ->selectRaw('ROUND(SUM(amount), 2) as total_amount, category')
            ->groupBy(['category'])
            ->get();
    }
}
