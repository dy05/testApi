<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\ExpenseRepository;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        protected ExpenseRepository $expenseRepository
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::all(),
        ]);
    }

    public function summary(int $id): JsonResponse
    {
        return response()->json([
            'data' => $this->expenseRepository->summary($id)
        ]);
    }
}
