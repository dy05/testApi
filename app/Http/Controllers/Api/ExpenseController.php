<?php

namespace App\Http\Controllers\Api;

//use App\Enums\Category as CategoryEnum;
use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function __construct(
        protected ExpenseRepository $expenseRepository
    ) {
    }

    public function index(Request $request)
    {
        $dateFormat = 'Y-m-d';

        $request->validate([
            'from' => ['nullable', Rule::date()->format($dateFormat)],
            'to' => ['nullable', Rule::date()->format($dateFormat)],
        ]);

        $from = $request->input('from');
        $to = $request->input('to');

        return response()->json([
            'data' => $this->expenseRepository->find(
                $request->input('user_id'),
                $from,
                $to,
                $request->input('per_page'),
                $request->input('sort_by'),
                $request->input('sort_desc', 1) == 1,
            ),
        ]);
    }

    public function store(Request $request)
    {
//        $allowedCategories = collect(Category::cases())
//            ->pluck('value')
//            ->toArray();

        $dateFormat = 'Y-m-d';

        $request->validate([
            'title' => ['required'],
            'amount' => ['required', 'numeric'],
//            'category' => ['required', 'in:' . join(',', $allowedCategories)],
            'category_id' => ['required', 'exists:categories,id'],
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', Rule::date()->format($dateFormat)],
        ]);

        $date = Carbon::createFromFormat($dateFormat, $request->input('date'));
//        $category = CategoryEnum::from($request->input('category'));
        $category = Category::query()
            ->where('id', '=', $request->input('category_id'))
            ->first();

        try {
            $expense = $this->expenseRepository->store(
                userId: $request->input('user_id'),
                title: $request->input('title'),
                amount: $request->input('amount'),
                category: $category,
                date: $date,
            );

            return response()->json([
                'data' => $expense
            ]);
        } catch (Exception $exc) {
            Log::error(self::class . ' - Error : ' . $exc->getMessage(), [$exc]);

            return response()->json([
                'message' => $exc->getMessage()
            ], 400);
        }
    }

    public function stats()
    {
        return response()->json([
            'data' => $this->expenseRepository->getStats()
        ]);
    }
}
