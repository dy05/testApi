<?php

namespace App\Repositories;

//use App\Enums\Category as CategoryEnum;
use App\Models\Category;
use App\Models\Expense;
use DateTime;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ExpenseRepository
{
    public function find(?int $userId = null, ?string $startDate = null, ?string $endDate = null, ?int $perPage = 10, ?string $sortBy = null, bool $sortDesc = true): LengthAwarePaginator
    {
        $query = Expense::query()
            ->withAggregate('category', 'label');

        if (!empty($userId)) {
            $query->where('user_id', '=', $userId);
        }

        if (!empty($startDate)) {
            $query->where('date', '>=', $startDate);
        }

        if (!empty($endDate)) {
            $query->where('date', '<=', $endDate);
        }

        if (!empty($sortBy) && in_array($sortBy, ['date', 'title', 'category', 'amount'])) {
            if ($sortBy === 'category') {
                $sortBy = 'category_label';
            }

            $query->orderBy($sortBy, $sortDesc ? 'desc' : 'asc');
        }

        return $query->paginate($perPage);
    }

    public function store(int $userId, string $title, string $amount, Category $category, DateTime $date): Expense|null
    {
        return Expense::query()
            ->create([
                'user_id' => $userId,
                'title' => $title,
                'amount' => str_replace(',', '.', $amount),
//                'category' => $category,
                'category_id' => $category->id,
                'date' => $date
            ]);
    }

    public function getStats(): Collection
    {
        return Expense::query()
            ->selectRaw('ROUND(SUM(amount), 2) as total_amount, category')
//            ->groupBy(['category'])
            ->groupBy(['category_id'])
            ->get();
    }

    public function summary(?int $id): Collection
    {
        if (empty($id)) {
            return collect();
        }

        return Expense::query()
            ->where('user_id', '=', $id)
            ->selectRaw('ROUND(SUM(amount), 2) as total_amount, ROUND(AVG(amount), 0) average, category')
//            ->groupBy(['category'])
            ->groupBy(['category_id'])
            ->get();
    }
}
