<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ExpenseRepository;

class UserController extends Controller
{
    public function __construct(
        protected ExpenseRepository $expenseRepository
    ) {
    }

    public function summary(int $id)
    {
        return response()->json([
            'data' => $this->expenseRepository->summary($id)
        ]);
    }
}
